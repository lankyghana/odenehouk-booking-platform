<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CancelAdminBookingRequest;
use App\Http\Requests\Admin\RescheduleBookingRequest;
use App\Http\Requests\Admin\UpdateBookingStatusRequest;
use App\Models\Booking;
use App\Models\Offer;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingController extends Controller
{
    protected StripeService $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Display all bookings
     */
    public function index(Request $request)
    {
        $query = Booking::query()
            ->with(['offer', 'user', 'payment'])
            ->leftJoin('offers', 'offers.id', '=', 'bookings.offer_id')
            ->select('bookings.*');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('booking_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('booking_date', '<=', $request->date_to);
        }

        // Search by customer
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $bookings = $query->orderByDesc('offers.created_at')
            ->orderBy('booking_date', 'desc')
            ->orderBy('booking_time', 'desc')
            ->paginate(20);

        return view('admin.bookings.index', compact('bookings'));
    }

    /**
     * Show booking details
     */
    public function show(Booking $booking)
    {
        $booking->load(['offer', 'user', 'payment']);
        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * Show calendar view
     */
    public function calendar()
    {
        return view('admin.bookings.calendar');
    }

    /**
     * Get bookings for calendar (API endpoint)
     */
    public function getCalendarEvents(Request $request)
    {
        $start = Carbon::parse($request->start);
        $end = Carbon::parse($request->end);

        $bookings = Booking::with('offer')
            ->whereBetween('booking_date', [$start, $end])
            ->whereIn('status', ['confirmed', 'pending'])
            ->get();

        $events = $bookings->map(function ($booking) {
            return [
                'id' => $booking->id,
                'title' => $booking->customer_name . ' - ' . $booking->offer->title,
                'start' => $booking->booking_date->format('Y-m-d') . 'T' . $booking->booking_time,
                'end' => $booking->booking_date->format('Y-m-d') . 'T' . $booking->end_time,
                'backgroundColor' => $this->getStatusColor($booking->status),
                'borderColor' => $this->getStatusColor($booking->status),
                'extendedProps' => [
                    'customer_email' => $booking->customer_email,
                    'customer_phone' => $booking->customer_phone,
                    'status' => $booking->status,
                    'amount' => $booking->total_amount,
                ],
            ];
        });

        return response()->json($events);
    }

    /**
     * Reschedule a booking
     */
    public function reschedule(RescheduleBookingRequest $request, Booking $booking)
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();

            $newTime = Carbon::parse($data['new_time']);
            $newEndTime = $newTime->copy()->addMinutes($booking->offer->duration_minutes);

            // Check if new slot is available
            $conflict = Booking::where('offer_id', $booking->offer_id)
                ->where('id', '!=', $booking->id)
                ->where('booking_date', $data['new_date'])
                ->where(function ($query) use ($newTime, $newEndTime) {
                    $query->where('booking_time', '<', $newEndTime->format('H:i:s'))
                        ->where('end_time', '>', $newTime->format('H:i:s'));
                })
                ->whereIn('status', ['confirmed', 'pending'])
                ->exists();

            if ($conflict) {
                return back()->with('error', 'The selected time slot is not available.');
            }

            $booking->update([
                'booking_date' => $data['new_date'],
                'booking_time' => $newTime->format('H:i:s'),
                'end_time' => $newEndTime->format('H:i:s'),
            ]);

            DB::commit();

            return redirect()->route('admin.bookings.show', $booking)
                ->with('success', 'Booking rescheduled successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to reschedule booking.');
        }
    }

    /**
     * Update booking status
     */
    public function updateStatus(UpdateBookingStatusRequest $request, Booking $booking)
    {
        $booking->update(['status' => $request->validated('status')]);

        return back()->with('success', 'Booking status updated successfully.');
    }

    /**
     * Cancel booking with refund
     */
    public function cancel(CancelAdminBookingRequest $request, Booking $booking)
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();

            $booking->update([
                'status' => 'cancelled',
                'cancellation_reason' => $data['cancellation_reason'],
                'cancelled_at' => now(),
            ]);

            // Process refund if requested
            if (($data['issue_refund'] ?? false) && $booking->payment && $booking->payment->isSuccessful()) {
                $this->stripeService->refundPayment($booking->payment);
            }

            DB::commit();

            return redirect()->route('admin.bookings.index')
                ->with('success', 'Booking cancelled successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to cancel booking.');
        }
    }

    /**
     * Delete booking
     */
    public function destroy(Booking $booking)
    {
        if (in_array($booking->status, ['confirmed', 'pending']) && 
            $booking->payment && 
            $booking->payment->isSuccessful()) {
            return back()->with('error', 'Cannot delete booking with successful payment. Please cancel and refund first.');
        }

        $booking->delete();

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Booking deleted successfully.');
    }

    /**
     * Get color for booking status
     */
    private function getStatusColor(string $status): string
    {
        return match ($status) {
            'pending' => '#FFA500',
            'confirmed' => '#10B981',
            'completed' => '#3B82F6',
            'cancelled' => '#EF4444',
            default => '#6B7280',
        };
    }
}
