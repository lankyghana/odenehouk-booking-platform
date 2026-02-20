<?php

namespace Database\Factories;

use App\Models\Offer;
use Illuminate\Database\Eloquent\Factories\Factory;

class OfferFactory extends Factory
{
    protected $model = Offer::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 25, 500),
            'duration_minutes' => 60,
            'is_active' => true,
            'image_url' => null,
            'max_bookings_per_day' => 10,
            'category' => 'Consultation',
        ];
    }
}
