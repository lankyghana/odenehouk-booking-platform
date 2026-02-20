@extends('layouts.app')

@section('title', 'Complete Payment')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-xl shadow-md p-8 space-y-4">
    <h1 class="text-2xl font-semibold">Complete your payment</h1>
    <p>Booking for <strong>{{ $offer->title }}</strong> on <strong>{{ $booking->booking_date }}</strong> at <strong>{{ $booking->booking_time }}</strong>.</p>
    <p class="text-lg font-semibold">Amount: ${{ number_format($booking->total_amount, 2) }}</p>

    <div id="card-element" class="p-4 border rounded"></div>
    <button id="pay-btn" class="btn-primary mt-4">Pay now</button>

    <div id="card-errors" class="text-red-600 mt-2"></div>
</div>
@endsection

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
const stripe = Stripe('{{ $stripeKey }}');
const elements = stripe.elements();
const card = elements.create('card');
card.mount('#card-element');
const payBtn = document.getElementById('pay-btn');
const cardErrors = document.getElementById('card-errors');
const paymentIntentId = '{{ $clientSecret }}'.split('_secret')[0];

payBtn.addEventListener('click', async () => {
    payBtn.disabled = true;
    cardErrors.textContent = '';
    const { error } = await stripe.confirmCardPayment('{{ $clientSecret }}', {
        payment_method: { card }
    });
    if (error) {
        cardErrors.textContent = error.message;
        payBtn.disabled = false;
    } else {
        window.location.href = '{{ route('payment.success') }}' + '?payment_intent=' + paymentIntentId;
    }
});
</script>
@endpush
