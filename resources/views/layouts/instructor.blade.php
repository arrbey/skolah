<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Instruktur {{ \App\Models\Setting::get('site_name', 'Skolah.com') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])

    <script defer src="{{ asset('vendor/alpinejs.min.js') }}"></script>
    @livewireStyles

    <style>
        [x-cloak] { display: none !important; }
    </style>

    @stack('head')
</head>
<body class="h-full bg-gray-50 font-sans antialiased"
      x-data="{ sidebarOpen: false }">

{{-- Mobile overlay --}}
<div x-show="sidebarOpen" x-cloak
     x-transition:enter="transition-opacity ease-linear duration-200"
     x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-200"
     x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     @click="sidebarOpen = false"
     class="fixed inset-0 z-40 bg-gray-900/60 lg:hidden"></div>

{{-- ─── SIDEBAR ────────────────────────────────────────────────────────────── --}}
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
       class="fixed inset-y-0 left-0 z-50 w-64 bg-dark flex flex-col
              transform transition-transform duration-300 ease-in-out
              lg:translate-x-0">

    {{-- Logo --}}
    <div class="flex items-center h-16 px-5 border-b border-white/10 shrink-0">
        <a href="{{ route('home') }}" class="flex items-center flex-1">
            @php $logo = \App\Models\Setting::get('site_logo'); @endphp
            @if($logo)
                <img src="{{ storageUrl($logo) }}" alt="{{ \App\Models\Setting::get('site_name', 'Skolah.com') }}" class="h-11 w-auto object-contain">
            @else
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 bg-gradient-to-br from-primary-500 to-secondary-500 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-white font-bold text-sm block leading-tight">{{ \App\Models\Setting::get('site_name', 'Skolah.com') }}</span>
                        <span class="text-secondary-400 text-xs font-medium">Panel Instruktur</span>
                    </div>
                </div>
            @endif
        </a>
        <button @click="sidebarOpen = false" class="lg:hidden text-gray-400 hover:text-white">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Instructor profile --}}
    <div class="px-4 py-4 border-b border-white/10">
        <div class="flex items-center gap-3">
            <img src="{{ avatarUrl(auth()->user()) }}"
                 alt="{{ auth()->user()->name }}"
                 class="w-10 h-10 rounded-full object-cover ring-2 ring-secondary-500/40">
            <div class="min-w-0">
                <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-secondary-600/30 text-secondary-300">
                    Instruktur
                </span>
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5">

        @php
            $navItems = [
                ['route' => 'instructor.dashboard',        'match' => 'instructor.dashboard',    'label' => 'Dashboard',        'icon' => 'home'],
                ['route' => 'instructor.courses.index',    'match' => 'instructor.courses*',      'label' => 'Kursus Saya',      'icon' => 'book-open'],
                ['route' => 'instructor.bootcamps.index',  'match' => 'instructor.bootcamps*',    'label' => 'Bootcamp',         'icon' => 'academic-cap'],
                ['route' => 'instructor.books.index',      'match' => 'instructor.books*',        'label' => 'Buku',             'icon' => 'library'],
                ['route' => 'instructor.book-orders.index','match' => 'instructor.book-orders*',  'label' => 'Pengiriman Buku',  'icon' => 'truck'],
                ['route' => 'instructor.earnings',         'match' => 'instructor.earnings',      'label' => 'Pendapatan',       'icon' => 'currency-dollar'],
            ];
        @endphp

        <p class="px-3 pb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Menu Utama</p>

        @foreach($navItems as $item)
            <a href="{{ route($item['route']) }}"
               class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                      {{ request()->routeIs($item['match'])
                         ? 'bg-secondary-600 text-white shadow-lg shadow-secondary-600/20'
                         : 'text-gray-400 hover:text-white hover:bg-white/10' }}">
                @include('layouts.partials.icon', ['name' => $item['icon'], 'class' => 'w-5 h-5 shrink-0'])
                {{ $item['label'] }}
            </a>
        @endforeach

        {{-- Quick create --}}
        <div class="pt-4">
            <p class="px-3 pb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Buat Baru</p>
            <a href="{{ route('instructor.courses.create') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-400 hover:text-white hover:bg-white/10 transition-all">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Kursus Baru
            </a>
        </div>

        {{-- User section --}}
        <div class="pt-4">
            <p class="px-3 pb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Akun</p>
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-400 hover:text-white hover:bg-white/10 transition-all">
                @include('layouts.partials.icon', ['name' => 'users', 'class' => 'w-5 h-5 shrink-0'])
                Dashboard Siswa
            </a>
            <a href="{{ route('dashboard.settings') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-400 hover:text-white hover:bg-white/10 transition-all">
                @include('layouts.partials.icon', ['name' => 'cog', 'class' => 'w-5 h-5 shrink-0'])
                Pengaturan
            </a>
        </div>
    </nav>

    {{-- Logout --}}
    <div class="px-3 py-3 border-t border-white/10">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-400 hover:text-red-400 hover:bg-red-400/10 transition-all">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Keluar
            </button>
        </form>
    </div>
