@extends('layouts.app')

@section('title', 'Book ' . $offer->title)

@section('content')
<div class="container mx-auto px-4 max-w-3xl">
    @include('bookings.partials.steps', ['step' => 1])

    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-primary-600 to-accent-600 p-6 sm:p-8 text-white">
            <p class="text-xs uppercase tracking-wider text-white/80 mb-1">You're booking</p>
            <h1 class="text-2xl sm:text-3xl font-bold mb-2">{{ $offer->title }}</h1>
            <p class="text-white/90 max-w-xl">{{ $offer->description }}</p>
            <div class="flex flex-wrap gap-3 mt-4">
                <span class="inline-flex items-center gap-1.5 bg-white/15 rounded-full px-3 py-1 text-sm font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $offer->formatted_duration }}
                </span>
                <span class="inline-flex items-center gap-1.5 bg-white/15 rounded-full px-3 py-1 text-sm font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2.21 0-4 .895-4 2s1.79 2 4 2 4 .895 4 2-1.79 2-4 2m0-10c1.657 0 3 1.343 3 3M12 8V6m0 12v-2m0 0c-1.657 0-3-1.343-3-3"/></svg>
                    {{ $offer->formatted_price }}
                </span>
            </div>
        </div>

        <div class="p-6 sm:p-8">
            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg text-red-700 text-sm">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('bookings.store', $offer) }}" class="space-y-6">
                @csrf
                <div>
                    <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-3">When</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="booking_date">Date</label>
                            <input type="date" id="booking_date" name="booking_date" value="{{ old('booking_date') }}" required class="input-field">
                            @error('booking_date')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="booking_time">Time</label>
                            <input type="time" id="booking_time" name="booking_time" value="{{ old('booking_time') }}" required class="input-field">
                            @error('booking_time')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <div>
                    <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-3">Your details</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="customer_name">Name</label>
                            <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required class="input-field">
                            @error('customer_name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="customer_email">Email</label>
                            <input type="email" id="customer_email" name="customer_email" value="{{ old('customer_email') }}" required class="input-field">
                            @error('customer_email')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1" for="customer_phone">Phone (optional)</label>
                        <input type="text" id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" class="input-field">
                        @error('customer_phone')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1" for="notes">Notes (optional)</label>
                        <textarea id="notes" name="notes" rows="3" class="input-field">{{ old('notes') }}</textarea>
                        @error('notes')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <button type="submit" class="btn-primary w-full flex items-center justify-center gap-2">
                    Continue to payment
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
