<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @php $favicon = \App\Models\Setting::get('site_favicon'); @endphp
    @if($favicon)
        <link rel="icon" href="{{ asset('storage/' . $favicon) }}">
    @else
        <link rel="icon" href="{{ asset('favicon.ico') }}">
    @endif

    {{-- Preconnect to external CDNs for faster TTFB on third-party assets --}}
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">

    {{-- SweetAlert2 (defer to avoid render-blocking) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>

    {{-- Analytics & Tracking --}}
    @php
        $gtmId = \App\Models\Setting::get('google_tag_manager');
        $ga4Id = \App\Models\Setting::get('google_analytics_id');
        $pixelId = \App\Models\Setting::get('facebook_pixel_id');
    @endphp

    @if($gtmId || $ga4Id || $pixelId)
    <!-- Dynamic Lazy-Load Tracking Scripts on User Interaction -->
    <script nonce="{{ $cspNonce ?? '' }}">
      (function() {
        var initialized = false;
        
        function initTracking() {
          if (initialized) return;
          initialized = true;
          
          // Clean up event listeners
          window.removeEventListener('scroll', initTracking);
          window.removeEventListener('mousemove', initTracking);
          window.removeEventListener('touchstart', initTracking);
          
          // 1. Google Tag Manager
          @if($gtmId)
          (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
          new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
          j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
          'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
          })(window,document,'script','dataLayer','{{ $gtmId }}');
          @endif

          // 2. Google Analytics 4
          @if($ga4Id)
          var gaScript = document.createElement('script');
          gaScript.src = "https://www.googletagmanager.com/gtag/js?id={{ $ga4Id }}";
          gaScript.async = true;
          document.head.appendChild(gaScript);

          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());
          gtag('config', '{{ $ga4Id }}');
          @endif

          // 3. Meta Pixel (Facebook)
          @if($pixelId)
          !function(f,b,e,v,n,t,s)
          {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
          n.callMethod.apply(n,arguments):n.queue.push(arguments)};
          if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
          n.queue=[];t=b.createElement(e);t.async=!0;
          t.src=v;s=b.getElementsByTagName(e)[0];
          s.parentNode.insertBefore(t,s)}(window, document,'script',
          'https://connect.facebook.net/en_US/fbevents.js');
          fbq('init', '{{ $pixelId }}');
          fbq('track', 'PageView');
          @endif
        }

        // Trigger on interaction or fallback after 4 seconds
        window.addEventListener('scroll', initTracking, { passive: true });
        window.addEventListener('mousemove', initTracking, { passive: true });
        window.addEventListener('touchstart', initTracking, { passive: true });
        setTimeout(initTracking, 4000);
      })();
    </script>
    
    @if($pixelId)
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id={{ $pixelId }}&ev=PageView&noscript=1"
    /></noscript>
    @endif
    @endif

    {{-- SEOTools meta tags --}}
    {!! \Artesaos\SEOTools\Facades\SEOMeta::generate() !!}
    {!! \Artesaos\SEOTools\Facades\OpenGraph::generate() !!}
    {!! \Artesaos\SEOTools\Facades\TwitterCard::generate() !!}
    {{-- Inject CSP nonce ke JSON-LD script yang di-generate SEOTools --}}
    {!! str_replace(
        '<script type="application/ld+json">',
        '<script type="application/ld+json" nonce="' . ($cspNonce ?? '') . '">',
        \Artesaos\SEOTools\Facades\JsonLd::generate()
    ) !!}

    {{-- Fallback title jika SEOTools tidak diset --}}
    @hasSection('title')
        <title>@yield('title') — {{ \App\Models\Setting::get('site_name', 'Skolah.com') }}</title>
    @else
        <title>{{ \Artesaos\SEOTools\Facades\SEOMeta::getTitle() ?: \App\Models\Setting::get('site_name', config('app.name')) }}</title>
    @endif

    {{-- Google Fonts: Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    {{-- Tailwind CSS (compiled via Vite) — already included by @vite above --}}

    {{-- Livewire styles --}}
    @livewireStyles

    {{-- Custom styles --}}
    <style>
        [x-cloak] { display: none !important; }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
    </style>

    {{-- Swiper.js (defer script; CSS stays in head for CLS safety) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js" defer></script>

    {{-- Scroll Reveal Animations --}}
    <link rel="stylesheet" href="{{ asset('css/animations.css') }}">

    @stack('head')
</head>
<body class="bg-white font-sans text-gray-800 antialiased overflow-x-hidden">
@if(!empty($gtmId))
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtmId }}"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
@endif

{{-- ─── NAVBAR ────────────────────────────────────────────────────────────── --}}
<header
    x-data="{ open: false, scrolled: false }"
    x-init="window.addEventListener('scroll', () => scrolled = window.scrollY > 10)"
    :class="scrolled ? 'bg-white shadow-md' : 'bg-white'"
    class="sticky top-0 z-50 transition-shadow duration-300 border-b border-gray-100"
>
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            <a href="{{ route('home') }}" class="flex items-center shrink-0">
                @php $logo = \App\Models\Setting::get('site_logo'); @endphp
                @if($logo)
                    <img src="{{ storageUrl($logo) }}" alt="{{ \App\Models\Setting::get('site_name', 'Skolah.com') }}" class="h-8 w-auto">
                @else
                    <div class="w-10 h-10 bg-gradient-to-br from-primary-600 to-secondary-600 rounded-xl flex items-center justify-center shadow-sm">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <span class="ml-2.5 text-xl font-extrabold text-primary-600">
                        {{ \App\Models\Setting::get('site_name', 'Skolah.com') }}
                    </span>
                @endif
            </a>

            {{-- Desktop Nav Links --}}
            <div class="hidden lg:flex items-center gap-1">
                <a href="{{ route('courses.index') }}" id="nav-home-courses"
                   class="px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:text-primary-600 hover:bg-primary-50 transition-colors {{ request()->routeIs('courses*') ? 'text-primary-600 bg-primary-50' : '' }}">
                    Kursus
                </a>
                <a href="{{ route('bootcamps.index') }}" id="nav-home-bootcamps"
                   class="px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:text-primary-600 hover:bg-primary-50 transition-colors {{ request()->routeIs('bootcamps*') ? 'text-primary-600 bg-primary-50' : '' }}">
                    Bootcamp
                </a>
                <a href="{{ route('books.index') }}" id="nav-home-books"
                   class="px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:text-primary-600 hover:bg-primary-50 transition-colors {{ request()->routeIs('books*') ? 'text-primary-600 bg-primary-50' : '' }}">
                    Buku
                </a>
                @if($hasMembershipPlans ?? false)
                <a href="{{ route('membership') }}"
                   class="px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:text-primary-600 hover:bg-primary-50 transition-colors {{ request()->routeIs('membership') ? 'text-primary-600 bg-primary-50' : '' }}">
                    Membership
                </a>
                @endif
            </div>

            {{-- Desktop Right: search + cart + auth --}}
            <div class="hidden lg:flex items-center gap-3">
                {{-- Search --}}
                <a href="{{ route('search') }}"
                   class="p-2 rounded-lg text-gray-500 hover:text-primary-600 hover:bg-primary-50 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </a>

                @auth
                    {{-- Notification Bell --}}
                    @livewire('notification-bell')

                    {{-- Cart --}}
                    <a href="{{ route('cart') }}"
                       class="relative p-2 rounded-lg text-gray-500 hover:text-primary-600 hover:bg-primary-50 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        @livewire('cart-count')
                    </a>

                    {{-- User Dropdown --}}
                    <div x-data="{ dropOpen: false }" class="relative">
                        <button @click="dropOpen = !dropOpen"
                                class="flex items-center gap-2 px-3 py-1.5 rounded-xl border border-gray-200 hover:border-primary-300 hover:bg-primary-50 transition-colors">
                            <img src="{{ avatarUrl(auth()->user()) }}"
                                 alt="{{ auth()->user()->name }}"
                                 loading="lazy"
                                 class="w-7 h-7 rounded-full object-cover ring-2 ring-primary-100">
                            <span class="text-sm font-medium text-gray-700 max-w-[120px] truncate">{{ auth()->user()->name }}</span>
                            <svg class="w-4 h-4 text-gray-400 transition-transform" :class="dropOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="dropOpen"
                             x-cloak
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             @click.outside="dropOpen = false"
                             class="absolute right-0 mt-2 w-56 rounded-2xl bg-white shadow-xl ring-1 ring-gray-900/10 divide-y divide-gray-100 overflow-hidden z-50">

                            <div class="px-4 py-3">
                                <p class="text-xs text-gray-500">Masuk sebagai</p>
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ auth()->user()->email }}</p>
                            </div>

                            <div class="py-1">
                                <a href="{{ route('dashboard') }}"
                                   class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                    Dashboard Saya
                                </a>
                                <a href="{{ route('dashboard.my-courses') }}"
                                   class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                    Kursus Saya
                                </a>
                                @if(auth()->user()->hasRole('instructor'))
                                    <a href="{{ route('instructor.dashboard') }}"
                                       class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-700 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                        Panel Instruktur
                                    </a>
                                @endif
                                @if(auth()->user()->hasRole('admin'))
                                    <a href="{{ route('admin.dashboard') }}"
                                       class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        Panel Admin
                                    </a>
                                @endif
                            </div>

                            <div class="py-1">
                                <a href="{{ route('dashboard.settings') }}"
                                   class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Profil & Pengaturan
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                            class="w-full flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}"
                       class="text-sm font-medium text-gray-700 hover:text-primary-600 transition-colors">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" id="nav-home-register"
                       class="px-4 py-2 rounded-xl bg-primary-600 text-white text-sm font-semibold hover:bg-primary-700 transition-colors shadow-sm">
                        Daftar Gratis
                    </a>
                @endauth
            </div>

            {{-- Mobile hamburger --}}
            <button @click="open = !open"
                    class="lg:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors">
                <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg x-show="open" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="open"
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="lg:hidden border-t border-gray-100 py-4 space-y-1">
            <a href="{{ route('courses.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-primary-50 hover:text-primary-600 transition-colors">Kursus</a>
            <a href="{{ route('bootcamps.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-primary-50 hover:text-primary-600 transition-colors">Bootcamp</a>
            <a href="{{ route('books.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-primary-50 hover:text-primary-600 transition-colors">Buku</a>
            @if($hasMembershipPlans ?? false)
            <a href="{{ route('membership') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-primary-50 hover:text-primary-600 transition-colors">Membership</a>
            @endif
            <a href="{{ route('search') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-primary-50 hover:text-primary-600 transition-colors">Cari Kursus</a>

            <div class="pt-3 border-t border-gray-100 space-y-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-primary-600 bg-primary-50">Dashboard Saya</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2.5 rounded-lg text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">Masuk</a>
                    <a href="{{ route('register') }}" class="block px-3 py-2.5 rounded-xl bg-primary-600 text-white text-sm font-semibold text-center hover:bg-primary-700 transition-colors">Daftar Gratis</a>
                @endauth
            </div>
        </div>
    </nav>
