<?php

namespace App\Services;

use App\Enums\PaymentState;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class PaymentTransitionService
{
    private const ALLOWED = [
        'pending' => ['processing', 'succeeded', 'failed'],
        'processing' => ['succeeded', 'failed'],
        'succeeded' => ['refunded'],
        'failed' => [],
        'refunded' => [],
    ];

    public function transition(Payment $payment, PaymentState $target, array $context = []): Payment
    {
        $current = $payment->status instanceof PaymentState ? $payment->status->value : (string) $payment->status;
        $next = $target->value;

        if ($current === $next) {
            return $payment;
        }

        if (!in_array($next, self::ALLOWED[$current] ?? [], true)) {
            throw new RuntimeException("Invalid payment state transition {$current} -> {$next}");
        }

        DB::transaction(function () use ($payment, $target, $context): void {
            $freshPayment = Payment::query()->whereKey($payment->id)->lockForUpdate()->firstOrFail();
            $freshPayment->status = $target;

            if ($target === PaymentState::REFUNDED) {
                $freshPayment->refunded_at = now();
            }

            $freshPayment->save();

            $booking = $freshPayment->booking;
            if (!$booking) {
                return;
            }

            match ($target) {
                PaymentState::SUCCEEDED => $booking->update([
                    'status' => 'confirmed',
                    'payment_status' => PaymentState::SUCCEEDED->value,
                    'payment_id' => (string) $freshPayment->id,
                ]),
                PaymentState::FAILED => $booking->update([
                    'payment_status' => PaymentState::FAILED->value,
                ]),
                PaymentState::REFUNDED => $booking->update([
                    'status' => 'cancelled',
                    'payment_status' => PaymentState::REFUNDED->value,
                ]),
                default => null,
            };
        });

        Log::info('payment.state_transitioned', [
            'payment_id' => $payment->id,
            'booking_id' => $payment->booking_id,
            'from' => $current,
            'to' => $target->value,
            'context' => $context,
        ]);

        return $payment->fresh();
    }
}
