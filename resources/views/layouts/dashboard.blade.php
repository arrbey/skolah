<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ \App\Models\Setting::get('site_name', 'Skolah.com') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @livewireStyles

    <style>
        [x-cloak] { display: none !important; }
    </style>
    @stack('head')
</head>
<body class="h-full bg-gray-50 font-sans antialiased overflow-x-hidden" x-data="{ sidebarOpen: false }">

{{-- Mobile overlay --}}
<div x-show="sidebarOpen" x-cloak
     x-transition:enter="transition-opacity ease-linear duration-200"
     x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-200"
     x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     @click="sidebarOpen = false"
     class="fixed inset-0 z-40 bg-gray-900/60 lg:hidden"></div>

{{-- ─────────────────────────── SIDEBAR ────────────────────────────────────── --}}
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
       class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 flex flex-col
              transform transition-transform duration-300 ease-in-out lg:translate-x-0">

    {{-- Logo --}}
    <div class="flex items-center h-16 px-6 border-b border-gray-100 shrink-0">
        <a href="{{ route('home') }}" id="nav-logo" class="flex items-center">
            @php $logo = \App\Models\Setting::get('site_logo'); @endphp
            @if($logo)
                <img src="{{ storageUrl($logo) }}" alt="Logo" class="h-10 w-auto object-contain">
            @else
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <span class="text-gray-900 font-bold text-lg tracking-tight">{{ \App\Models\Setting::get('site_name', 'Skolah.com') }}</span>
                </div>
            @endif
        </a>
    </div>

    {{-- User info --}}
    <div class="px-4 py-4 border-b border-gray-100">
        <div class="flex items-center gap-3">
            <img src="{{ avatarUrl(auth()->user()) }}" class="w-10 h-10 rounded-full object-cover ring-2 ring-primary-100">
            <div class="min-w-0">
                <p class="text-sm font-semibold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5">
        @php
            $navItems = [
                ['route' => 'dashboard',              'label' => 'Dashboard',      'icon' => 'home'],
                ['route' => 'dashboard.my-courses',   'label' => 'Kursus Saya',    'icon' => 'book-open'],
                ['route' => 'dashboard.my-bootcamps', 'label' => 'Bootcamp Saya',  'icon' => 'academic-cap'],
                ['route' => 'dashboard.my-books',     'label' => 'Buku Saya',      'icon' => 'library'],
                ['route' => 'dashboard.certificates', 'label' => 'Sertifikat',     'icon' => 'badge-check'],
                ['route' => 'dashboard.chat',         'label' => 'Chat Pesan',     'icon' => 'chat-alt'],
                ['route' => 'dashboard.orders',       'label' => 'Riwayat Order',  'icon' => 'shopping-bag'],
                ['route' => 'dashboard.settings',     'label' => 'Pengaturan',     'icon' => 'cog'],
            ];
        @endphp

        @foreach($navItems as $item)
            @php
                $isActive = ($item['route'] === 'dashboard') 
                            ? request()->routeIs('dashboard') 
                            : request()->routeIs($item['route'] . '*');
                
                // Buat ID unik untuk tour (contoh: nav-dashboard, nav-my-courses, dst)
                $tourId = 'nav-' . str_replace('dashboard.', '', $item['route']);
            @endphp
            <a href="{{ route($item['route']) }}"
               id="{{ $tourId }}"
               class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                      {{ $isActive ? 'bg-primary-600 text-white shadow-sm' : 'text-gray-700 hover:bg-primary-50 hover:text-primary-600' }}">
                @include('layouts.partials.icon', ['name' => $item['icon'], 'class' => 'w-5 h-5 shrink-0'])
                {{ $item['label'] }}
            </a>
        @endforeach

        <div class="pt-4 pb-2"><p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Lainnya</p></div>
        <a href="{{ route('courses.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-100">
            @include('layouts.partials.icon', ['name' => 'search', 'class' => 'w-5 h-5 shrink-0']) Cari Kursus
        </a>
    </nav>

    {{-- Logout --}}
    <div class="px-3 py-3 border-t border-gray-100">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-red-600 hover:bg-red-50">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Keluar
            </button>
        </form>
    </div>
