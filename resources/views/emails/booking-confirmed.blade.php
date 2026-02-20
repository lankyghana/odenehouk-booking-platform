<h1>Your booking is confirmed</h1>
<p>Hello {{ $booking->customer_name }},</p>
<p>Your booking for {{ $booking->offer->title ?? 'your selected service' }} on {{ $booking->booking_date->format('M d, Y') }} at {{ $booking->booking_time }} has been confirmed.</p>
<p>Thank you.</p>
