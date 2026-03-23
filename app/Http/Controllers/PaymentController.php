<?php

namespace App\Http\Controllers;

use App\Enums\PaymentState;
use App\Jobs\ProcessStripeWebhookJob;
use App\Models\Payment;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class PaymentController extends Controller
{
    protected StripeService $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Handle Stripe webhook
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $signature = (string) $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        try {
            $event = $this->stripeService->verifyWebhookSignature($payload, $signature);
        } catch (Throwable $e) {
            Log::error('Webhook signature failed', [
                'error' => $e->getMessage(),
            ]);

            // Still return 200 to prevent Stripe retries on invalid signature
            return response()->json(['error' => 'Invalid signature'], Response::HTTP_OK);
        }

        try {
            ProcessStripeWebhookJob::dispatch($event->toArray());

            Log::info('payment.webhook_received', [
                'event_id' => $event->id,
                'event_type' => $event->type,
            ]);

            switch ($event->type) {
                case 'checkout.session.completed':
                    $session = $event->data->object;

                    Log::info('Stripe checkout completed', [
                        'session_id' => $session->id,
                    ]);
                    break;

                case 'payment_intent.succeeded':
                    $intent = $event->data->object;

                    Log::info('Stripe payment succeeded', [
                        'intent_id' => $intent->id,
                    ]);
                    break;
            }
        } catch (Throwable $e) {
            Log::error('Webhook job dispatch failed', [
                'event_id' => $event->id ?? 'unknown',
                'event_type' => $event->type ?? 'unknown',
                'error' => $e->getMessage(),
            ]);
            // Continue and return 200 even if job dispatch fails
        }

        return response()->json(['status' => 'success'], Response::HTTP_OK);
    }

    /**
     * Payment success callback
     */
    public function success(Request $request)
    {
        $paymentIntentId = (string) $request->query('payment_intent', '');

        if (!$paymentIntentId) {
            Log::warning('payment.success_callback_no_intent');
            return redirect()->route('home')->with('error', 'Invalid payment.');
        }

        try {
            $payment = Payment::with('booking')
                ->where('stripe_payment_intent_id', $paymentIntentId)
                ->firstOrFail();

            Log::info('payment.success_callback_received', [
                'payment_id' => $payment->id,
                'booking_id' => $payment->booking_id,
                'payment_intent_id' => $paymentIntentId,
                'current_status' => $payment->status?->value ?? (string) $payment->status,
            ]);

            if (($payment->status?->value ?? (string) $payment->status) === PaymentState::SUCCEEDED->value) {
                Log::info('payment.already_succeeded_redirect', [
                    'payment_id' => $payment->id,
                    'booking_id' => $payment->booking_id,
                ]);
                session(['booking_email' => $payment->booking->customer_email]);

                return redirect()
                    ->route('bookings.confirmation', $payment->booking)
                    ->with('success', 'Payment successful! Your booking is confirmed.');
            }

            // Verify payment status immediately with Stripe
            Log::info('payment.verifying_with_stripe', [
                'payment_id' => $payment->id,
                'payment_intent_id' => $paymentIntentId,
            ]);

            $stripeIntent = $this->stripeService->retrievePaymentIntent($paymentIntentId);

            // Check for failed/canceled status
            if (in_array($stripeIntent->status, ['canceled', 'requires_payment_method'], true)) {
                Log::warning('payment.failed_on_stripe', [
                    'payment_id' => $payment->id,
                    'booking_id' => $payment->booking_id,
                    'stripe_status' => $stripeIntent->status,
                ]);

                return redirect()
                    ->route('bookings.create', $payment->booking->offer_id)
                    ->with('error', 'Payment failed or was cancelled. Please try again.');
            }

            if ($stripeIntent->status === 'succeeded') {
                // Payment succeeded on Stripe - mark it as succeeded immediately
                Log::info('payment.confirmed_on_stripe', [
                    'payment_id' => $payment->id,
                    'booking_id' => $payment->booking_id,
                    'payment_intent_id' => $paymentIntentId,
                ]);

                $this->stripeService->handleSuccessfulPayment($paymentIntentId);

                // Reload payment to get updated status
                $payment = $payment->fresh();
                session(['booking_email' => $payment->booking->customer_email]);

                Log::info('payment.marked_as_succeeded', [
                    'payment_id' => $payment->id,
                    'booking_id' => $payment->booking_id,
                    'new_status' => $payment->status?->value ?? (string) $payment->status,
                ]);

                return redirect()
                    ->route('bookings.confirmation', $payment->booking)
                    ->with('success', 'Payment successful! Your booking is confirmed.');
            }

            // Payment not yet confirmed - show processing view with polling
            Log::info('payment.processing_status_shown', [
                'payment_id' => $payment->id,
                'booking_id' => $payment->booking_id,
                'stripe_status' => $stripeIntent->status,
            ]);

            return view('bookings.payment-processing', [
                'paymentIntentId' => $paymentIntentId,
                'booking' => $payment->booking,
            ]);
        } catch (Throwable $e) {
            Log::error('payment.success_callback_failed', [
                'payment_intent_id' => $paymentIntentId,
                'error' => $e->getMessage(),
            ]);
            return redirect()->route('home')
                ->with('error', 'Unable to confirm payment. Please contact support.');
        }
    }

    public function status(Request $request)
    {
        $paymentIntentId = (string) $request->query('payment_intent', '');
        $payment = Payment::with('booking')->where('stripe_payment_intent_id', $paymentIntentId)->first();

        if (!$payment) {
            return response()->json(['status' => 'not_found'], Response::HTTP_NOT_FOUND);
        }

        $status = $payment->status?->value ?? (string) $payment->status;

        // Only check Stripe API if payment is pending/processing AND older than 5 seconds
        // This prevents excessive API calls and Stripe rate limiting
        $shouldCheckStripe = (
            ($status === PaymentState::PENDING->value || $status === PaymentState::PROCESSING->value) &&
            $payment->created_at->addSeconds(5) <= now()
        );

        if ($shouldCheckStripe) {
            try {
                $stripeIntent = $this->stripeService->retrievePaymentIntent($paymentIntentId);

                if ($stripeIntent->status === 'succeeded') {
                    // Payment succeeded - mark it before returning
                    $this->stripeService->handleSuccessfulPayment($paymentIntentId);
                    $payment = $payment->fresh();
                    $status = PaymentState::SUCCEEDED->value;
                } elseif (in_array($stripeIntent->status, ['canceled', 'requires_payment_method'], true)) {
                    // Payment failed
                    $this->stripeService->handleFailedPayment($paymentIntentId);
                    $payment = $payment->fresh();
                    $status = PaymentState::FAILED->value;
                }
            } catch (Throwable $e) {
                Log::warning('payment.status_stripe_check_failed', [
                    'payment_intent_id' => $paymentIntentId,
                    'error' => $e->getMessage(),
                ]);
                // Continue with DB status if Stripe check fails
            }
        }

        if ($status === PaymentState::SUCCEEDED->value && $payment->booking) {
            session(['booking_email' => $payment->booking->customer_email]);
        }

        return response()->json([
            'status' => $status,
            'confirmation_url' => $status === PaymentState::SUCCEEDED->value && $payment->booking
                ? route('bookings.confirmation', $payment->booking)
                : null,
        ]);
    }

    /**
     * Payment cancellation callback
     */
    public function cancel(Request $request)
    {
        $paymentIntentId = (string) $request->query('payment_intent', '');

        if ($paymentIntentId) {
            try {
                $payment = Payment::where('stripe_payment_intent_id', $paymentIntentId)->first();
                if ($payment && $payment->booking) {
                    Log::info('payment.cancelled_user_redirect', [
                        'payment_id' => $payment->id,
                        'booking_id' => $payment->booking_id,
                        'payment_intent_id' => $paymentIntentId,
                    ]);

                    return redirect()->route('bookings.create', $payment->booking->offer_id)
                        ->with('error', 'Payment failed or was cancelled. Please try again.');
                }
            } catch (Throwable $e) {
                Log::warning('payment.cancel_lookup_failed', [
                    'payment_intent_id' => $paymentIntentId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('payment.cancelled_generic_home', [
            'payment_intent_id' => $paymentIntentId,
        ]);

        return redirect()->route('home')
            ->with('info', 'Payment was cancelled. Your booking was not confirmed.');
    }
}
