@php
    $navItems = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'dashboard'],
        ['label' => 'Bookings', 'route' => 'admin.bookings.index', 'icon' => 'bookings'],
        ['label' => 'Offers', 'route' => 'admin.offers.index', 'icon' => 'offers'],
        ['label' => 'Branding', 'route' => 'admin.branding.edit', 'icon' => 'branding'],
    ];
@endphp
<aside class="fixed inset-y-0 left-0 z-40 w-72 bg-white border-r border-slate-200 shadow-xl transform transition-transform duration-200 lg:translate-x-0" x-bind:class="{ '-translate-x-full': !sidebarOpen }" x-transition>
    <div class="h-full flex flex-col">
        <div class="flex items-center justify-between px-6 h-16 border-b border-slate-200">
            <div class="flex items-center space-x-3">
                <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-primary-600 to-accent-500 flex items-center justify-center text-white font-semibold">B</div>
                <div>
                    <p class="text-sm text-slate-500">Admin</p>
                    <p class="text-base font-semibold">{{ config('app.name', 'Platform') }}</p>
                </div>
            </div>
            <button class="lg:hidden text-slate-500 hover:text-slate-700" @click="sidebarOpen = false">✕</button>
        </div>
        <nav class="flex-1 overflow-y-auto py-5">
            <ul class="space-y-2 px-3">
                @foreach($navItems as $item)
                    @php
                        $active = request()->routeIs($item['route'] . '*');
                    @endphp
                    <li>
                        <a href="{{ route($item['route']) }}" class="group flex items-center gap-3 rounded-xl px-3.5 py-3 text-sm font-medium transition-all {{ $active ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg {{ $active ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500 group-hover:bg-white' }}">
                                @if($item['icon'] === 'dashboard')
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M4 10v10a1 1 0 001 1h5v-6h4v6h5a1 1 0 001-1V10"/>
                                    </svg>
                                @elseif($item['icon'] === 'bookings')
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10m-12 9h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v11a2 2 0 002 2z"/>
                                    </svg>
                                @elseif($item['icon'] === 'offers')
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M3 11l8.586 8.586a2 2 0 002.828 0L21 13a2 2 0 000-2.828L12.414 1.586a2 2 0 00-1.414-.586H5a2 2 0 00-2 2v6a2 2 0 00.586 1.414z"/>
                                    </svg>
                                @else
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 20h9M12 4h9M4 9h16M4 15h16M8 4v16"/>
                                    </svg>
                                @endif
                            </span>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>
        <div class="border-t border-slate-200 p-4 text-xs text-slate-500">
            <p>Signed in as</p>
            <p class="font-semibold text-slate-700">{{ auth()->user()->name ?? 'Admin' }}</p>
        </div>
    </div>
</aside>
