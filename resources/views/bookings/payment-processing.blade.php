@extends('layouts.app')

@section('title', 'Confirming Payment')

@section('content')
<div class="max-w-xl mx-auto bg-white rounded-xl shadow p-8 text-center">
    <h1 class="text-2xl font-semibold mb-3">Confirming your booking</h1>
    <p class="text-gray-600 mb-6">This usually takes a few seconds. Please don't close this page.</p>

    <div class="mb-6">
        <div class="inline-block">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
        </div>
    </div>

    <p id="status-message" class="text-gray-500 text-sm mb-4">Verifying payment…</p>

    <div id="error-section" class="hidden mt-6 p-4 bg-red-50 rounded-lg border border-red-200">
        <p class="text-red-700 font-semibold mb-3">Payment confirmation is taking longer than expected</p>
        <p class="text-red-600 text-sm mb-4">Your payment was successful, but we're still confirming your booking. You can:</p>
        <div class="flex flex-col gap-2">
            <a href="{{ route('payment.status') }}?payment_intent={{ $paymentIntentId }}"
               class="btn-primary inline-block">Check Status</a>
            <a href="{{ route('home') }}"
               class="btn-secondary inline-block">Return Home</a>
        </div>
        <p class="text-red-600 text-xs mt-3">We'll send you a confirmation email when booking is confirmed.</p>
    </div>

    <p class="text-xs text-gray-400 mt-6">Reference: {{ substr($paymentIntentId, -8) }}</p>
</div>
@endsection

@push('scripts')
<script>
const paymentIntentId = @json($paymentIntentId);
const statusEndpoint = '{{ route('payment.status') }}' + '?payment_intent=' + encodeURIComponent(paymentIntentId);
const timeoutSeconds = 20;
const initialPollInterval = 2000; // 2 seconds
const maxPollInterval = 5000; // 5 seconds

let pollCount = 0;
let pollInterval = initialPollInterval;
let startTime = Date.now();

const updateMessage = (message) => {
    const el = document.getElementById('status-message');
    if (el) el.textContent = message;
};

const showError = () => {
    const errorSection = document.getElementById('error-section');
    if (errorSection) {
        errorSection.classList.remove('hidden');
        updateMessage('Taking longer than expected…');
    }
};

const pollStatus = async () => {
    const elapsedSeconds = Math.floor((Date.now() - startTime) / 1000);

    try {
        const response = await fetch(statusEndpoint, {
            headers: { 'Accept': 'application/json' }
        });

        if (!response.ok) {
            if (elapsedSeconds >= timeoutSeconds) {
                showError();
            }
            return;
        }

        const data = await response.json();

        if (data.status === 'succeeded' && data.confirmation_url) {
            window.location.href = data.confirmation_url;
            return;
        }

        if (data.status === 'failed') {
            window.location.href = '{{ route('payment.cancel') }}?payment_intent=' + encodeURIComponent(paymentIntentId);
            return;
        }

        // Update message based on time elapsed
        if (elapsedSeconds < 5) {
            updateMessage('Verifying payment…');
        } else if (elapsedSeconds < 10) {
            updateMessage('Almost there…');
        } else if (elapsedSeconds < timeoutSeconds) {
            updateMessage('Taking a moment…');
        } else {
            showError();
            return;
        }

        // Backoff polling interval
        if (pollCount > 3) {
            pollInterval = maxPollInterval;
        }

        pollCount++;
        setTimeout(pollStatus, pollInterval);
    } catch (e) {
        console.error('Status check failed:', e);

        if (elapsedSeconds >= timeoutSeconds) {
            showError();
        } else {
            setTimeout(pollStatus, pollInterval);
        }
    }
};

// Start polling immediately
pollStatus();
</script>
@endpush
