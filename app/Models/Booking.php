<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'offer_id',
        'booking_date',
        'booking_time',
        'end_time',
        'status',
        'customer_name',
        'customer_email',
        'customer_phone',
        'notes',
        'total_amount',
        'payment_id',
        'payment_status',
        'cancellation_reason',
        'cancelled_at',
        'review_token',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'total_amount' => 'decimal:2',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Get the user that owns the booking
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the offer for this booking
     */
    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    /**
     * Get the payment for this booking
     */
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Get the review for this booking
     */
    public function review()
    {
        return $this->hasOne(Review::class);
    }

    /**
     * Generate and set review token if not already set
     */
    public function generateReviewToken(): string
    {
        if (!$this->review_token) {
            $this->review_token = \Illuminate\Support\Str::uuid();
            $this->save();
        }
        return $this->review_token;
    }

    /**
     * Scope for upcoming bookings
     */
    public function scopeUpcoming($query)
    {
        return $query->where('booking_date', '>=', Carbon::today())
                    ->whereIn('status', ['confirmed', 'pending']);
    }

    /**
     * Scope for past bookings
     */
    public function scopePast($query)
    {
        return $query->where('booking_date', '<', Carbon::today())
                    ->orWhere('status', 'completed');
    }

    /**
     * Scope for confirmed bookings
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Check if booking can be cancelled
     */
    public function canBeCancelled(): bool
    {
        $cancellationHours = config('booking.cancellation_hours', 24);
        $bookingDateTime = Carbon::parse($this->booking_date . ' ' . $this->booking_time);
        $now = Carbon::now();
        
        return $now->diffInHours($bookingDateTime, false) >= $cancellationHours 
               && in_array($this->status, ['confirmed', 'pending']);
    }

    /**
     * Check if booking can be rescheduled
     */
    public function canBeRescheduled(): bool
    {
        return $this->canBeCancelled();
    }

    /**
     * Get formatted date and time
     */
    public function getFormattedDateTimeAttribute(): string
    {
        return $this->booking_date->format('M d, Y') . ' at ' . 
               Carbon::parse($this->booking_time)->format('g:i A');
    }
}
