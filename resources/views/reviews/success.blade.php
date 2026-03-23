@extends('layouts.app')

@section('title', 'Review Submitted')

@section('content')
<div class="max-w-xl mx-auto bg-white rounded-xl shadow-md p-8 text-center">
    <div class="mb-4">
        <svg class="w-16 h-16 text-green-500 mx-auto" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
    </div>

    <h1 class="text-2xl font-semibold mb-2">Thank You for Your Review!</h1>
    <p class="text-gray-600 mb-6">We appreciate your feedback and will use it to improve our services.</p>

    <div class="mb-6 p-4 bg-gray-50 rounded-lg text-left">
        <p class="text-sm text-gray-600 mb-1">Your Rating</p>
        <div class="flex gap-1">
            @for ($i = 0; $i < 5; $i++)
                @if ($i < $booking->review->rating)
                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                @else
                    <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                @endif
            @endfor
        </div>
        <p class="text-lg font-semibold mt-2">{{ $booking->review->rating }}/5 stars</p>
        @if ($booking->review->comment)
            <p class="text-sm text-gray-700 mt-3 italic">"{{ $booking->review->comment }}"</p>
        @endif
    </div>

    <p class="text-sm text-gray-600 mb-6">Check your email for a receipt and booking details.</p>

    <a href="{{ route('home') }}" class="btn-primary inline-block">Return Home</a>
</div>
@endsection
