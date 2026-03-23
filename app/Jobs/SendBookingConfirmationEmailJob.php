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
use Illuminate\Support\Facades\Log;
use Throwable;

class SendBookingConfirmationEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $bookingId)
    {
        $this->onQueue('notifications');
    }

    public function handle(): void
    {
        $booking = Booking::with('payment')->find($this->bookingId);
        if (!$booking || !$booking->customer_email) {
            Log::warning('booking.confirmation_email_booking_not_found', [
                'booking_id' => $this->bookingId,
            ]);
            return;
        }

        // Duplication guard: only send if payment is confirmed
        if (!$booking->payment || !$booking->payment->isSuccessful()) {
            Log::warning('booking.confirmation_email_payment_not_confirmed', [
                'booking_id' => $this->bookingId,
                'payment_id' => $booking->payment?->id,
                'payment_status' => $booking->payment?->status,
            ]);
            return;
        }

        try {
            Mail::to($booking->customer_email)->send(new BookingConfirmedMail($booking));

            Log::info('booking.confirmation_email_sent', [
                'booking_id' => $this->bookingId,
                'customer_email' => $booking->customer_email,
            ]);
        } catch (Throwable $e) {
            Log::error('booking.confirmation_email_send_failed', [
                'booking_id' => $this->bookingId,
                'error' => $e->getMessage(),
                'exception_class' => get_class($e),
            ]);

            // Do NOT re-throw - email failures should not block payment confirmation
            // The job can be retried manually later, but won't crash the payment flow
        }
    }
}