</aside>
{{-- ─── END SIDEBAR ────────────────────────────────────────────────────────── --}}

{{-- ─── MAIN ───────────────────────────────────────────────────────────────── --}}
<div class="lg:pl-64 flex flex-col min-h-screen">

    {{-- Top Header --}}
    <header class="sticky top-0 z-30 h-16 bg-white border-b border-gray-200 flex items-center px-4 sm:px-6 gap-4 shadow-sm">
        <button @click="sidebarOpen = true"
                class="lg:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <div class="flex-1 min-w-0">
            @yield('page-header')
        </div>

        <div class="flex items-center gap-3">
            {{-- Link ke halaman publik --}}
            <a href="{{ route('home') }}"
               class="hidden sm:flex items-center gap-1.5 text-sm text-gray-500 hover:text-primary-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                Lihat Situs
            </a>

            {{-- Notification --}}
            <x-notification-dropdown />

            {{-- Avatar --}}
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="flex items-center gap-2 p-1 rounded-xl hover:bg-gray-100 transition-colors">
                    <img src="{{ avatarUrl(auth()->user()) }}"
                         alt="{{ auth()->user()->name }}"
                         class="w-8 h-8 rounded-full object-cover ring-2 ring-secondary-100">
                    <span class="hidden sm:block text-sm font-medium text-gray-700 max-w-[100px] truncate">{{ auth()->user()->name }}</span>
                </button>
                <div x-show="open" x-cloak @click.outside="open = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="absolute right-0 mt-2 w-48 rounded-xl bg-white shadow-lg ring-1 ring-gray-200 py-1 z-50">
                    <div class="px-4 py-2 border-b border-gray-100">
                        <p class="text-xs text-gray-500">Panel Instruktur</p>
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                    </div>
                    <a href="{{ route('dashboard.settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Pengaturan</a>
                    <div class="border-t border-gray-100 mt-1 pt-1">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Keluar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- Flash Messages --}}
    @if(session('success') || session('error') || session('warning'))
        <div class="px-4 sm:px-6 pt-4 space-y-2">
            @if(session('success'))
                <div x-data="{ s: true }" x-show="s" x-transition class="flex items-center justify-between gap-3 rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
                    <span>{{ session('success') }}</span>
                    <button @click="s = false"><svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
            @endif
            @if(session('error'))
                <div x-data="{ s: true }" x-show="s" x-transition class="flex items-center justify-between gap-3 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
                    <span>{{ session('error') }}</span>
                    <button @click="s = false"><svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
            @endif
            @if(session('warning'))
                <div x-data="{ s: true }" x-show="s" x-transition class="flex items-center justify-between gap-3 rounded-xl bg-yellow-50 border border-yellow-200 px-4 py-3 text-sm text-yellow-800">
                    <span>{{ session('warning') }}</span>
                    <button @click="s = false"><svg class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
            @endif
        </div>
    @endif

    {{-- Content --}}
    <main class="flex-1 px-4 sm:px-6 py-6">
        @yield('content')
    </main>

    <footer class="border-t border-gray-200 px-4 sm:px-6 py-4">
        <p class="text-xs text-gray-400 text-center">
            &copy; {{ date('Y') }} Skolah.com — Panel Instruktur
        </p>
    </footer>
</div>

@livewireScripts(['nonce' => $cspNonce ?? ''])
@stack('scripts')
</body>
</html>
