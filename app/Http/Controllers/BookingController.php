<?php

namespace App\Http\Controllers;

use App\Http\Requests\Booking\AvailableSlotsRequest;
use App\Http\Requests\Booking\CancelBookingRequest;
use App\Http\Requests\Booking\StoreBookingRequest;
use App\Models\Payment;
use App\Models\Offer;
use App\Models\Booking;
use App\Services\StripeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Throwable;

class BookingController extends Controller
{
    protected StripeService $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Show booking form
     */
    public function create(Offer $offer)
    {
        if (!$offer->is_active) {
            return redirect()->route('home')
                ->with('error', 'This offer is not available for booking.');
        }

        return view('bookings.create', compact('offer'));
    }

    /**
     * Get available time slots for a date
     */
    public function getAvailableSlots(AvailableSlotsRequest $request, Offer $offer)
    {
        $date = Carbon::parse($request->date);
        
        // Get existing bookings for this date and offer
        $existingBookings = Booking::where('offer_id', $offer->id)
            ->where('booking_date', $date)
            ->whereIn('status', ['confirmed', 'pending'])
            ->get(['booking_time', 'end_time']);

        // Generate available slots (9 AM to 5 PM)
        $startHour = 9;
        $endHour = 17;
        $slotDuration = $offer->duration_minutes;
        $availableSlots = [];

        $currentTime = Carbon::createFromTime($startHour, 0);
        $endTime = Carbon::createFromTime($endHour, 0);

        while ($currentTime->lt($endTime)) {
            $slotTime = $currentTime->format('H:i:s');
            $slotEndTime = $currentTime->copy()->addMinutes($slotDuration);

            // Check if slot overlaps with existing booking
            $isAvailable = true;
            foreach ($existingBookings as $booking) {
                $bookingStart = Carbon::parse($booking->booking_time);
                $bookingEnd = Carbon::parse($booking->end_time);

                if ($currentTime->lt($bookingEnd) && $slotEndTime->gt($bookingStart)) {
                    $isAvailable = false;
                    break;
                }
            }

            if ($isAvailable && $slotEndTime->lte($endTime)) {
                $availableSlots[] = [
                    'time' => $slotTime,
                    'display' => $currentTime->format('g:i A'),
                ];
            }

            $currentTime->addMinutes(30); // 30-minute intervals
        }

        return response()->json(['slots' => $availableSlots]);
    }

    /**
     * Store a new booking
     */
    public function store(StoreBookingRequest $request, Offer $offer)
    {
        $data = $request->validated();
        $booking = null;

        try {
            $booking = DB::transaction(function () use ($offer, $data) {
                $lockedOffer = Offer::query()->whereKey($offer->id)->lockForUpdate()->firstOrFail();

                $bookingTime = Carbon::parse($data['booking_time']);
                $endTime = $bookingTime->copy()->addMinutes($lockedOffer->duration_minutes);

                $activeBookingCount = Booking::query()
                    ->where('offer_id', $lockedOffer->id)
                    ->whereDate('booking_date', $data['booking_date'])
                    ->whereIn('status', ['confirmed', 'pending'])
                    ->lockForUpdate()
                    ->count();

                if ($lockedOffer->max_bookings_per_day && $activeBookingCount >= $lockedOffer->max_bookings_per_day) {
                    throw new \RuntimeException('This offer has reached the daily booking limit.');
                }

                $overlapExists = Booking::query()
                    ->where('offer_id', $lockedOffer->id)
                    ->whereDate('booking_date', $data['booking_date'])
                    ->whereIn('status', ['confirmed', 'pending'])
                    ->where(function ($query) use ($bookingTime, $endTime) {
                        $query->where('booking_time', '<', $endTime->format('H:i:s'))
                            ->where('end_time', '>', $bookingTime->format('H:i:s'));
                    })
                    ->lockForUpdate()
                    ->exists();

                if ($overlapExists) {
                    throw new \RuntimeException('This time slot is no longer available.');
                }

                return Booking::create([
                    'user_id' => auth()->id(),
                    'offer_id' => $lockedOffer->id,
                    'booking_date' => $data['booking_date'],
                    'booking_time' => $bookingTime->format('H:i:s'),
                    'end_time' => $endTime->format('H:i:s'),
                    'customer_name' => $data['customer_name'],
                    'customer_email' => $data['customer_email'],
                    'customer_phone' => $data['customer_phone'] ?? null,
                    'notes' => $data['notes'] ?? null,
                    'total_amount' => $lockedOffer->price,
                    'status' => 'pending',
                    'payment_status' => 'pending',
                ]);
            });

            $paymentIntent = $this->stripeService->createPaymentIntent($booking);

            Payment::updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'stripe_payment_intent_id' => $paymentIntent->id,
                    'amount' => $booking->total_amount,
                    'currency' => 'usd',
                    'status' => 'pending',
                    'metadata' => [
                        'offer_id' => $booking->offer_id,
                        'booking_date' => $booking->booking_date->format('Y-m-d'),
                        'booking_time' => $booking->booking_time,
                    ],
                ]
            );

            return view('bookings.payment', [
                'booking' => $booking,
                'offer' => $offer,
                'clientSecret' => $paymentIntent->client_secret,
                'stripeKey' => config('services.stripe.key'),
            ]);
        } catch (Throwable $e) {
            Log::warning('booking.store_failed', [
                'offer_id' => $offer->id,
                'booking_id' => $booking?->id,
                'error' => $e->getMessage(),
            ]);
            
            return back()->with('error', $e->getMessage() ?: 'An error occurred while processing your booking. Please try again.')
                ->withInput();
        }
    }

    /**
     * Show booking confirmation
     */
    public function confirmation(Booking $booking)
    {
        // Ensure user can view this booking
        if ($booking->customer_email !== session('booking_email') && 
            (!auth()->check() || auth()->id() !== $booking->user_id)) {
            abort(403);
        }

        if ($booking->payment_status !== 'succeeded') {
            return redirect()->route('payment.success', ['payment_intent' => $booking->payment?->stripe_payment_intent_id])
                ->with('info', 'Your payment is still processing.');
        }

        return view('bookings.confirmation', compact('booking'));
    }

    /**
     * Cancel a booking
     */
    public function cancel(CancelBookingRequest $request, Booking $booking)
    {
        if (!$booking->canBeCancelled()) {
            return back()->with('error', 'This booking cannot be cancelled.');
        }

        $this->authorize('cancel', $booking);

        try {
            DB::beginTransaction();

            $booking->update([
                'status' => 'cancelled',
                'cancellation_reason' => $request->validated('cancellation_reason'),
                'cancelled_at' => now(),
            ]);

            // Process refund if payment was successful
            if ($booking->payment && $booking->payment->isSuccessful()) {
                $this->stripeService->refundPayment($booking->payment);
            }

            DB::commit();

            return redirect()->route('home')
                ->with('success', 'Your booking has been cancelled and refund has been processed.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'Failed to cancel booking. Please contact support.');
        }
    }
}
