@extends('layouts.app')

@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;
@endphp

@section('title', 'Home - ' . config('app.name'))

@section('content')
<div class="container mx-auto px-4">
    <!-- Hero Section -->
    <div class="relative overflow-hidden bg-gradient-to-b from-slate-50 via-white to-slate-100 rounded-3xl p-12 mb-16 flex flex-col items-center text-center gap-6">
        <div class="relative w-48 h-48">
            @php
                $heroImage = $branding['hero_image'] ?? null;
                $heroImageUrl = $heroImage ? (Str::startsWith($heroImage, ['http://', 'https://']) ? $heroImage : Storage::url($heroImage)) : 'https://images.unsplash.com/photo-1544723795-3fb6469f5b39?auto=format&fit=crop&w=600&q=80';
            @endphp
            <img src="{{ $heroImageUrl }}" alt="Profile" class="w-48 h-48 rounded-full object-cover shadow-lg border-4 border-white" />
        </div>
        <div class="space-y-3">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900">{{ $branding['hero_name'] ?? 'Dr Lawrence Amoah' }}</h1>
            <p class="text-lg text-gray-600 max-w-2xl">{{ $branding['hero_title'] ?? 'Coaching & Teaching Creators & Entrepreneurs to Make MONEY Online' }}</p>
            <div class="flex justify-center items-center gap-4 text-gray-600 text-2xl">
                @php
                    $socials = [
                        'instagram' => 'bi-instagram',
                        'tiktok' => 'bi-tiktok',
                        'email' => 'bi-envelope',
                        'facebook' => 'bi-facebook',
                        'youtube' => 'bi-youtube',
                        'linkedin' => 'bi-linkedin',
                    ];
                @endphp
                @foreach($socials as $key => $icon)
                    @if(!empty($branding[$key]))
                        <a href="{{ $key === 'email' ? 'mailto:' . $branding[$key] : $branding[$key] }}" class="hover:text-primary-600 transition" aria-label="{{ ucfirst($key) }}">
                            <i class="bi {{ $icon }}"></i>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
        <div class="text-center p-6 fade-in" style="animation-delay: 0.1s;">
            <div class="w-16 h-16 bg-primary-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold mb-2">Easy Booking</h3>
            <p class="text-gray-600">Select your preferred date and time with our intuitive booking system</p>
        </div>

        <div class="text-center p-6 fade-in" style="animation-delay: 0.2s;">
            <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold mb-2">Instant Confirmation</h3>
            <p class="text-gray-600">Get immediate booking confirmation via email and SMS</p>
        </div>

        <div class="text-center p-6 fade-in" style="animation-delay: 0.3s;">
            <div class="w-16 h-16 bg-accent-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold mb-2">Secure Payment</h3>
            <p class="text-gray-600">Safe and secure payment processing with Stripe</p>
        </div>
    </div>

    <!-- Offers Section -->
    <div id="offers" class="mb-16">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold mb-4">Available Offers</h2>
            <p class="text-xl text-gray-600">Choose the perfect service for your needs</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($offers as $offer)
                <div class="bg-white rounded-2xl shadow-md overflow-hidden card-hover fade-in" style="animation-delay: {{ $loop->index * 0.1 }}s;">
                    @if($offer->image_url)
                        <img src="{{ $offer->image_url }}" alt="{{ $offer->title }}" class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gradient-to-br from-primary-400 via-primary-500 to-accent-500 flex items-center justify-center">
                            <svg class="w-20 h-20 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                    @endif
                    
                    <div class="p-6">
                        @if($offer->category)
                            <span class="inline-block px-3 py-1 text-xs font-semibold text-primary-600 bg-primary-50 rounded-full mb-3">
                                {{ $offer->category }}
                            </span>
                        @endif
                        
                        <h3 class="text-xl font-bold mb-3 text-gray-800">{{ $offer->title }}</h3>
                        <p class="text-gray-600 mb-4 line-clamp-3">{{ $offer->description }}</p>
                        
                        <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-100">
                            <div>
                                <div class="text-3xl font-bold text-primary-600">{{ $offer->formatted_price }}</div>
                                <div class="text-sm text-gray-500">per session</div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-semibold text-gray-700">{{ $offer->formatted_duration }}</div>
                                <div class="text-sm text-gray-500">duration</div>
                            </div>
                        </div>
                        
                        <a href="{{ route('bookings.create', $offer) }}" class="btn-primary w-full text-center block">
                            Book Now →
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-20">
                    <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <p class="text-gray-600 text-lg">No offers available at the moment.</p>
                    <p class="text-gray-500 text-sm mt-2">Please check back later for new offerings.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-gradient-to-r from-primary-600 to-accent-600 rounded-3xl p-12 text-center text-white mb-16">
        <h2 class="text-3xl md:text-4xl font-bold mb-4">Ready to Get Started?</h2>
        <p class="text-xl mb-8 opacity-90">Book your session today and take the first step towards your goals</p>
        <a href="#offers" class="inline-block px-8 py-4 bg-white text-primary-600 rounded-lg font-semibold hover:bg-gray-100 transition-all duration-300 hover:scale-105 shadow-lg">
            View All Offers
        </a>
    </div>

    <!-- Trust Indicators -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center mb-16">
        <div>
            <div class="text-3xl font-bold text-primary-600 mb-2">500+</div>
            <div class="text-gray-600">Happy Clients</div>
        </div>
        <div>
            <div class="text-3xl font-bold text-primary-600 mb-2">98%</div>
            <div class="text-gray-600">Satisfaction Rate</div>
        </div>
        <div>
            <div class="text-3xl font-bold text-primary-600 mb-2">24/7</div>
            <div class="text-gray-600">Support Available</div>
        </div>
        <div>
            <div class="text-3xl font-bold text-primary-600 mb-2">100%</div>
            <div class="text-gray-600">Secure Payments</div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
</script>
@endpush
