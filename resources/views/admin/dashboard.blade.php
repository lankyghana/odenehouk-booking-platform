@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">
    <section class="rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="p-6 sm:p-8 lg:p-10">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.22em] text-slate-500">Admin Overview</p>
                    <h1 class="mt-2 text-3xl font-semibold text-slate-900 sm:text-4xl">Dashboard</h1>
                    <p class="mt-2 max-w-2xl text-slate-600">A clear snapshot of your bookings, revenue performance, and customer activity.</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.bookings.index') }}" class="inline-flex items-center rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Manage bookings</a>
                    <a href="{{ route('admin.branding.edit') }}" class="inline-flex items-center rounded-xl bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-700">Edit branding</a>
                    <a href="{{ route('home') }}" class="inline-flex items-center rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">View site</a>
                </div>
            </div>
            <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                    <p class="text-xs uppercase tracking-wide text-slate-500">Today</p>
                    <p class="mt-1 text-2xl font-semibold text-slate-900">{{ $stats['today_bookings'] }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                    <p class="text-xs uppercase tracking-wide text-slate-500">Monthly revenue</p>
                    <p class="mt-1 text-2xl font-semibold text-slate-900">${{ number_format($stats['month_revenue'], 2) }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                    <p class="text-xs uppercase tracking-wide text-slate-500">Total customers</p>
                    <p class="mt-1 text-2xl font-semibold text-slate-900">{{ $stats['total_customers'] }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Total bookings</p>
            <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $stats['total_bookings'] }}</p>
        </article>
        <article class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
            <p class="text-sm text-amber-700">Pending bookings</p>
            <p class="mt-2 text-3xl font-semibold text-amber-800">{{ $stats['pending_bookings'] }}</p>
        </article>
        <article class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
            <p class="text-sm text-emerald-700">Confirmed bookings</p>
            <p class="mt-2 text-3xl font-semibold text-emerald-800">{{ $stats['confirmed_bookings'] }}</p>
        </article>
        <article class="rounded-2xl border border-cyan-200 bg-cyan-50 p-5 shadow-sm">
            <p class="text-sm text-cyan-700">Active offers</p>
            <p class="mt-2 text-3xl font-semibold text-cyan-900">{{ $stats['active_offers'] }}</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm md:col-span-2">
            <p class="text-sm text-slate-500">Revenue this month</p>
            <p class="mt-2 text-3xl font-semibold text-slate-900">${{ number_format($stats['month_revenue'], 2) }}</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm md:col-span-2">
            <p class="text-sm text-slate-500">Total revenue</p>
            <p class="mt-2 text-3xl font-semibold text-slate-900">${{ number_format($stats['total_revenue'], 2) }}</p>
        </article>
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <h2 class="text-lg font-semibold text-slate-900">Recent bookings</h2>
                <a href="{{ route('admin.bookings.index') }}" class="text-sm font-medium text-primary-600 hover:text-primary-700">View all</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($recentBookings as $booking)
                    @php
                        $badgeClass = match ($booking->status) {
                            'confirmed' => 'bg-emerald-100 text-emerald-700',
                            'pending' => 'bg-amber-100 text-amber-700',
                            'completed' => 'bg-cyan-100 text-cyan-700',
                            'cancelled' => 'bg-rose-100 text-rose-700',
                            default => 'bg-slate-100 text-slate-700',
                        };
                    @endphp
                    <a href="{{ route('admin.bookings.show', $booking) }}" class="block px-5 py-4 hover:bg-slate-50 transition">
                        <div class="flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate font-medium text-slate-900">{{ $booking->customer_name }}</p>
                                <p class="truncate text-sm text-slate-600">{{ $booking->offer->title ?? 'Offer' }}</p>
                            </div>
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $badgeClass }}">{{ ucfirst($booking->status) }}</span>
                        </div>
                        <p class="mt-2 text-xs text-slate-500">{{ $booking->booking_date->format('M j, Y') }} at {{ $booking->booking_time }}</p>
                    </a>
                @empty
                    <p class="px-5 py-6 text-sm text-slate-600">No bookings yet.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <h2 class="text-lg font-semibold text-slate-900">Upcoming schedule</h2>
                <a href="{{ route('admin.bookings.calendar') }}" class="text-sm font-medium text-primary-600 hover:text-primary-700">Open calendar</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($upcomingBookings as $booking)
                    @php
                        $badgeClass = match ($booking->status) {
                            'confirmed' => 'bg-emerald-100 text-emerald-700',
                            'pending' => 'bg-amber-100 text-amber-700',
                            'completed' => 'bg-cyan-100 text-cyan-700',
                            'cancelled' => 'bg-rose-100 text-rose-700',
                            default => 'bg-slate-100 text-slate-700',
                        };
                    @endphp
                    <a href="{{ route('admin.bookings.show', $booking) }}" class="block px-5 py-4 hover:bg-slate-50 transition">
                        <div class="flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate font-medium text-slate-900">{{ $booking->customer_name }}</p>
                                <p class="truncate text-sm text-slate-600">{{ $booking->offer->title ?? 'Offer' }}</p>
                            </div>
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $badgeClass }}">{{ ucfirst($booking->status) }}</span>
                        </div>
                        <p class="mt-2 text-xs text-slate-500">{{ $booking->booking_date->format('M j, Y') }} at {{ $booking->booking_time }}</p>
                    </a>
                @empty
                    <p class="px-5 py-6 text-sm text-slate-600">No upcoming bookings.</p>
                @endforelse
            </div>
        </div>
    </section>
</div>
@endsection
