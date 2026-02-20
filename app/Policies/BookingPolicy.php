<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function view(User $user, Booking $booking): bool
    {
        return $user->can('manage bookings') || $booking->user_id === $user->id || $booking->customer_email === $user->email;
    }

    public function cancel(User $user, Booking $booking): bool
    {
        return $this->view($user, $booking) && $booking->canBeCancelled();
    }

    public function manage(User $user): bool
    {
        return $user->can('manage bookings');
    }
}
