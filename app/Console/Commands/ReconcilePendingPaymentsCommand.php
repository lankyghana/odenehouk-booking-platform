<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Services\StripeService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class ReconcilePendingPaymentsCommand extends Command
{
    protected $signature = 'payments:reconcile-pending';

    protected $description = 'Reconcile stale pending/processing Stripe payments';

    public function handle(StripeService $stripeService): int
    {
        $stalePayments = Payment::query()
            ->whereIn('status', ['pending', 'processing'])
            ->where('created_at', '<=', now()->subMinutes(15))
            ->limit(100)
            ->get();

        foreach ($stalePayments as $payment) {
            try {
                $intent = $stripeService->retrievePaymentIntent($payment->stripe_payment_intent_id);

                if (($intent->status ?? null) === 'succeeded') {
                    $stripeService->handleSuccessfulPayment($payment->stripe_payment_intent_id);
                    continue;
                }

                if (in_array($intent->status ?? null, ['canceled', 'requires_payment_method'], true)) {
                    $stripeService->handleFailedPayment($payment->stripe_payment_intent_id);
                }
            } catch (Throwable $e) {
                Log::warning('payment.reconcile_failed', [
                    'payment_id' => $payment->id,
                    'payment_intent_id' => $payment->stripe_payment_intent_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info('Pending payment reconciliation completed.');
        return self::SUCCESS;
    }
}