</aside>

{{-- ─────────────────────────── MAIN WRAPPER ───────────────────────────────── --}}
<div class="lg:pl-64 flex flex-col min-h-screen">
    <header class="sticky top-0 z-30 h-16 bg-white border-b border-gray-200 flex items-center px-4 sm:px-6 gap-4 shadow-sm">
        <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>

        <div class="flex-1 min-w-0">@yield('page-header')</div>

        <div class="flex items-center gap-3">
            <x-notification-dropdown />
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" id="user-menu-tour" class="flex items-center gap-2 p-1 rounded-xl hover:bg-gray-100 transition-colors">
                    <img src="{{ avatarUrl(auth()->user()) }}" class="w-8 h-8 rounded-full object-cover ring-2 ring-primary-100">
                </button>
                <div x-show="open" x-cloak @click.outside="open = false" x-transition class="absolute right-0 mt-2 w-48 rounded-xl bg-white shadow-lg ring-1 ring-gray-200 py-1 z-50">
                    <a href="{{ route('home') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Ke Halaman Utama</a>
                    <a href="{{ route('dashboard.settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Profil & Pengaturan</a>
                    <form method="POST" action="{{ route('logout') }}"><div class="border-t border-gray-100 mt-1 pt-1">@csrf<button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Keluar</button></div></form>
                </div>
            </div>
        </div>
    </header>

    {{-- Flash Messages --}}
    @if(session('success') || session('error'))
        <div class="px-4 sm:px-6 pt-4">
            @if(session('success'))
                <div x-data="{ s: true }" x-show="s" x-transition class="flex items-center justify-between gap-3 rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800 mb-3">
                    <span>{{ session('success') }}</span>
                    <button @click="s = false" class="text-green-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
            @endif
            @if(session('error'))
                <div x-data="{ s: true }" x-show="s" x-transition class="flex items-center justify-between gap-3 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800 mb-3">
                    <span>{{ session('error') }}</span>
                    <button @click="s = false" class="text-red-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
            @endif
        </div>
    @endif

    <main class="flex-1 px-4 sm:px-6 py-6">@yield('content')</main>

    <footer class="border-t border-gray-200 px-4 sm:px-6 py-4">
        <p class="text-xs text-gray-400 text-center">&copy; {{ date('Y') }} Skolah.com — Platform Edukasi Digital Terlengkap</p>
    </footer>
</div>

@livewireScripts(['nonce' => $cspNonce ?? ''])
@include('layouts.partials.onboarding')

@unless(request()->routeIs('dashboard.chat*'))
    @include('layouts.partials.floating-chat')
@endunless

@auth
<script nonce="{{ $cspNonce ?? '' }}">
    document.addEventListener('DOMContentLoaded', () => {
        let checkEcho = setInterval(() => {
            if (window.Echo) {
                clearInterval(checkEcho);
                window.Echo.private('App.Models.User.' + {{ Auth::id() }})
                    .notification((notification) => {
                        if (window.Swal) {
                            Swal.fire({
                                toast: true, position: 'top-end', showConfirmButton: false, timer: 5000, timerProgressBar: true,
                                icon: notification.type || 'info', title: notification.title, text: notification.message,
                                didOpen: (toast) => { toast.addEventListener('click', () => { if (notification.url) window.location.href = notification.url; }) }
                            });
                        }
                        const badge = document.getElementById('notification-badge');
                        if (badge) {
                            let count = parseInt(badge.innerText) || 0;
                            badge.innerText = count + 1;
                            badge.classList.remove('hidden');
                        }
                    });
            }
        }, 500);
    });
</script>
@endauth

@stack('scripts')
</body>
</html>