</header>
{{-- ─── END NAVBAR ─────────────────────────────────────────────────────────── --}}

{{-- ─── FLASH MESSAGES ─────────────────────────────────────────────────────── --}}
@if(session('success') || session('error') || session('warning') || session('info'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition
                 class="flex items-center justify-between gap-3 rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800 mb-3">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('success') }}
                </div>
                <button @click="show = false" class="text-green-500 hover:text-green-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        @endif
        @if(session('error'))
            <div x-data="{ show: true }" x-show="show" x-transition
                 class="flex items-center justify-between gap-3 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800 mb-3">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('error') }}
                </div>
                <button @click="show = false" class="text-red-500 hover:text-red-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        @endif
        @if(session('warning'))
            <div x-data="{ show: true }" x-show="show" x-transition
                 class="flex items-center justify-between gap-3 rounded-xl bg-yellow-50 border border-yellow-200 px-4 py-3 text-sm text-yellow-800 mb-3">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-yellow-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('warning') }}
                </div>
                <button @click="show = false" class="text-yellow-500 hover:text-yellow-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        @endif
        @if(session('info'))
            <div x-data="{ show: true }" x-show="show" x-transition
                 class="flex items-center justify-between gap-3 rounded-xl bg-blue-50 border border-blue-200 px-4 py-3 text-sm text-blue-800 mb-3">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('info') }}
                </div>
                <button @click="show = false" class="text-blue-500 hover:text-blue-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        @endif
    </div>
