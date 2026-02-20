<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentSuccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_success_redirects_to_confirmation_when_webhook_already_confirmed(): void
    {
        $booking = Booking::factory()->create([
            'payment_status' => 'succeeded',
        ]);

        Payment::factory()->create([
            'booking_id' => $booking->id,
            'stripe_payment_intent_id' => 'pi_success_123',
            'status' => 'succeeded',
        ]);

        $response = $this->get(route('payment.success', ['payment_intent' => 'pi_success_123']));

        $response->assertRedirect(route('bookings.confirmation', $booking));
    }
}
