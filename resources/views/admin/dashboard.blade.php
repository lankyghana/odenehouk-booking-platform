@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">
    <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8 mb-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Admin Overview</p>
                <h1 class="mt-2 text-2xl font-semibold text-slate-900">Welcome back, Admin</h1>
                <p class="mt-2 max-w-2xl text-sm text-slate-600">Here is a quick view of booking activity, revenue, and customer growth.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.bookings.index') }}" class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-slate-800">Manage bookings</a>
                <a href="{{ route('home') }}" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">View site</a>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-3">
            <article class="rounded-xl border border-slate-200 bg-slate-50 p-5 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Today bookings</p>
                        <p class="mt-2 text-3xl font-bold text-slate-900">{{ $stats['today_bookings'] }}</p>
                    </div>
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-white text-slate-500 shadow-sm">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10m-12 9h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v11a2 2 0 002 2z"/>
                        </svg>
                    </span>
                </div>
            </article>
            <article class="rounded-xl border border-slate-200 bg-slate-50 p-5 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Monthly revenue</p>
                        <p class="mt-2 text-3xl font-bold text-slate-900">${{ number_format($stats['month_revenue'], 2) }}</p>
                    </div>
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-white text-slate-500 shadow-sm">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-2.21 0-4 .895-4 2s1.79 2 4 2 4 .895 4 2-1.79 2-4 2m0-10c1.657 0 3 1.343 3 3M12 8V6m0 12v-2m0 0c-1.657 0-3-1.343-3-3"/>
                        </svg>
                    </span>
                </div>
            </article>
            <article class="rounded-xl border border-slate-200 bg-slate-50 p-5 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Total customers</p>
                        <p class="mt-2 text-3xl font-bold text-slate-900">{{ $stats['total_customers'] }}</p>
                    </div>
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-white text-slate-500 shadow-sm">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-1a4 4 0 00-5-3.87M17 20H7m10 0v-1c0-1.657-1.343-3-3-3h-4c-1.657 0-3 1.343-3 3v1m0 0H2v-1a4 4 0 015-3.87M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </span>
                </div>
            </article>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4 mb-8">
        <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md">
            <p class="text-sm text-slate-500">Total bookings</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ $stats['total_bookings'] }}</p>
        </article>
        <article class="rounded-xl border border-amber-200 bg-amber-50 p-5 shadow-sm transition hover:shadow-md">
            <p class="text-sm text-amber-700">Pending bookings</p>
            <p class="mt-2 text-3xl font-bold text-amber-800">{{ $stats['pending_bookings'] }}</p>
        </article>
        <article class="rounded-xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm transition hover:shadow-md">
            <p class="text-sm text-emerald-700">Confirmed bookings</p>
            <p class="mt-2 text-3xl font-bold text-emerald-800">{{ $stats['confirmed_bookings'] }}</p>
        </article>
        <article class="rounded-xl border border-cyan-200 bg-cyan-50 p-5 shadow-sm transition hover:shadow-md">
            <p class="text-sm text-cyan-700">Active offers</p>
            <p class="mt-2 text-3xl font-bold text-cyan-900">{{ $stats['active_offers'] }}</p>
        </article>
        <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md md:col-span-2">
            <p class="text-sm text-slate-500">Revenue this month</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">${{ number_format($stats['month_revenue'], 2) }}</p>
        </article>
        <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md md:col-span-2">
            <p class="text-sm text-slate-500">Total revenue</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">${{ number_format($stats['total_revenue'], 2) }}</p>
        </article>
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm" x-data="{ tab: 'all' }">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <h2 class="text-lg font-medium text-slate-900">Recent bookings</h2>
                <a href="{{ route('admin.bookings.index') }}" class="text-sm font-medium text-slate-700 hover:text-slate-900">View all</a>
            </div>
            <div class="flex items-center gap-2 border-b border-slate-100 px-5 py-3 text-sm">
                <button type="button" @click="tab = 'all'" :class="tab === 'all' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="rounded-full px-3 py-1.5 transition">All</button>
                <button type="button" @click="tab = 'confirmed'" :class="tab === 'confirmed' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="rounded-full px-3 py-1.5 transition">Confirmed</button>
                <button type="button" @click="tab = 'pending'" :class="tab === 'pending' ? 'bg-amber-500 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="rounded-full px-3 py-1.5 transition">Pending</button>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($recentBookings as $booking)
                    @php
                        $badgeClass = match ($booking->status) {
                            'confirmed' => 'bg-emerald-100 text-emerald-700',
                            'pending' => 'bg-amber-100 text-amber-700',
                            default => 'bg-slate-100 text-slate-700',
                        };
                    @endphp
                    <a x-show="tab === 'all' || tab === '{{ $booking->status }}'" href="{{ route('admin.bookings.show', $booking) }}" class="block px-5 py-4 transition hover:bg-slate-50">
                        <div class="flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-slate-900">{{ $booking->customer_name }}</p>
                                <p class="truncate text-sm text-slate-500">{{ $booking->offer->title ?? 'Offer' }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $booking->booking_date->format('M j, Y') }} at {{ $booking->booking_time }}</p>
                            </div>
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $badgeClass }}">{{ ucfirst($booking->status) }}</span>
                        </div>
                    </a>
                @empty
                    <p class="px-5 py-6 text-sm text-slate-600">No bookings yet.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <h2 class="text-lg font-medium text-slate-900">Upcoming schedule</h2>
                <a href="{{ route('admin.bookings.calendar') }}" class="text-sm font-medium text-slate-700 hover:text-slate-900">Open calendar</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($upcomingBookings as $booking)
                    @php
                        $badgeClass = match ($booking->status) {
                            'confirmed' => 'bg-emerald-100 text-emerald-700',
                            'pending' => 'bg-amber-100 text-amber-700',
                            default => 'bg-slate-100 text-slate-700',
                        };
                    @endphp
                    <a href="{{ route('admin.bookings.show', $booking) }}" class="block px-5 py-4 transition hover:bg-slate-50">
                        <div class="flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-slate-900">{{ $booking->customer_name }}</p>
                                <p class="truncate text-sm text-slate-500">{{ $booking->offer->title ?? 'Offer' }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $booking->booking_date->format('M j, Y') }} at {{ $booking->booking_time }}</p>
                            </div>
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $badgeClass }}">{{ ucfirst($booking->status) }}</span>
                        </div>
                    </a>
                @empty
                    <p class="px-5 py-6 text-sm text-slate-600">No upcoming bookings.</p>
                @endforelse
            </div>
        </div>
    </section>
</div>
@endsection
