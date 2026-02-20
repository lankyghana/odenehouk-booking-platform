<?php

namespace App\Jobs;

use App\Mail\BookingConfirmedMail;
use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendBookingConfirmationEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $bookingId)
    {
        $this->onQueue('notifications');
    }

    public function handle(): void
    {
        $booking = Booking::find($this->bookingId);
        if (!$booking || !$booking->customer_email) {
            return;
        }

        Mail::to($booking->customer_email)->send(new BookingConfirmedMail($booking));
    }
}
