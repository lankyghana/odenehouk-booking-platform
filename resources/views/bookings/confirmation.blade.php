@extends('layouts.app')

@section('title', 'Booking Confirmed')

@section('content')
<div class="container mx-auto px-4 max-w-2xl">
    @include('bookings.partials.steps', ['step' => 4])

    <div class="bg-white rounded-2xl shadow-md overflow-hidden text-center">
        <div class="bg-gradient-to-r from-primary-600 to-accent-600 p-10 text-white">
            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold mb-2">You're all booked in!</h1>
            <p class="text-white/90">A confirmation email is on its way to {{ $booking->customer_email }}</p>
        </div>

        <div class="p-6 sm:p-8 text-left space-y-6">
            <div class="bg-gray-50 rounded-xl p-5 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Service</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $booking->offer->title ?? 'Booking' }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Date &amp; time</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $booking->booking_date->format('M j, Y') }} at {{ \Carbon\Carbon::parse($booking->booking_time)->format('g:i A') }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Booking reference</span>
                    <span class="text-sm font-semibold text-gray-900">#{{ $booking->id }}</span>
                </div>
                <div class="flex items-center justify-between pt-3 border-t border-gray-200">
                    <span class="text-sm text-gray-500">Status</span>
                    <span class="badge {{ $booking->status === 'confirmed' ? 'badge-success' : 'badge-warning' }}">{{ ucfirst($booking->status) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Payment</span>
                    <span class="badge badge-success">{{ ucfirst($booking->payment_status) }}</span>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('home') }}" class="btn-secondary flex-1 text-center">Back to home</a>
                @if($booking->review_token)
                    <a href="{{ route('review.show', $booking->review_token) }}" class="btn-primary flex-1 text-center">Leave a review</a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
