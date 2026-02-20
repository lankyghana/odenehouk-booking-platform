<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'booking_id' => Booking::factory(),
            'stripe_payment_intent_id' => 'pi_' . fake()->bothify('################'),
            'stripe_charge_id' => null,
            'amount' => 99.00,
            'currency' => 'usd',
            'status' => 'pending',
            'payment_method' => null,
            'metadata' => [],
            'refunded_amount' => 0,
            'refunded_at' => null,
        ];
    }
}
