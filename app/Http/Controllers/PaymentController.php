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

            return response()->json(['error' => 'Invalid signature'], Response::HTTP_BAD_REQUEST);
        }

        ProcessStripeWebhookJob::dispatch($event->toArray());

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

        return response()->json(['status' => 'success'], Response::HTTP_OK);
    }

    /**
     * Payment success callback
     */
    public function success(Request $request)
    {
        $paymentIntentId = (string) $request->query('payment_intent', '');
        
        if (!$paymentIntentId) {
            return redirect()->route('home')->with('error', 'Invalid payment.');
        }

        try {
            $payment = Payment::with('booking')
                ->where('stripe_payment_intent_id', $paymentIntentId)
                ->firstOrFail();

            if (($payment->status?->value ?? (string) $payment->status) === PaymentState::SUCCEEDED->value) {
                session(['booking_email' => $payment->booking->customer_email]);

                return redirect()
                    ->route('bookings.confirmation', $payment->booking)
                    ->with('success', 'Payment successful! Your booking is confirmed.');
            }

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

        if ($status === PaymentState::SUCCEEDED->value && $payment->booking) {
            session(['booking_email' => $payment->booking->customer_email]);
        }

        return response()->json([
            'status' => $status,
            'confirmation_url' => $status === PaymentState::SUCCEEDED->value
                ? route('bookings.confirmation', $payment->booking)
                : null,
        ]);
    }

    /**
     * Payment cancellation callback
     */
    public function cancel()
    {
        return redirect()->route('home')
            ->with('info', 'Payment was cancelled. Your booking was not confirmed.');
    }
}
