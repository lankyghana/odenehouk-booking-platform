@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-gray-600">Overview of bookings, revenue, and offers.</p>
        </div>
        <a href="{{ route('home') }}" class="text-primary-600 hover:text-primary-700 text-sm">View site</a>
    </div>

    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Hero Branding</h2>
                <p class="text-gray-600 text-sm">Update the homepage hero (photo, name, title, and links).</p>
            </div>
            <a href="{{ route('admin.branding.edit') }}" class="text-primary-600 hover:text-primary-700 text-sm">Open full page</a>
        </div>

        <form action="{{ route('admin.branding.update') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            @csrf

            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Hero Photo</label>
                    <input type="file" name="hero_image" accept="image/*" class="w-full border border-gray-200 rounded-lg p-2.5">
                    @if(($branding['hero_image'] ?? null))
                        <p class="text-xs text-gray-500 mt-1">Current: {{ $branding['hero_image'] }}</p>
                    @endif
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="hero_name" value="{{ old('hero_name', $branding['hero_name'] ?? '') }}" class="w-full border border-gray-200 rounded-lg p-2.5" placeholder="Hero name">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Headline</label>
                    <input type="text" name="hero_title" value="{{ old('hero_title', $branding['hero_title'] ?? '') }}" class="w-full border border-gray-200 rounded-lg p-2.5" placeholder="What you do">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @php
                    $socialFields = [
                        'instagram' => 'Instagram URL',
                        'tiktok' => 'TikTok URL',
                        'email' => 'Contact Email',
                        'facebook' => 'Facebook URL',
                        'youtube' => 'YouTube URL',
                        'linkedin' => 'LinkedIn URL',
                    ];
                @endphp
                @foreach($socialFields as $field => $label)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ $label }}</label>
                        <input type="text" name="{{ $field }}" value="{{ old($field, $branding[$field] ?? '') }}" class="w-full border border-gray-200 rounded-lg p-2.5">
                    </div>
                @endforeach
                <div class="md:col-span-2 flex justify-end pt-1">
                    <button type="submit" class="btn-primary">Save branding</button>
                </div>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow p-5">
            <p class="text-sm text-gray-500">Total Bookings</p>
            <p class="text-3xl font-semibold text-gray-900 mt-1">{{ $stats['total_bookings'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-5">
            <p class="text-sm text-gray-500">Today</p>
            <p class="text-3xl font-semibold text-gray-900 mt-1">{{ $stats['today_bookings'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-5">
            <p class="text-sm text-gray-500">Pending</p>
            <p class="text-3xl font-semibold text-amber-600 mt-1">{{ $stats['pending_bookings'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-5">
            <p class="text-sm text-gray-500">Confirmed</p>
            <p class="text-3xl font-semibold text-emerald-600 mt-1">{{ $stats['confirmed_bookings'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-5">
            <p class="text-sm text-gray-500">Revenue (Month)</p>
            <p class="text-3xl font-semibold text-gray-900 mt-1">${{ number_format($stats['month_revenue'], 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-5">
            <p class="text-sm text-gray-500">Revenue (Total)</p>
            <p class="text-3xl font-semibold text-gray-900 mt-1">${{ number_format($stats['total_revenue'], 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-5">
            <p class="text-sm text-gray-500">Active Offers</p>
            <p class="text-3xl font-semibold text-gray-900 mt-1">{{ $stats['active_offers'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-5">
            <p class="text-sm text-gray-500">Total Customers</p>
            <p class="text-3xl font-semibold text-gray-900 mt-1">{{ $stats['total_customers'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Recent Bookings</h2>
                <a href="{{ route('admin.bookings.index') }}" class="text-primary-600 text-sm">View all</a>
            </div>
            <div class="space-y-3">
                @forelse($recentBookings as $booking)
                    <div class="flex items-center justify-between border border-gray-100 rounded-lg p-3">
                        <div>
                            <p class="font-medium text-gray-900">{{ $booking->customer_name }}</p>
                            <p class="text-sm text-gray-600">{{ $booking->offer->title ?? 'Offer' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-700">{{ $booking->booking_date->format('M j, Y') }} {{ $booking->booking_time }}</p>
                            <p class="text-xs uppercase tracking-wide text-gray-500">{{ $booking->status }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-600">No bookings yet.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Upcoming</h2>
                <a href="{{ route('admin.bookings.calendar') }}" class="text-primary-600 text-sm">Calendar</a>
            </div>
            <div class="space-y-3">
                @forelse($upcomingBookings as $booking)
                    <div class="flex items-center justify-between border border-gray-100 rounded-lg p-3">
                        <div>
                            <p class="font-medium text-gray-900">{{ $booking->customer_name }}</p>
                            <p class="text-sm text-gray-600">{{ $booking->offer->title ?? 'Offer' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-700">{{ $booking->booking_date->format('M j, Y') }} {{ $booking->booking_time }}</p>
                            <p class="text-xs uppercase tracking-wide text-gray-500">{{ $booking->status }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-600">No upcoming bookings.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
