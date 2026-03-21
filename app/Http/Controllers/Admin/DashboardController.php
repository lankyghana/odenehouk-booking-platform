<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Offer;
use App\Models\Payment;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display admin dashboard
     */
    public function index()
    {
        $stats = $this->getStats();
        $recentBookings = Booking::with(['offer', 'user'])
            ->latest()
            ->take(10)
            ->get();

        $upcomingBookings = Booking::with(['offer'])
            ->where('booking_date', '>=', Carbon::today())
            ->whereIn('status', ['confirmed', 'pending'])
            ->orderBy('booking_date')
            ->orderBy('booking_time')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentBookings', 'upcomingBookings'));
    }

    /**
     * Get dashboard statistics
     */
    private function getStats(): array
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        return [
            'total_bookings' => Booking::count(),
            'today_bookings' => Booking::whereDate('booking_date', $today)->count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'confirmed_bookings' => Booking::where('status', 'confirmed')->count(),
            'month_revenue' => Payment::where('status', 'succeeded')
                ->where('created_at', '>=', $thisMonth)
                ->sum('amount'),
            'total_revenue' => Payment::where('status', 'succeeded')->sum('amount'),
            'active_offers' => Offer::where('is_active', true)->count(),
            'total_customers' => Booking::distinct('customer_email')->count(),
        ];
    }
}
