@extends('layouts.admin')

@section('title', 'Manage Bookings')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Bookings</h1>
            <p class="text-gray-600">View and manage customer bookings.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.bookings.calendar') }}" class="btn-secondary">Calendar</a>
        </div>
    </div>

    <form method="GET" class="bg-white rounded-xl shadow p-4 grid grid-cols-1 md:grid-cols-5 gap-3">
        <div>
            <label class="text-sm text-gray-600">Status</label>
            <select name="status" class="w-full border border-gray-200 rounded-lg p-2.5">
                <option value="">All</option>
                @foreach(['pending','confirmed','completed','cancelled'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-sm text-gray-600">From</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full border border-gray-200 rounded-lg p-2.5">
        </div>
        <div>
            <label class="text-sm text-gray-600">To</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full border border-gray-200 rounded-lg p-2.5">
        </div>
        <div class="md:col-span-2">
            <label class="text-sm text-gray-600">Search (name, email, phone)</label>
            <input type="text" name="search" value="{{ request('search') }}" class="w-full border border-gray-200 rounded-lg p-2.5" placeholder="Search customers">
        </div>
        <div class="md:col-span-5 flex justify-end space-x-2">
            <a href="{{ route('admin.bookings.index') }}" class="btn-secondary">Reset</a>
            <button type="submit" class="btn-primary">Filter</button>
        </div>
    </form>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Offer</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Amount</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($bookings as $booking)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900">{{ $booking->customer_name }}</div>
                                <div class="text-sm text-gray-600">{{ $booking->customer_email }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $booking->offer->title ?? 'Offer' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ $booking->booking_date->format('M j, Y') }}<br>
                                <span class="text-gray-500">{{ $booking->booking_time }} - {{ $booking->end_time }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 capitalize">{{ $booking->status }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">${{ number_format($booking->total_amount, 2) }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.bookings.show', $booking) }}" class="text-primary-600 hover:text-primary-700 text-sm">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-gray-600">No bookings found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $bookings->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
