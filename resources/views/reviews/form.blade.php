@extends('layouts.app')

@section('title', 'Leave a Review')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-xl shadow-md p-8">
    <h1 class="text-2xl font-semibold mb-2">Leave a Review</h1>
    <p class="text-gray-600 mb-6">Share your experience with this booking</p>

    <!-- Booking Details -->
    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-gray-600">Date</p>
                <p class="font-semibold">{{ $booking->booking_date->format('M d, Y') }}</p>
            </div>
            <div>
                <p class="text-gray-600">Time</p>
                <p class="font-semibold">{{ \Carbon\Carbon::parse($booking->booking_time)->format('g:i A') }}</p>
            </div>
            <div>
                <p class="text-gray-600">Service</p>
                <p class="font-semibold">{{ $booking->offer->title ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-gray-600">Amount</p>
                <p class="font-semibold">${{ number_format($booking->total_amount, 2) }}</p>
            </div>
        </div>
    </div>

    <!-- Review Form -->
    <form action="{{ route('review.store', ['token' => $token]) }}" method="POST" class="space-y-6">
        @csrf

        <!-- Star Rating -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">Rating *</label>
            <div class="flex gap-2">
                @for ($i = 1; $i <= 5; $i++)
                    <label class="cursor-pointer group">
                        <input type="radio" name="rating" value="{{ $i }}" class="sr-only peer"
                            @if ($i == 5) checked @endif>
                        <svg class="w-8 h-8 text-gray-300 group-hover:text-yellow-400 peer-checked:text-yellow-400 transition"
                             fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </label>
                @endfor
            </div>
            @error('rating')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Comment -->
        <div>
            <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">
                Comment (Optional)
            </label>
            <textarea id="comment" name="comment" rows="4" maxlength="1000"
                placeholder="Share your feedback... (max 1000 characters)"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent resize-none">{{ old('comment') }}</textarea>
            @error('comment')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Character Count -->
        <div class="text-right text-sm text-gray-500">
            <span id="char-count">0</span> / 1000 characters
        </div>

        <!-- Submit -->
        <div class="flex gap-4 pt-4">
            <a href="{{ route('home') }}" class="btn-secondary flex-1">Skip</a>
            <button type="submit" class="btn-primary flex-1">Submit Review</button>
        </div>
    </form>
</div>

<script>
    const textarea = document.getElementById('comment');
    const charCount = document.getElementById('char-count');

    textarea.addEventListener('input', () => {
        charCount.textContent = textarea.value.length;
    });

    // Set initial value for default checked rating
    document.querySelectorAll('input[name="rating"]').forEach(input => {
        if (input.checked) {
            input.dispatchEvent(new Event('change', { bubbles: true }));
        }
    });
</script>
@endsection