@endif
{{-- ─── END FLASH MESSAGES ──────────────────────────────────────────────────── --}}

{{-- ─── MAIN CONTENT ───────────────────────────────────────────────────────── --}}
<main>
    @yield('content')
</main>
{{-- ─── END MAIN CONTENT ───────────────────────────────────────────────────── --}}

{{-- ─── FOOTER ─────────────────────────────────────────────────────────────── --}}
<footer class="bg-white border-t border-slate-200 mt-0">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Newsletter strip --}}
        <div class="py-12 border-b border-slate-200">
            <div class="max-w-2xl mx-auto text-center">
                <h3 class="text-2xl font-bold text-slate-800 mb-2">Dapatkan update kursus terbaru 📚</h3>
                <p class="text-slate-500 text-sm mb-6">Bergabung dengan 50.000+ pelajar. Tidak ada spam, bisa unsubscribe kapan saja.</p>
                <form class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto" onsubmit="return false;">
                    <input type="email" placeholder="Masukkan email Anda"
                           class="flex-1 rounded-xl bg-white border border-slate-300 px-4 py-3 text-sm text-slate-800 placeholder-slate-400 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                    <button type="submit"
                            class="shrink-0 px-6 py-3 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 hover:shadow-lg hover:-translate-y-0.5 transition-all">
                        Langganan
                    </button>
                </form>
            </div>
        </div>

        {{-- Main grid --}}
        <div class="py-14 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">

            <div class="lg:col-span-1">
                <a href="{{ route('home') }}" class="flex items-center mb-5">
                    @if($logo)
                        <img src="{{ storageUrl($logo) }}" alt="{{ \App\Models\Setting::get('site_name', 'Skolah.com') }}" class="h-8 w-auto">
                    @else
                        <div class="w-9 h-9 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-sm">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <span class="ml-2.5 text-xl font-extrabold text-slate-900 tracking-tight">{{ \App\Models\Setting::get('site_name', 'Skolah.com') }}</span>
                    @endif
                </a>
                <p class="text-slate-500 text-sm leading-relaxed mb-6">
                    {{ \App\Models\Setting::get('site_description', 'Platform Edukasi Digital Terlengkap di Indonesia. Belajar dari instruktur terbaik kapan saja, di mana saja.') }}
                </p>
                {{-- Social --}}
                <div class="flex gap-3">
                    @if(\App\Models\Setting::get('site_facebook'))
                    <a href="{{ \App\Models\Setting::get('site_facebook') }}" target="_blank" aria-label="Facebook" class="w-9 h-9 rounded-full bg-white border border-slate-200 text-slate-400 hover:text-blue-600 hover:border-blue-600 hover:bg-blue-50 flex items-center justify-center shadow-sm transition-all">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878V14.89h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg>
                    </a>
                    @endif
                    @if(\App\Models\Setting::get('site_instagram'))
                    <a href="{{ \App\Models\Setting::get('site_instagram') }}" target="_blank" aria-label="Instagram" class="w-9 h-9 rounded-full bg-white border border-slate-200 text-slate-400 hover:text-pink-600 hover:border-pink-600 hover:bg-pink-50 flex items-center justify-center shadow-sm transition-all">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    </a>
                    @endif
                    @if(\App\Models\Setting::get('site_youtube'))
                    <a href="{{ \App\Models\Setting::get('site_youtube') }}" target="_blank" aria-label="YouTube" class="w-9 h-9 rounded-full bg-white border border-slate-200 text-slate-400 hover:text-red-600 hover:border-red-600 hover:bg-red-50 flex items-center justify-center shadow-sm transition-all">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                    </a>
                    @endif
                    @if(\App\Models\Setting::get('site_tiktok'))
                    <a href="{{ \App\Models\Setting::get('site_tiktok') }}" target="_blank" aria-label="TikTok" class="w-9 h-9 rounded-full bg-white border border-slate-200 text-slate-400 hover:text-slate-800 hover:border-slate-800 hover:bg-slate-100 flex items-center justify-center shadow-sm transition-all">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.33 6.33 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.69a8.27 8.27 0 004.84 1.55V6.79a4.85 4.85 0 01-1.07-.1z"/></svg>
                    </a>
                    @endif
                </div>
            </div>

            {{-- Produk --}}
            <div>
                <h4 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-5">Produk</h4>
                <ul class="space-y-3">
                    <li><a href="{{ route('courses.index') }}" class="text-sm font-medium text-slate-500 hover:text-blue-600 transition-colors">Kursus Online</a></li>
                    <li><a href="{{ route('bootcamps.index') }}" class="text-sm font-medium text-slate-500 hover:text-blue-600 transition-colors">Bootcamp &amp; Webinar</a></li>
                    <li><a href="{{ route('books.index') }}" class="text-sm font-medium text-slate-500 hover:text-blue-600 transition-colors">Buku Digital</a></li>
                    @if($hasMembershipPlans ?? false)
                    <li><a href="{{ route('membership') }}" class="text-sm font-medium text-slate-500 hover:text-blue-600 transition-colors">Membership Premium</a></li>
                    @endif
                    <li><a href="{{ route('search') }}" class="text-sm font-medium text-slate-500 hover:text-blue-600 transition-colors">Cari Kursus</a></li>
                </ul>
            </div>

            {{-- Perusahaan --}}
            <div>
                <h4 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-5">Perusahaan</h4>
                <ul class="space-y-3">
                    <li><a href="{{ route('about') }}" class="text-sm font-medium text-slate-500 hover:text-blue-600 transition-colors">Tentang Kami</a></li>
                    <li><a href="{{ route('contact') }}" class="text-sm font-medium text-slate-500 hover:text-blue-600 transition-colors">Hubungi Kami</a></li>
                    <li><a href="{{ route('blog.index') }}" class="text-sm font-medium text-slate-500 hover:text-blue-600 transition-colors">Blog &amp; Artikel</a></li>
                </ul>
            </div>

            {{-- Bantuan + Pembayaran --}}
            <div>
                <h4 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-5">Bantuan</h4>
                <ul class="space-y-3 mb-8">
                    <li><a href="{{ route('faq') }}" class="text-sm font-medium text-slate-500 hover:text-blue-600 transition-colors">Pusat Bantuan (FAQ)</a></li>
                    <li><a href="{{ route('terms') }}" class="text-sm font-medium text-slate-500 hover:text-blue-600 transition-colors">Syarat &amp; Ketentuan</a></li>
                    <li><a href="{{ route('privacy') }}" class="text-sm font-medium text-slate-500 hover:text-blue-600 transition-colors">Kebijakan Privasi</a></li>
                </ul>
                <div>
                    <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3">Metode Pembayaran</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach(['Midtrans', 'GoPay', 'OVO', 'QRIS', 'VA BCA'] as $pay)
                            <span class="text-[11px] bg-white border border-slate-200 text-slate-600 px-2 py-1 rounded-md font-semibold shadow-sm">{{ $pay }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Bottom bar --}}
        <div class="border-t border-slate-200 py-6 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-sm text-slate-500 font-medium">
                &copy; {{ date('Y') }} <span class="text-slate-800 font-bold">{{ \App\Models\Setting::get('site_name', 'Skolah.com') }}</span> — Hak cipta dilindungi.
            </p>
            <p class="text-sm text-slate-500 font-medium flex items-center gap-1">
                &copy; {{ date('Y') }} {{ config('app.name') }}. Seluruh hak cipta dilindungi.
            </p>
        </div>
    </div>
