@extends('layouts.app')

@section('title', 'Booking Confirmation')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-xl shadow-md p-8 space-y-3">
    <h1 class="text-2xl font-semibold">Your booking is confirmed</h1>
    <p>Thank you! Here are your details:</p>
    <ul class="list-disc list-inside text-gray-700 space-y-1">
        <li>Booking ID: {{ $booking->id }}</li>
        <li>Date: {{ $booking->booking_date }}</li>
        <li>Time: {{ $booking->booking_time }}</li>
        <li>Status: {{ ucfirst($booking->status) }}</li>
        <li>Payment: {{ ucfirst($booking->payment_status) }}</li>
    </ul>
    <a href="{{ route('home') }}" class="btn-primary inline-block mt-4">Back to home</a>
</div>
@endsection
