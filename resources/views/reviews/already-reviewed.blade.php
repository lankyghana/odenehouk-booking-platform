@extends('layouts.app')

@section('title', 'Review Already Submitted')

@section('content')
<div class="max-w-xl mx-auto bg-white rounded-xl shadow-md p-8 text-center">
    <div class="mb-4">
        <svg class="w-16 h-16 text-blue-500 mx-auto" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-11-1a1 1 0 11-2 0 1 1 0 012 0z" clip-rule="evenodd"/>
        </svg>
    </div>

    <h1 class="text-2xl font-semibold mb-2">Review Already Submitted</h1>
    <p class="text-gray-600 mb-6">You have already submitted a review for this booking.</p>

    <div class="mb-6 p-4 bg-gray-50 rounded-lg text-left">
        <p class="text-sm text-gray-600 mb-1">Your Rating</p>
        <div class="flex gap-1 mb-2">
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
            <span class="ml-2 font-semibold">{{ $booking->review->rating }}/5</span>
        </div>
        <p class="text-xs text-gray-500">Submitted on {{ $booking->review->created_at->format('M d, Y') }}</p>
        @if ($booking->review->comment)
            <p class="text-sm text-gray-700 mt-3 italic">"{{ $booking->review->comment }}"</p>
        @endif
    </div>

    <p class="text-sm text-gray-600 mb-6">Thank you for sharing your feedback!</p>

    <a href="{{ route('home') }}" class="btn-primary inline-block">Return Home</a>
</div>
@endsection
