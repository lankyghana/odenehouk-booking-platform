@extends('layouts.admin')

@section('title', 'Offer Details')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $offer->title }}</h1>
            <p class="text-gray-600">{{ $offer->category ?? 'General' }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <form action="{{ route('admin.offers.toggle-status', $offer) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="btn-secondary">{{ $offer->is_active ? 'Deactivate' : 'Activate' }}</button>
            </form>
            <a href="{{ route('admin.offers.edit', $offer) }}" class="btn-primary">Edit</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-xl shadow p-6 space-y-3">
                <h2 class="text-lg font-semibold text-gray-900">Details</h2>
                <p class="text-gray-700">{{ $offer->description }}</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm text-gray-700">
                    <div><span class="text-gray-500">Price</span><br>${{ number_format($offer->price, 2) }}</div>
                    <div><span class="text-gray-500">Duration</span><br>{{ $offer->duration_minutes }} mins</div>
                    <div><span class="text-gray-500">Max/day</span><br>{{ $offer->max_bookings_per_day ?? '—' }}</div>
                </div>
                @if($offer->image_url)
                    <div class="mt-3">
                        <img src="{{ $offer->image_url }}" alt="Offer image" class="rounded-lg border border-gray-100">
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-3">Recent Bookings</h2>
                <div class="space-y-3">
                    @forelse($recentBookings as $booking)
                        <div class="flex items-center justify-between border border-gray-100 rounded-lg p-3">
                            <div>
                                <p class="font-medium text-gray-900">{{ $booking->customer_name }}</p>
                                <p class="text-sm text-gray-600">{{ $booking->booking_date->format('M j, Y') }} {{ $booking->booking_time }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-700">${{ number_format($booking->total_amount, 2) }}</p>
                                <p class="text-xs uppercase tracking-wide text-gray-500">{{ $booking->status }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-600">No bookings yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-3">Meta</h2>
                <dl class="space-y-2 text-sm text-gray-700">
                    <div class="flex justify-between"><span>Status</span><span class="capitalize">{{ $offer->is_active ? 'Active' : 'Inactive' }}</span></div>
                    <div class="flex justify-between"><span>Bookings</span><span>{{ $offer->bookings_count }}</span></div>
                    <div class="flex justify-between"><span>Created</span><span>{{ $offer->created_at->format('M j, Y') }}</span></div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
