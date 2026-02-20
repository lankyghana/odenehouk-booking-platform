<header class="sticky top-0 z-20 bg-white border-b border-slate-200 shadow-sm">
    <div class="flex items-center justify-between px-4 sm:px-6 h-16">
        <div class="flex items-center space-x-3">
            <button class="lg:hidden text-slate-600" @click="sidebarOpen = true">
                <span class="sr-only">Open sidebar</span>
                ☰
            </button>
            <div class="hidden lg:block text-sm text-slate-500">Admin Panel</div>
        </div>
        <div class="flex items-center space-x-4">
            <span class="text-sm font-medium text-slate-700">{{ auth()->user()->name ?? 'Admin' }}</span>
            <form method="POST" action="{{ route('logout') }}" class="hidden md:block">
                @csrf
                <button type="submit" class="text-sm text-red-600 hover:text-red-700">Logout</button>
            </form>
        </div>
    </div>
</header>
