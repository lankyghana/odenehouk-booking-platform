<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class ReviewController extends Controller
{
    /**
     * Show review form
     */
    public function show(string $token)
    {
        try {
            $booking = Booking::where('review_token', $token)->firstOrFail();

            // Check if already reviewed
            if ($booking->review) {
                return view('reviews.already-reviewed', [
                    'booking' => $booking,
                ]);
            }

            return view('reviews.form', [
                'booking' => $booking,
                'token' => $token,
            ]);
        } catch (Throwable $e) {
            Log::warning('review.invalid_token', [
                'token' => $token,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('home')
                ->with('error', 'Invalid or expired review link.');
        }
    }

    /**
     * Submit review
     */
    public function store(Request $request, string $token)
    {
        try {
            $booking = Booking::where('review_token', $token)->firstOrFail();

            // Prevent duplicate reviews
            if ($booking->review) {
                Log::warning('review.duplicate_attempt', [
                    'booking_id' => $booking->id,
                    'token' => $token,
                ]);

                return redirect()->route('review.show', ['token' => $token])
                    ->with('info', 'You have already submitted a review for this booking.');
            }

            $validated = $request->validate([
                'rating' => ['required', 'integer', 'min:1', 'max:5'],
                'comment' => ['nullable', 'string', 'max:1000'],
            ]);

            Review::create([
                'booking_id' => $booking->id,
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
            ]);

            Log::info('review.submitted', [
                'booking_id' => $booking->id,
                'rating' => $validated['rating'],
                'has_comment' => !empty($validated['comment']),
            ]);

            return view('reviews.success', [
                'booking' => $booking,
            ]);
        } catch (Throwable $e) {
            Log::error('review.submission_failed', [
                'token' => $token,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('home')
                ->with('error', 'Unable to submit review. Please try again.');
        }
    }
}
