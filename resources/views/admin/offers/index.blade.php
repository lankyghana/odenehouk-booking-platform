@extends('layouts.admin')

@section('title', 'Manage Offers')

@section('content')
@php use Illuminate\Support\Str; @endphp
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Offers</h1>
            <p class="text-gray-600">Create, update, and monitor offers.</p>
        </div>
        <a href="{{ route('admin.offers.create') }}" class="btn-primary">New Offer</a>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Title</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Price</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Duration</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Bookings</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Status</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($offers as $offer)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900">{{ $offer->title }}</div>
                                <div class="text-sm text-gray-600">{{ Str::limit($offer->description, 80) }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">${{ number_format($offer->price, 2) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $offer->duration_minutes }} mins</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $offer->bookings_count }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                <span class="px-2 py-1 rounded-full text-xs {{ $offer->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-700' }}">{{ $offer->is_active ? 'Active' : 'Inactive' }}</span>
                            </td>
                            <td class="px-4 py-3 text-right space-x-2">
                                <form action="{{ route('admin.offers.toggle-status', $offer) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-sm text-primary-600 hover:text-primary-700">{{ $offer->is_active ? 'Deactivate' : 'Activate' }}</button>
                                </form>
                                <a href="{{ route('admin.offers.show', $offer) }}" class="text-sm text-gray-700 hover:text-primary-700">View</a>
                                <a href="{{ route('admin.offers.edit', $offer) }}" class="text-sm text-gray-700 hover:text-primary-700">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-gray-600">No offers yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $offers->links() }}
        </div>
    </div>
</div>
@endsection
