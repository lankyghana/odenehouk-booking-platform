<?php

namespace Tests\Feature;

use App\Models\Offer;
use App\Services\StripeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Stripe\PaymentIntent;
use Tests\TestCase;

class BookingCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_creation_creates_pending_booking_and_payment(): void
    {
        $offer = Offer::factory()->create([
            'price' => 120.00,
            'duration_minutes' => 60,
        ]);

        $this->mock(StripeService::class, function ($mock): void {
            $mock->shouldReceive('createPaymentIntent')->once()->andReturn(PaymentIntent::constructFrom([
                'id' => 'pi_test_123',
                'client_secret' => 'pi_test_123_secret_abc',
            ]));
        });

        $response = $this->followingRedirects()->post(route('bookings.store', $offer), [
            'booking_date' => now()->addDay()->toDateString(),
            'booking_time' => '10:00',
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'customer_phone' => '+123456789',
        ]);

        $response->assertSuccessful();
        $this->assertDatabaseHas('bookings', [
            'offer_id' => $offer->id,
            'customer_email' => 'john@example.com',
            'payment_status' => 'pending',
        ]);
        $this->assertDatabaseHas('payments', [
            'stripe_payment_intent_id' => 'pi_test_123',
        ]);
    }
}
