@extends('layouts.app')

@section('title', 'Terms of Service - ' . config('app.name'))

@section('content')
@php
    $businessName = $branding['hero_name'] ?? config('app.name');
    $contactEmail = $branding['email'] ?? config('mail.from.address');
@endphp
<div class="container mx-auto px-4 max-w-3xl">
    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-primary-600 to-accent-600 p-8 sm:p-10 text-white">
            <h1 class="text-2xl sm:text-3xl font-bold mb-2">Terms of Service</h1>
            <p class="text-white/90">Last updated: July 6, 2026</p>
        </div>

        <div class="p-6 sm:p-10 prose prose-slate max-w-none prose-h2:text-xl prose-h2:font-semibold prose-a:text-primary-600">
            <p>
                These Terms of Service ("Terms") govern your use of {{ config('app.name') }} to book sessions
                with {{ $businessName }}. By booking a session, you agree to these Terms.
            </p>

            <h2>Bookings and payment</h2>
            <p>
                When you book a session, you agree to pay the price shown at the time of booking. Payment is
                collected securely through Stripe when you complete checkout. Your booking is only confirmed
                once payment has succeeded.
            </p>

            <h2>Cancellations and rescheduling</h2>
            <p>
                You may cancel or request to reschedule your booking up to 24 hours before the scheduled start
                time. Cancellations made after that window may not be eligible for a refund or reschedule at
                our discretion.
            </p>

            <h2>Refunds</h2>
            <p>
                If your booking is cancelled by us, or approved for cancellation with a refund, the refund will
                be issued to your original payment method through Stripe. Refunds may take several business
                days to appear on your statement depending on your bank or card issuer.
            </p>

            <h2>No-shows</h2>
            <p>
                If you do not attend a confirmed session without cancelling in advance, the session will be
                treated as completed and is not eligible for a refund.
            </p>

            <h2>Reviews</h2>
            <p>
                After a completed session, you may receive an email invitation to leave a rating and review.
                Submitted reviews should be honest and respectful. We reserve the right to remove reviews that
                are abusive, fraudulent, or unrelated to the service provided.
            </p>

            <h2>Acceptable use</h2>
            <p>
                You agree to provide accurate booking information and not to use the platform for any unlawful
                purpose, to abuse the booking system, or to attempt to disrupt or gain unauthorized access to
                the platform.
            </p>

            <h2>Limitation of liability</h2>
            <p>
                {{ $businessName }} provides sessions on a good-faith basis and is not liable for indirect or
                consequential losses arising from your use of the platform, to the fullest extent permitted by
                law.
            </p>

            <h2>Changes to these Terms</h2>
            <p>
                We may update these Terms from time to time. Continued use of the platform after changes are
                posted means you accept the revised Terms.
            </p>

            <h2>Contact us</h2>
            <p>
                Questions about these Terms can be sent to
                @if($contactEmail)
                    <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>.
                @else
                    the email address listed on our homepage.
                @endif
            </p>
        </div>
    </div>
</div>
@endsection
