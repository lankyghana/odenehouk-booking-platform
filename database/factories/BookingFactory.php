<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        $time = fake()->time('H:i:s');

        return [
            'user_id' => User::factory(),
            'offer_id' => Offer::factory(),
            'booking_date' => now()->addDay()->toDateString(),
            'booking_time' => $time,
            'end_time' => now()->addDay()->addHour()->format('H:i:s'),
            'status' => 'pending',
            'customer_name' => fake()->name(),
            'customer_email' => fake()->safeEmail(),
            'customer_phone' => fake()->phoneNumber(),
            'notes' => null,
            'total_amount' => 99.00,
            'payment_id' => null,
            'payment_status' => 'pending',
            'cancellation_reason' => null,
            'cancelled_at' => null,
        ];
    }
}
