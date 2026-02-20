@extends('layouts.app')

@section('title', 'Book ' . $offer->title)

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-xl shadow-md p-8 space-y-6">
    <div>
        <h1 class="text-3xl font-semibold mb-2">Book: {{ $offer->title }}</h1>
        <p class="text-gray-600">{{ $offer->description }}</p>
        <p class="text-lg font-semibold mt-2">Price: ${{ number_format($offer->price, 2) }}</p>
        <p class="text-sm text-gray-500">Duration: {{ $offer->duration_minutes }} minutes</p>
    </div>

    @if(session('error'))
        <div class="p-3 bg-red-50 text-red-700 rounded">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('bookings.store', $offer) }}" class="space-y-4">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="booking_date">Date</label>
                <input type="date" id="booking_date" name="booking_date" value="{{ old('booking_date') }}" required class="w-full rounded border-gray-300 focus:ring-primary-500 focus:border-primary-500">
                @error('booking_date')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="booking_time">Time</label>
                <input type="time" id="booking_time" name="booking_time" value="{{ old('booking_time') }}" required class="w-full rounded border-gray-300 focus:ring-primary-500 focus:border-primary-500">
                @error('booking_time')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="customer_name">Name</label>
                <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required class="w-full rounded border-gray-300 focus:ring-primary-500 focus:border-primary-500">
                @error('customer_name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="customer_email">Email</label>
                <input type="email" id="customer_email" name="customer_email" value="{{ old('customer_email') }}" required class="w-full rounded border-gray-300 focus:ring-primary-500 focus:border-primary-500">
                @error('customer_email')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1" for="customer_phone">Phone (optional)</label>
            <input type="text" id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" class="w-full rounded border-gray-300 focus:ring-primary-500 focus:border-primary-500">
            @error('customer_phone')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1" for="notes">Notes</label>
            <textarea id="notes" name="notes" rows="3" class="w-full rounded border-gray-300 focus:ring-primary-500 focus:border-primary-500">{{ old('notes') }}</textarea>
            @error('notes')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="btn-primary">Proceed to payment</button>
    </form>
</div>
@endsection
