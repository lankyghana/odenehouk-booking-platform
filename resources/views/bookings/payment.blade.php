@extends('layouts.app')

@section('title', 'Complete Payment')

@section('content')
<div class="container mx-auto px-4 max-w-2xl">
    @include('bookings.partials.steps', ['step' => 2])

    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
        <div class="p-6 sm:p-8 border-b border-gray-100">
            <h1 class="text-2xl font-bold text-gray-900 mb-4">Complete your payment</h1>
            <div class="bg-gray-50 rounded-xl p-4 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Service</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $offer->title }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Date &amp; time</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $booking->booking_date->format('M j, Y') }} at {{ \Carbon\Carbon::parse($booking->booking_time)->format('g:i A') }}</span>
                </div>
                <div class="flex items-center justify-between pt-2 border-t border-gray-200">
                    <span class="text-sm font-medium text-gray-700">Total</span>
                    <span class="text-xl font-bold text-primary-600">${{ number_format($booking->total_amount, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="p-6 sm:p-8">
            <label class="block text-sm font-medium text-gray-700 mb-2">Card details</label>
            <div id="card-element" class="input-field"></div>
            <div id="card-errors" class="text-red-600 text-sm mt-2" role="alert"></div>

            <button id="pay-btn" type="button" class="btn-primary w-full mt-6 flex items-center justify-center gap-2">
                <svg id="pay-btn-spinner" class="hidden animate-spin h-5 w-5 text-white" viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                <span id="pay-btn-label">Pay ${{ number_format($booking->total_amount, 2) }}</span>
            </button>

            <p class="flex items-center justify-center gap-1.5 text-xs text-gray-500 mt-4">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Payments are securely processed by Stripe. We never see or store your card details.
            </p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
const stripe = Stripe('{{ $stripeKey }}');
const elements = stripe.elements();
const card = elements.create('card', {
    style: {
        base: {
            fontSize: '16px',
            color: '#111827',
            '::placeholder': { color: '#9ca3af' },
        },
    },
});
card.mount('#card-element');

const payBtn = document.getElementById('pay-btn');
const payBtnLabel = document.getElementById('pay-btn-label');
const payBtnSpinner = document.getElementById('pay-btn-spinner');
const cardErrors = document.getElementById('card-errors');
const paymentIntentId = '{{ $clientSecret }}'.split('_secret')[0];

const setLoading = (loading) => {
    payBtn.disabled = loading;
    payBtnSpinner.classList.toggle('hidden', !loading);
    payBtnLabel.textContent = loading ? 'Processing…' : 'Pay ${{ number_format($booking->total_amount, 2) }}';
};

payBtn.addEventListener('click', async () => {
    setLoading(true);
    cardErrors.textContent = '';
    const { error } = await stripe.confirmCardPayment('{{ $clientSecret }}', {
        payment_method: { card }
    });
    if (error) {
        cardErrors.textContent = error.message;
        setLoading(false);
    } else {
        window.location.href = '{{ route('payment.success') }}' + '?payment_intent=' + paymentIntentId;
    }
});
</script>
@endpush
