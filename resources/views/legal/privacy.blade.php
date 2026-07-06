@extends('layouts.app')

@section('title', 'Privacy Policy - ' . config('app.name'))

@section('content')
@php
    $businessName = $branding['hero_name'] ?? config('app.name');
    $contactEmail = $branding['email'] ?? config('mail.from.address');
@endphp
<div class="container mx-auto px-4 max-w-3xl">
    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-primary-600 to-accent-600 p-8 sm:p-10 text-white">
            <h1 class="text-2xl sm:text-3xl font-bold mb-2">Privacy Policy</h1>
            <p class="text-white/90">Last updated: July 6, 2026</p>
        </div>

        <div class="p-6 sm:p-10 prose prose-slate max-w-none prose-h2:text-xl prose-h2:font-semibold prose-a:text-primary-600">
            <p>
                This Privacy Policy explains how {{ $businessName }} ("we", "us", "our") collects, uses,
                and protects your information when you book a session through {{ config('app.name') }}.
            </p>

            <h2>Information we collect</h2>
            <p>When you make a booking, we collect:</p>
            <ul>
                <li>Your name, email address, and phone number (if provided)</li>
                <li>Booking details such as the service, date, time, and any notes you add</li>
                <li>Payment confirmation details from Stripe, our payment processor</li>
            </ul>
            <p>
                We do not collect or store your full card number, expiry date, or CVC. Card details are
                entered directly into Stripe's secure payment form and never pass through our servers.
            </p>

            <h2>How we use your information</h2>
            <ul>
                <li>To create and manage your booking</li>
                <li>To send booking confirmations, reminders, and receipts by email</li>
                <li>To respond to support requests related to your booking</li>
                <li>To invite you to leave a review after your session</li>
            </ul>

            <h2>Payment processing</h2>
            <p>
                Payments are processed by <a href="https://stripe.com/privacy" target="_blank" rel="noopener">Stripe</a>,
                a PCI-DSS compliant payment processor. Stripe's own privacy policy governs how they handle your
                payment information.
            </p>

            <h2>Cookies and session data</h2>
            <p>
                We use a session cookie to keep you signed in and to verify that you're the person who made a
                booking when viewing its confirmation page. We do not use third-party advertising or tracking
                cookies.
            </p>

            <h2>Data retention</h2>
            <p>
                We retain booking and payment records for as long as needed to provide our services and to
                meet accounting and legal obligations.
            </p>

            <h2>Sharing your information</h2>
            <p>
                We do not sell your personal information. We only share it with service providers who help us
                run the platform, such as our payment processor (Stripe) and email delivery provider, and only
                to the extent necessary to provide our services.
            </p>

            <h2>Your rights</h2>
            <p>
                You may request a copy of the information we hold about you, or ask us to correct or delete it,
                by contacting us at
                @if($contactEmail)
                    <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>.
                @else
                    the email address listed on our homepage.
                @endif
            </p>

            <h2>Children's privacy</h2>
            <p>Our services are not directed at individuals under the age of 18.</p>

            <h2>Changes to this policy</h2>
            <p>
                We may update this Privacy Policy from time to time. Changes will be posted on this page with
                an updated "Last updated" date.
            </p>

            <h2>Contact us</h2>
            <p>
                If you have questions about this Privacy Policy,
                @if($contactEmail)
                    email us at <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>.
                @else
                    please reach out via the contact details on our homepage.
                @endif
            </p>
        </div>
    </div>
</div>
@endsection
