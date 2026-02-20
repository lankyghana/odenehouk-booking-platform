@extends('layouts.app')

@section('title', 'Processing Payment')

@section('content')
<div class="max-w-xl mx-auto bg-white rounded-xl shadow p-8 text-center">
    <h1 class="text-2xl font-semibold mb-3">Finalizing your payment</h1>
    <p class="text-gray-600 mb-4">We are waiting for secure webhook confirmation from Stripe before confirming your booking.</p>
    <div class="inline-block animate-pulse text-primary-600 font-medium">Checking status...</div>
</div>
@endsection

@push('scripts')
<script>
const paymentIntentId = @json($paymentIntentId);
const statusEndpoint = '{{ route('payment.status') }}' + '?payment_intent=' + encodeURIComponent(paymentIntentId);

const pollStatus = async () => {
    try {
        const response = await fetch(statusEndpoint, { headers: { 'Accept': 'application/json' }});
        if (!response.ok) return;

        const data = await response.json();
        if (data.status === 'succeeded' && data.confirmation_url) {
            window.location.href = data.confirmation_url;
        }
    } catch (e) {
        console.error(e);
    }
};

setInterval(pollStatus, 3000);
pollStatus();
</script>
@endpush
