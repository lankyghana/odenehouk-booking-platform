<?php

namespace App\Services;

use App\Enums\PaymentState;
use App\Jobs\SendBookingConfirmationEmailJob;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\StripeEvent;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Refund;
use Stripe\Customer;
use Stripe\Webhook;
use Stripe\Balance;
use Stripe\Event as StripeEventObject;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class StripeService
{
    public function __construct(
        private readonly PaymentTransitionService $paymentTransitionService,
    ) {
        Stripe::setApiKey((string) config('services.stripe.secret'));
        Stripe::setApiVersion((string) config('services.stripe.version'));
        Stripe::setAppInfo('booking-platform', '2.0.0');
    }

    /**
     * Create a payment intent for booking
     */
    public function createPaymentIntent(Booking $booking): PaymentIntent
    {
        try {
            $amount = (int) round($booking->total_amount * 100);
            $customerId = $this->ensureCustomer($booking);

            $paymentIntent = PaymentIntent::create([
                'amount' => $amount,
                'currency' => 'usd',
                'customer' => $customerId,
                'automatic_payment_methods' => ['enabled' => true],
                'metadata' => [
                    'booking_id' => $booking->id,
                    'customer_email' => $booking->customer_email,
                    'customer_name' => $booking->customer_name,
                    'offer_title' => optional($booking->offer)->title ?? 'N/A',
                ],
                'description' => "Booking: " . (optional($booking->offer)->title ?? 'Booking'),
                'receipt_email' => $booking->customer_email ?: null,
            ], [
                'idempotency_key' => "pi_booking_{$booking->id}"
            ]);

            return $paymentIntent;
        } catch (\Exception $e) {
            Log::error('payment.intent_create_failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function processWebhookEvent(array $event): void
    {
        $eventId = $event['id'] ?? null;
        $eventType = $event['type'] ?? null;
        $paymentIntentId = $event['data']['object']['id'] ?? null;

        if (!$eventId || !$eventType) {
            throw new RuntimeException('Invalid Stripe event payload');
        }

        try {
            DB::transaction(function () use ($eventId, $eventType, $paymentIntentId, $event): void {
                $stripeEvent = StripeEvent::query()
                    ->where('stripe_event_id', $eventId)
                    ->lockForUpdate()
                    ->first();

                if ($stripeEvent && $stripeEvent->processed_at) {
                    Log::info('payment.webhook_duplicate_ignored', [
                        'stripe_event_id' => $eventId,
                        'event_type' => $eventType,
                    ]);
                    return;
                }

                $stripeEvent ??= StripeEvent::create([
                    'stripe_event_id' => $eventId,
                    'event_type' => $eventType,
                    'payment_intent_id' => $paymentIntentId,
                    'status' => 'received',
                    'payload' => $event,
                ]);

                match ($eventType) {
                    'payment_intent.processing' => $this->handlePaymentProcessing($paymentIntentId, $eventId),
                    'payment_intent.succeeded' => $this->handleSuccessfulPayment($paymentIntentId, $eventId),
                    'payment_intent.payment_failed' => $this->handleFailedPayment($paymentIntentId, $eventId),
                    'charge.refunded' => $this->handleChargeRefunded($event, $eventId),
                    default => $this->markIgnored($stripeEvent, $eventType),
                };

                $stripeEvent->status = 'processed';
                $stripeEvent->processed_at = now();
                $stripeEvent->save();
            });
        } catch (Throwable $e) {
            Log::error('payment.webhook_processing_failed', [
                'stripe_event_id' => $eventId,
                'event_type' => $eventType,
                'payment_intent_id' => $paymentIntentId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function handlePaymentProcessing(?string $paymentIntentId, ?string $stripeEventId = null): void
    {
        if (!$paymentIntentId) {
            return;
        }

        $payment = Payment::where('stripe_payment_intent_id', $paymentIntentId)->first();
        if (!$payment) {
            return;
        }

        $this->paymentTransitionService->transition($payment, PaymentState::PROCESSING, [
            'stripe_event_id' => $stripeEventId,
        ]);
    }

    public function handleSuccessfulPayment(string $paymentIntentId, ?string $stripeEventId = null): void
    {
        try {
            $payment = Payment::where('stripe_payment_intent_id', $paymentIntentId)->firstOrFail();

            // Idempotency guard: prevent duplicate processing
            if ($payment->isSuccessful()) {
                Log::info('payment.success_already_processed', [
                    'payment_id' => $payment->id,
                    'payment_intent_id' => $paymentIntentId,
                    'stripe_event_id' => $stripeEventId,
                ]);
                return;
            }

            // Also prevent re-processing if already refunded
            if (($payment->status?->value ?? (string) $payment->status) === PaymentState::REFUNDED->value) {
                Log::info('payment.already_refunded_skip', [
                    'payment_id' => $payment->id,
                    'payment_intent_id' => $paymentIntentId,
                    'stripe_event_id' => $stripeEventId,
                ]);
                return;
            }

            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

            $charge = $paymentIntent->charges->data[0] ?? null;
            if (!$charge) {
                Log::warning('payment.charge_missing_on_success', [
                    'payment_id' => $payment->id,
                    'booking_id' => $payment->booking_id,
                    'payment_intent_id' => $paymentIntentId,
                    'stripe_event_id' => $stripeEventId,
                ]);
            }

            $payment->update([
                'stripe_charge_id' => $charge->id ?? ($paymentIntent->latest_charge ?? null),
                'payment_method' => $paymentIntent->payment_method ?? ($paymentIntent->payment_method_types[0] ?? null),
            ]);

            $this->paymentTransitionService->transition($payment, PaymentState::SUCCEEDED, [
                'stripe_event_id' => $stripeEventId,
            ]);

            SendBookingConfirmationEmailJob::dispatch($payment->booking_id);

            Log::info('payment.succeeded', [
                'booking_id' => $payment->booking_id,
                'payment_id' => $payment->id,
                'payment_intent_id' => $paymentIntentId,
                'stripe_event_id' => $stripeEventId,
            ]);
        } catch (\Exception $e) {
            Log::error('payment.success_handler_failed', [
                'payment_intent_id' => $paymentIntentId,
                'stripe_event_id' => $stripeEventId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Handle failed payment webhook
     */
    public function handleFailedPayment(?string $paymentIntentId, ?string $stripeEventId = null): void
    {
        if (!$paymentIntentId) {
            return;
        }

        try {
            $payment = Payment::where('stripe_payment_intent_id', $paymentIntentId)->first();
            
            if (!$payment) {
                Log::warning('payment.failed_not_found', [
                    'payment_intent_id' => $paymentIntentId,
                    'stripe_event_id' => $stripeEventId,
                ]);
                return;
            }

            if (($payment->status?->value ?? (string) $payment->status) === PaymentState::SUCCEEDED->value) {
                return;
            }

            $this->paymentTransitionService->transition($payment, PaymentState::FAILED, [
                'stripe_event_id' => $stripeEventId,
            ]);

            Log::warning('payment.failed', [
                'payment_id' => $payment->id,
                'booking_id' => $payment->booking_id,
                'payment_intent_id' => $paymentIntentId,
                'stripe_event_id' => $stripeEventId,
            ]);
        } catch (\Exception $e) {
            Log::error('payment.failure_handler_failed', [
                'payment_intent_id' => $paymentIntentId,
                'stripe_event_id' => $stripeEventId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function handleChargeRefunded(array $event, ?string $stripeEventId = null): void
    {
        $paymentIntentId = $event['data']['object']['payment_intent'] ?? null;
        if (!$paymentIntentId) {
            return;
        }

        $payment = Payment::where('stripe_payment_intent_id', $paymentIntentId)->first();
        if (!$payment) {
            return;
        }

        $this->paymentTransitionService->transition($payment, PaymentState::REFUNDED, [
            'stripe_event_id' => $stripeEventId,
        ]);
    }

    private function markIgnored(StripeEvent $stripeEvent, string $eventType): void
    {
        $stripeEvent->status = 'ignored';
        $stripeEvent->processed_at = now();
        $stripeEvent->save();

        Log::info('payment.webhook_event_ignored', [
            'stripe_event_id' => $stripeEvent->stripe_event_id,
            'event_type' => $eventType,
        ]);
    }

    /**
     * Refund a payment
     */
    public function refundPayment(Payment $payment, ?float $amount = null): Refund
    {
        try {
            $status = $payment->status?->value ?? (string) $payment->status;
            if ($status !== PaymentState::SUCCEEDED->value) {
                throw new \RuntimeException('Only succeeded payments can be refunded');
            }

            $remaining = (float) $payment->amount - (float) $payment->refunded_amount;
            if ($remaining <= 0) {
                throw new \RuntimeException('Payment already fully refunded');
            }

            $refundAmount = (float) ($amount ?? $remaining);
            if ($refundAmount <= 0) {
                throw new \RuntimeException('Refund amount must be positive');
            }

            if ($refundAmount > $remaining) {
                $refundAmount = $remaining;
            }

            $refundAmountCents = (int) round($refundAmount * 100);

            $refund = Refund::create([
                'payment_intent' => $payment->stripe_payment_intent_id,
                'amount' => $refundAmountCents,
            ], [
                'idempotency_key' => "refund_{$payment->id}_{$refundAmountCents}"
            ]);

            $payment->update([
                'status' => $refundAmount >= (float) $payment->amount ? PaymentState::REFUNDED->value : $status,
                'refunded_amount' => $payment->refunded_amount + $refundAmount,
                'refunded_at' => $refundAmount >= (float) $payment->amount ? now() : $payment->refunded_at,
            ]);

            if ($refundAmount >= (float) $payment->amount) {
                $this->paymentTransitionService->transition($payment->fresh(), PaymentState::REFUNDED, [
                    'payment_intent_id' => $payment->stripe_payment_intent_id,
                ]);
            }

            Log::info('payment.refund_processed', [
                'payment_id' => $payment->id,
                'booking_id' => $payment->booking_id,
                'payment_intent_id' => $payment->stripe_payment_intent_id,
                'amount' => $refundAmount,
            ]);

            return $refund;
        } catch (\Exception $e) {
            Log::error('payment.refund_failed', [
                'payment_id' => $payment->id ?? null,
                'payment_intent_id' => $payment->stripe_payment_intent_id ?? null,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Retrieve payment intent
     */
    public function retrievePaymentIntent(string $paymentIntentId): PaymentIntent
    {
        return PaymentIntent::retrieve($paymentIntentId);
    }

    /**
     * Verify webhook signature
     */
    public function verifyWebhookSignature($payload, $signature)
    {
        $secret = config('services.stripe.webhook_secret');
        if (empty($secret)) {
            throw new \RuntimeException('Stripe webhook secret not configured');
        }

        return Webhook::constructEvent(
            $payload,
            $signature,
            $secret
        );
    }

    public function checkConnectivity(): bool
    {
        Balance::retrieve();
        return true;
    }

    /**
     * Ensure a Stripe customer exists for this booking's user
     */
    protected function ensureCustomer(Booking $booking): ?string
    {
        $user = $booking->user;

        if (!$user || !$user->email) {
            return null;
        }

        if ($user->stripe_customer_id ?? null) {
            return $user->stripe_customer_id;
        }

        $customer = Customer::create([
            'email' => $user->email,
            'name' => $user->name,
            'phone' => $user->phone,
            'metadata' => [
                'user_id' => $user->id,
            ],
        ]);

        $user->update(['stripe_customer_id' => $customer->id]);

        return $customer->id;
    }
}
