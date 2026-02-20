@php
    $navItems = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard'],
        ['label' => 'Bookings', 'route' => 'admin.bookings.index'],
        ['label' => 'Offers', 'route' => 'admin.offers.index'],
        ['label' => 'Branding', 'route' => 'admin.branding.edit'],
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
        <nav class="flex-1 overflow-y-auto py-4">
            <ul class="space-y-1 px-3">
                @foreach($navItems as $item)
                    @php
                        $active = request()->routeIs($item['route'] . '*');
                    @endphp
                    <li>
                        <a href="{{ route($item['route']) }}" class="flex items-center px-3 py-2 rounded-lg text-sm font-medium transition {{ $active ? 'bg-primary-50 text-primary-700 border border-primary-100' : 'text-slate-700 hover:text-primary-700 hover:bg-slate-50' }}">
                            <span class="mr-3 h-2 w-2 rounded-full {{ $active ? 'bg-primary-500' : 'bg-slate-300' }}"></span>
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
