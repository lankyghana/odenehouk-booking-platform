<!-- Logo -->
@php
    $appName = config('app.name', 'Booking Platform');
    // Extract first letter or first word initials
    $logoText = strtoupper(substr($appName, 0, 1));
@endphp

<div style="text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #e5e7eb;">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto;">
        <tr>
            <td align="center">
                <!-- Dynamic Gradient Logo Box -->
                <div style="display: inline-block; width: 50px; height: 50px; background: linear-gradient(135deg, #2563eb 0%, #ec4899 100%); border-radius: 8px; text-align: center; line-height: 50px; margin-bottom: 10px;">
                    <span style="color: white; font-weight: bold; font-size: 28px;">{{ $logoText }}</span>
                </div>
                <p style="font-size: 20px; font-weight: bold; margin: 10px 0 0 0; color: #1f2937;">
                    {{ $appName }}
                </p>
            </td>
        </tr>
    </table>
</div>

<h1>Your booking is confirmed</h1>
<p>Hello {{ $booking->customer_name }},</p>
<p>Your booking for {{ $booking->offer->title ?? 'your selected service' }} on {{ $booking->booking_date->format('M d, Y') }} at {{ $booking->booking_time }} has been confirmed.</p>

<h2 style="margin-top: 30px; margin-bottom: 15px;">Booking Details</h2>
<table style="width: 100%; border-collapse: collapse;">
    <tr>
        <td style="padding: 8px; border-bottom: 1px solid #e5e7eb;">
            <strong>Booking ID:</strong>
        </td>
        <td style="padding: 8px; border-bottom: 1px solid #e5e7eb;">
            {{ $booking->id }}
        </td>
    </tr>
    <tr>
        <td style="padding: 8px; border-bottom: 1px solid #e5e7eb;">
            <strong>Amount:</strong>
        </td>
        <td style="padding: 8px; border-bottom: 1px solid #e5e7eb;">
            ${{ number_format($booking->total_amount, 2) }}
        </td>
    </tr>
</table>

@if ($booking->review_token)
<div style="margin-top: 30px; padding: 20px; background-color: #f3f4f6; border-radius: 8px; text-align: center;">
    <h3 style="margin-top: 0;">We'd Love Your Feedback!</h3>
    <p style="margin-bottom: 15px;">Share your experience and help us improve our services.</p>
    <a href="{{ route('review.show', ['token' => $booking->review_token]) }}"
       style="display: inline-block; padding: 10px 20px; background-color: #3b82f6; color: white; text-decoration: none; border-radius: 6px; font-weight: 500;">
        Leave a Review
    </a>
</div>
@endif

<p style="margin-top: 30px; color: #6b7280; font-size: 14px;">Thank you for booking with us!</p>
