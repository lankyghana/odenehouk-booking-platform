@extends('layouts.admin')

@section('title', 'Bookings Calendar')

@section('content')
<div class="max-w-5xl mx-auto space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Calendar</h1>
            <p class="text-gray-600">A simple placeholder while calendar UI is wired up.</p>
        </div>
        <a href="{{ route('admin.bookings.index') }}" class="text-primary-600 hover:text-primary-700 text-sm">Back to list</a>
    </div>

    <div class="bg-white rounded-xl shadow p-6">
        <p class="text-gray-700">Calendar events endpoint is ready at <code class="font-mono text-sm text-gray-800">{{ route('admin.bookings.calendar.events') }}</code>. Hook up FullCalendar or your preferred UI to consume it.</p>
    </div>
</div>
@endsection
