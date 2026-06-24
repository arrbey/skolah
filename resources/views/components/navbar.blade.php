@props([
    'transparent' => false,
])

<header
    x-data="{
        mobileOpen: false,
        scrolled: false,
        userOpen: false,
        init() {
            window.addEventListener('scroll', () => { this.scrolled = window.scrollY > 12 })
        }
    }"
    :class="scrolled || !$transparent ? 'bg-white shadow-sm border-b border-gray-100' : 'bg-transparent'"
    class="sticky top-0 z-50 transition-all duration-300"
>
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 gap-4">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center shrink-0">
                @php $logo = \App\Models\Setting::get('site_logo'); @endphp
                @if($logo)
                    <img src="{{ storageUrl($logo) }}" alt="{{ \App\Models\Setting::get('site_name', 'Skolah.com') }}" class="h-11 w-auto">
                @else
                    <div class="w-10 h-10 bg-gradient-to-br from-primary-600 to-secondary-600 rounded-xl flex items-center justify-center shadow-sm">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <span class="ml-2.5 text-xl font-extrabold text-primary-600 leading-tight">
                        {{ \App\Models\Setting::get('site_name', 'Skolah.com') }}
                    </span>
                @endif
            </a>

            {{-- Desktop Nav --}}
            <div class="hidden lg:flex items-center gap-1 flex-1 justify-center">
                @foreach(collect([
                    ['route' => 'courses.index',   'label' => 'Kursus',     'match' => 'courses*'],
                    ['route' => 'bootcamps.index',  'label' => 'Bootcamp',   'match' => 'bootcamps*'],
                    ['route' => 'books.index',      'label' => 'Buku',       'match' => 'books*'],
                    ['route' => 'membership',       'label' => 'Membership', 'match' => 'membership'],
                ])->filter(fn($link) => $link['route'] !== 'membership' || ($hasMembershipPlans ?? false)) as $link)
                    <a href="{{ route($link['route']) }}"
                       class="px-3 py-2 rounded-lg text-sm font-medium transition-colors
                              {{ request()->routeIs($link['match']) ? 'text-primary-600 bg-primary-50' : 'text-gray-700 hover:text-primary-600 hover:bg-primary-50' }}">
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </div>

            {{-- Desktop Right --}}
            <div class="hidden lg:flex items-center gap-2">
                <a href="{{ route('search') }}"
                   class="p-2 rounded-lg text-gray-500 hover:text-primary-600 hover:bg-primary-50 transition-colors"
                   aria-label="Cari">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </a>

                @auth
                    {{-- Cart --}}
                    <a href="#" class="relative p-2 rounded-lg text-gray-500 hover:text-primary-600 hover:bg-primary-50 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        @livewire('cart-count')
                    </a>

                    {{-- User Dropdown --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                                class="flex items-center gap-2 px-2.5 py-1.5 rounded-xl border border-gray-200 hover:border-primary-300 hover:bg-primary-50 transition-all">
                            <x-avatar :user="auth()->user()" size="sm" />
                            <span class="text-sm font-medium text-gray-700 max-w-[100px] truncate">{{ auth()->user()->name }}</span>
                            <svg class="w-4 h-4 text-gray-400 transition-transform duration-150" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="open" x-cloak @click.outside="open = false"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="absolute right-0 mt-2 w-56 rounded-2xl bg-white shadow-xl ring-1 ring-gray-900/5 overflow-hidden z-50">

                            <div class="px-4 py-3 bg-gradient-to-br from-primary-50 to-secondary-50 border-b border-gray-100">
                                <p class="text-xs text-gray-500">Masuk sebagai</p>
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ auth()->user()->email }}</p>
                            </div>

                            <div class="py-1">
                                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                    Dashboard Saya
                                </a>
                                <a href="{{ route('dashboard.my-courses') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                    Kursus Saya
                                </a>
                                @if(auth()->user()->hasRole('instructor'))
                                    <a href="{{ route('instructor.dashboard') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                        Panel Instruktur
                                    </a>
                                @endif
                                @if(auth()->user()->hasRole('admin'))
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                                        Panel Admin
                                    </a>
                                @endif
                            </div>

                            <div class="border-t border-gray-100 py-1">
                                <a href="{{ route('dashboard.settings') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    Profil & Pengaturan
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600 transition-colors">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}"
                       class="px-4 py-2 rounded-xl bg-primary-600 text-white text-sm font-semibold hover:bg-primary-700 transition-colors shadow-sm">
                        Daftar Gratis
                    </a>
                @endauth
            </div>

            {{-- Mobile: cart + hamburger --}}
            <div class="flex lg:hidden items-center gap-2">
                @auth
                    <a href="#" class="relative p-2 rounded-lg text-gray-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </a>
                @endauth
                <button @click="mobileOpen = !mobileOpen"
                        class="p-2 rounded-lg text-gray-600 hover:bg-gray-100 transition-colors"
                        aria-label="Menu">
                    <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="mobileOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="mobileOpen" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="lg:hidden border-t border-gray-100 py-4 space-y-0.5">
            <a href="{{ route('courses.index') }}"  class="block px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-primary-50 hover:text-primary-600">Kursus</a>
            <a href="{{ route('bootcamps.index') }}" class="block px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-primary-50 hover:text-primary-600">Bootcamp</a>
            <a href="{{ route('books.index') }}"     class="block px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-primary-50 hover:text-primary-600">Buku</a>
            @if($hasMembershipPlans ?? false)
            <a href="{{ route('membership') }}"      class="block px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-primary-50 hover:text-primary-600">Membership</a>
            @endif
            <a href="{{ route('search') }}"          class="block px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-primary-50 hover:text-primary-600">Cari Kursus</a>
            <div class="pt-3 border-t border-gray-100 space-y-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="block px-3 py-2.5 rounded-lg text-sm font-medium text-primary-600 bg-primary-50">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2.5 rounded-lg text-sm font-medium text-red-600 hover:bg-red-50">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}"    class="block px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">Masuk</a>
                    <a href="{{ route('register') }}" class="block px-3 py-2.5 rounded-xl bg-primary-600 text-white text-sm font-semibold text-center hover:bg-primary-700">Daftar Gratis</a>
                @endauth
            </div>
        </div>
    </nav>
</header>
