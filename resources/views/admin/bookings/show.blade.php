@extends('layouts.admin')

@section('title', 'Booking Details')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Booking #{{ $booking->id }}</h1>
            <p class="text-gray-600">{{ $booking->customer_name }} — {{ $booking->customer_email }}</p>
        </div>
        <a href="{{ route('admin.bookings.index') }}" class="text-primary-600 hover:text-primary-700 text-sm">Back to bookings</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-xl shadow p-5">
                <h2 class="text-lg font-semibold text-gray-900 mb-3">Booking Info</h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm text-gray-700">
                    <div><dt class="text-gray-500">Offer</dt><dd class="font-medium text-gray-900">{{ $booking->offer->title ?? 'Offer' }}</dd></div>
                    <div><dt class="text-gray-500">Status</dt><dd class="font-medium capitalize">{{ $booking->status }}</dd></div>
                    <div><dt class="text-gray-500">Date</dt><dd>{{ $booking->booking_date->format('M j, Y') }}</dd></div>
                    <div><dt class="text-gray-500">Time</dt><dd>{{ $booking->booking_time }} - {{ $booking->end_time }}</dd></div>
                    <div><dt class="text-gray-500">Amount</dt><dd>${{ number_format($booking->total_amount, 2) }}</dd></div>
                    <div><dt class="text-gray-500">Phone</dt><dd>{{ $booking->customer_phone }}</dd></div>
                </dl>
            </div>

            <div class="bg-white rounded-xl shadow p-5 space-y-4">
                <h2 class="text-lg font-semibold text-gray-900">Update Status</h2>
                <form action="{{ route('admin.bookings.update-status', $booking) }}" method="POST" class="flex flex-col md:flex-row md:items-center md:space-x-3 space-y-3 md:space-y-0">
                    @csrf
                    <select name="status" class="border border-gray-200 rounded-lg p-2.5">
                        @foreach(['pending','confirmed','completed','cancelled'] as $status)
                            <option value="{{ $status }}" @selected($booking->status === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn-primary">Save</button>
                </form>
            </div>

            <div class="bg-white rounded-xl shadow p-5 space-y-4">
                <h2 class="text-lg font-semibold text-gray-900">Reschedule</h2>
                <form action="{{ route('admin.bookings.reschedule', $booking) }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    @csrf
                    <div>
                        <label class="text-sm text-gray-600">New Date</label>
                        <input type="date" name="new_date" class="w-full border border-gray-200 rounded-lg p-2.5" required>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">New Time</label>
                        <input type="time" name="new_time" class="w-full border border-gray-200 rounded-lg p-2.5" required>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="btn-secondary w-full">Reschedule</button>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-xl shadow p-5 space-y-4">
                <h2 class="text-lg font-semibold text-gray-900">Cancel Booking</h2>
                <form action="{{ route('admin.bookings.cancel', $booking) }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="text-sm text-gray-600">Reason</label>
                        <textarea name="cancellation_reason" class="w-full border border-gray-200 rounded-lg p-2.5" rows="3" required></textarea>
                    </div>
                    <label class="inline-flex items-center space-x-2">
                        <input type="checkbox" name="issue_refund" value="1" class="rounded border-gray-300">
                        <span class="text-sm text-gray-700">Issue refund if payment succeeded</span>
                    </label>
                    <button type="submit" class="btn-danger">Cancel booking</button>
                </form>
            </div>
        </div>

        <div class="space-y-4">
            <div class="bg-white rounded-xl shadow p-5">
                <h2 class="text-lg font-semibold text-gray-900 mb-3">Payment</h2>
                @if($booking->payment)
                    <dl class="space-y-2 text-sm text-gray-700">
                        <div class="flex justify-between"><span>Status</span><span class="capitalize">{{ $booking->payment->status }}</span></div>
                        <div class="flex justify-between"><span>Amount</span><span>${{ number_format($booking->payment->amount, 2) }}</span></div>
                        <div class="flex justify-between"><span>Method</span><span>{{ $booking->payment->provider ?? 'Stripe' }}</span></div>
                        <div class="flex justify-between"><span>Reference</span><span>{{ $booking->payment->provider_reference ?? 'N/A' }}</span></div>
                    </dl>
                @else
                    <p class="text-sm text-gray-600">No payment record.</p>
                @endif
            </div>

            <div class="bg-white rounded-xl shadow p-5">
                <h2 class="text-lg font-semibold text-gray-900 mb-3">Customer</h2>
                <p class="font-medium text-gray-900">{{ $booking->customer_name }}</p>
                <p class="text-sm text-gray-700">{{ $booking->customer_email }}</p>
                <p class="text-sm text-gray-700">{{ $booking->customer_phone }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
