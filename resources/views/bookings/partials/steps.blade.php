@php
    $stepNames = ['Details', 'Payment', 'Confirming', 'Confirmed'];
@endphp
<nav aria-label="Checkout progress" class="max-w-2xl mx-auto mb-8">
    <ol class="grid grid-cols-4 gap-2 sm:gap-4">
        @foreach($stepNames as $index => $label)
            @php $number = $index + 1; @endphp
            <li>
                <div class="h-1.5 rounded-full {{ $number <= $step ? 'bg-primary-600' : 'bg-gray-200' }}"></div>
                <p class="mt-2 text-[11px] sm:text-xs font-medium truncate {{ $number <= $step ? 'text-gray-900' : 'text-gray-400' }}">
                    <span class="hidden sm:inline">{{ $number }}. </span>{{ $label }}
                </p>
            </li>
        @endforeach
    </ol>
</nav>
