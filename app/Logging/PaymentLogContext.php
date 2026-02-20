<?php

namespace App\Logging;

class PaymentLogContext
{
    public static function make(
        ?int $bookingId = null,
        ?int $paymentId = null,
        ?string $paymentIntentId = null,
        ?string $stripeEventId = null,
    ): array {
        return [
            'booking_id' => $bookingId,
            'payment_id' => $paymentId,
            'payment_intent_id' => $paymentIntentId,
            'stripe_event_id' => $stripeEventId,
            'correlation_id' => request()?->attributes->get('correlation_id'),
        ];
    }
}