</footer>
{{-- ─── END FOOTER ──────────────────────────────────────────────────────────── --}}

{{-- ─── SCRIPTS ─────────────────────────────────────────────────────────────── --}}
@livewireScripts(['nonce' => $cspNonce ?? ''])

{{-- Midtrans Snap.js --}}
<script src="{{ config('midtrans.snap_url') }}" data-client-key="{{ config('midtrans.client_key') }}"></script>



{{-- Scroll Reveal Animations --}}
<script src="{{ asset('js/animations.js') }}"></script>

@stack('scripts')
{{-- ─── END SCRIPTS ─────────────────────────────────────────────────────────── --}}

{{-- ─── WHATSAPP FLOATING BUTTON ────────────────────────────────────────────── --}}
@php
    $waRaw = \App\Models\Setting::get('site_whatsapp', '+62 812-3456-7890');
    $waClean = preg_replace('/[^0-9]/', '', $waRaw);
    if(str_starts_with($waClean, '0')) {
        $waClean = '62' . substr($waClean, 1);
    }
@endphp
<a href="https://wa.me/{{ $waClean }}" target="_blank" rel="noopener noreferrer"
   class="fixed bottom-6 right-6 z-50 flex items-center justify-center w-14 h-14 bg-[#25D366] text-white rounded-full shadow-lg hover:bg-[#128C7E] hover:scale-110 hover:-translate-y-1 transition-all duration-300 group"
   aria-label="Chat WhatsApp">
    <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.274.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.1.824zm-3.423-14.416c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm.029 18.88c-1.161 0-2.305-.292-3.318-.844l-3.677.964.984-3.595c-.607-1.052-.927-2.261-.926-3.513.003-3.864 3.149-7.009 7.022-7.009 1.874 0 3.633.731 4.957 2.057 1.326 1.326 2.056 3.09 2.057 4.965.002 3.873-3.141 7.016-7.1 7.016z"/>
    </svg>
    <span class="absolute right-16 px-3 py-1 bg-white text-gray-800 text-sm font-semibold rounded-lg shadow-md opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap">
        Tanya Admin Skolah
    </span>
</a>

@include('layouts.partials.onboarding')

</body>
</html>
