<?php

namespace Tests\Unit;

use App\Models\Booking;
use App\Models\User;
use App\Policies\BookingPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_view_booking(): void
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'customer_email' => 'other@example.com',
        ]);

        $policy = new BookingPolicy();

        $this->assertTrue($policy->view($user, $booking));
    }

    public function test_non_owner_cannot_view_booking_without_permissions(): void
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create([
            'customer_email' => 'not-user@example.com',
        ]);

        $policy = new BookingPolicy();

        $this->assertFalse($policy->view($user, $booking));
    }
}
