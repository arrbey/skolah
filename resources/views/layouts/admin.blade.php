<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php $favicon = \App\Models\Setting::get('site_favicon'); @endphp
    @if($favicon)
        <link rel="icon" href="{{ asset('storage/' . $favicon) }}">
    @else
        <link rel="icon" href="{{ asset('favicon.ico') }}">
    @endif
    <title>@yield('title', 'Dashboard') — Admin {{ \App\Models\Setting::get('site_name', 'Skolah.com') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])

    <script defer src="{{ asset('vendor/alpinejs.min.js') }}"></script>
    @livewireStyles

    <style>
        [x-cloak] { display: none !important; }
        .sidebar-group-label {
            padding: 1.5rem 0.875rem 0.5rem 0.875rem;
            font-size: 10px;
            font-weight: 800;
            color: #94a3b8; /* slate-400 */
            text-transform: uppercase;
            letter-spacing: 0.15em;
        }
        .sidebar-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.625rem 0.875rem;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            line-height: 1.25rem;
            font-weight: 600;
            transition: all 0.2s ease;
            text-decoration: none;
            margin-bottom: 2px;
        }
        .sidebar-item-active {
            background-color: #eff6ff; /* blue-50 */
            color: #2563eb; /* blue-600 */
        }
        .sidebar-item-inactive {
            color: #64748b; /* slate-500 */
        }
        .sidebar-item-inactive:hover {
            color: #0f172a; /* slate-900 */
            background-color: #f8fafc; /* slate-50 */
        }
    </style>

    @stack('styles')
    @stack('head')
</head>
<body class="h-full bg-slate-50 font-sans antialiased" x-data="{ sidebarOpen: false }">

{{-- Mobile overlay --}}
<div x-show="sidebarOpen" x-cloak
     x-transition:enter="transition-opacity ease-linear duration-200"
     x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-200"
     x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     @click="sidebarOpen = false"
     class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm lg:hidden"></div>

{{-- ─── SIDEBAR ────────────────────────────────────────────────────────────── --}}
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
       class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200 flex flex-col
              transform transition-transform duration-300 ease-in-out shadow-sm
              lg:translate-x-0">

    {{-- Logo + brand --}}
    <div class="flex items-center gap-3 h-16 px-5 border-b border-slate-100 shrink-0">
        <a href="{{ route('home') }}" class="flex items-center flex-1 min-w-0 group">
            @php $logo = \App\Models\Setting::get('site_logo'); @endphp
            @if($logo)
                <img src="{{ storageUrl($logo) }}" alt="{{ \App\Models\Setting::get('site_name', 'Skolah.com') }}" class="h-11 w-auto object-contain group-hover:scale-105 transition-all">
            @else
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 bg-blue-600 rounded-xl flex items-center justify-center shadow group-hover:scale-105 transition-all">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-slate-900 font-black text-[15px] tracking-tight block">{{ \App\Models\Setting::get('site_name', 'Skolah.com') }}</span>
                        <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded uppercase tracking-wider block w-fit mt-0.5">Admin Panel</span>
                    </div>
                </div>
            @endif
        </a>
        <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-slate-900 ml-auto transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Admin profile chip --}}
    <div class="px-4 py-4 border-b border-slate-100">
        <div class="flex items-center gap-3 bg-slate-50 border border-slate-100 rounded-xl px-3 py-2.5 shadow-sm">
            <img src="{{ avatarUrl(auth()->user()) }}"
                 alt="{{ auth()->user()->name }}"
                 class="w-9 h-9 rounded-full object-cover ring-2 ring-white shadow-sm shrink-0">
            <div class="min-w-0 flex-1">
                <p class="text-sm font-bold text-slate-900 truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs font-medium text-slate-500 truncate">{{ auth()->user()->email }}</p>
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto px-3 py-2 space-y-0.5 scrollbar-thin">

        {{-- Overview --}}
        <p class="sidebar-group-label">Overview</p>

        <a href="{{ route('admin.dashboard') }}"
           class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'sidebar-item-active' : 'sidebar-item-inactive' }}">
            @include('layouts.partials.icon', ['name' => 'home', 'class' => 'w-5 h-5 shrink-0'])
            Dashboard
        </a>
        <a href="{{ route('admin.analytics') }}"
           class="sidebar-item {{ request()->routeIs('admin.analytics') ? 'sidebar-item-active' : 'sidebar-item-inactive' }}">
            @include('layouts.partials.icon', ['name' => 'chart-bar', 'class' => 'w-5 h-5 shrink-0'])
            Analitik
        </a>

        {{-- Konten --}}
        <p class="sidebar-group-label">Konten</p>

        <a href="{{ route('admin.courses.index') }}"
           class="sidebar-item {{ request()->routeIs('admin.courses*') ? 'sidebar-item-active' : 'sidebar-item-inactive' }}">
            @include('layouts.partials.icon', ['name' => 'book-open', 'class' => 'w-5 h-5 shrink-0'])
            Kursus
        </a>
        <a href="{{ route('admin.bundles.index') }}"
           class="sidebar-item {{ request()->routeIs('admin.bundles*') ? 'sidebar-item-active' : 'sidebar-item-inactive' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            Bundle Kursus
        </a>
        <a href="{{ route('admin.bootcamps.index') }}"
           class="sidebar-item {{ request()->routeIs('admin.bootcamps*') ? 'sidebar-item-active' : 'sidebar-item-inactive' }}">
            @include('layouts.partials.icon', ['name' => 'academic-cap', 'class' => 'w-5 h-5 shrink-0'])
            Bootcamp
        </a>
        <a href="{{ route('admin.institutions.index') }}"
           class="sidebar-item {{ request()->routeIs('admin.institutions*') ? 'sidebar-item-active' : 'sidebar-item-inactive' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            Lembaga
        </a>
        <a href="{{ route('admin.books.index') }}"
           class="sidebar-item {{ request()->routeIs('admin.books.index') || (request()->routeIs('admin.books*') && !request()->routeIs('admin.book-orders*')) ? 'sidebar-item-active' : 'sidebar-item-inactive' }}">
            @include('layouts.partials.icon', ['name' => 'library', 'class' => 'w-5 h-5 shrink-0'])
            Buku
        </a>
        <a href="{{ route('admin.book-orders.index') }}"
           class="sidebar-item {{ request()->routeIs('admin.book-orders*') ? 'sidebar-item-active' : 'sidebar-item-inactive' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
            </svg>
            Pengiriman Buku
        </a>
        <a href="{{ route('admin.categories.index') }}"
           class="sidebar-item {{ request()->routeIs('admin.categories*') ? 'sidebar-item-active' : 'sidebar-item-inactive' }}">
            @include('layouts.partials.icon', ['name' => 'folder', 'class' => 'w-5 h-5 shrink-0'])
            Kategori
        </a>
        <a href="{{ route('admin.posts.index') }}"
           class="sidebar-item {{ request()->routeIs('admin.posts*') ? 'sidebar-item-active' : 'sidebar-item-inactive' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v4m2 10l2 2m-2-2a2 2 0 112-2 2 2 0 01-2 2zM11 8h3m-3 4h2"/>
            </svg>
            Kelola Blog
        </a>
        <a href="{{ route('admin.tags.index') }}"
           class="sidebar-item {{ request()->routeIs('admin.tags*') ? 'sidebar-item-active' : 'sidebar-item-inactive' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
            Tag
        </a>
        <a href="{{ route('admin.testimonials.index') }}"
           class="sidebar-item {{ request()->routeIs('admin.testimonials*') ? 'sidebar-item-active' : 'sidebar-item-inactive' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            Testimoni
        </a>
        <a href="{{ route('admin.certificate-templates.index') }}"
           class="sidebar-item {{ request()->routeIs('admin.certificate-templates*') ? 'sidebar-item-active' : 'sidebar-item-inactive' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Desain Sertifikat
        </a>

        {{-- Event Management --}}
        <p class="sidebar-group-label">Event Management</p>

        <a href="{{ route('admin.tickets.scan') }}"
           class="sidebar-item {{ request()->routeIs('admin.tickets.scan') ? 'sidebar-item-active' : 'sidebar-item-inactive' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
            </svg>
            Scan & Absensi
        </a>

        <a href="{{ route('admin.tickets.index') }}"
           class="sidebar-item {{ request()->routeIs('admin.tickets.index', 'admin.tickets.show-bootcamp') ? 'sidebar-item-active' : 'sidebar-item-inactive' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
            </svg>
            Daftar Absensi
        </a>

        {{-- Transaksi --}}
        <p class="sidebar-group-label">Transaksi</p>

        <a href="{{ route('admin.orders.index') }}"
           class="sidebar-item {{ request()->routeIs('admin.orders*') ? 'sidebar-item-active' : 'sidebar-item-inactive' }}">
            @include('layouts.partials.icon', ['name' => 'credit-card', 'class' => 'w-5 h-5 shrink-0'])
            Pesanan
            {{-- Badge jumlah order pending (placeholder) --}}
            {{-- <span class="ml-auto text-xs bg-red-500 text-white px-1.5 py-0.5 rounded-full font-bold">3</span> --}}
        </a>
        <a href="{{ route('admin.memberships.index') }}"
           class="sidebar-item {{ request()->routeIs('admin.memberships*') ? 'sidebar-item-active' : 'sidebar-item-inactive' }}">
            @include('layouts.partials.icon', ['name' => 'star', 'class' => 'w-5 h-5 shrink-0'])
            Membership
        </a>
        <a href="{{ route('admin.promo-codes.index') }}"
           class="sidebar-item {{ request()->routeIs('admin.promo-codes*') ? 'sidebar-item-active' : 'sidebar-item-inactive' }}">
            @include('layouts.partials.icon', ['name' => 'tag', 'class' => 'w-5 h-5 shrink-0'])
            Kode Promo
        </a>
        <a href="{{ route('admin.flash-sales.index') }}"
           class="sidebar-item {{ request()->routeIs('admin.flash-sales*') ? 'sidebar-item-active' : 'sidebar-item-inactive' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            Flash Sale
        </a>

        {{-- Pengguna --}}
        <p class="sidebar-group-label">Pengguna</p>

        <a href="{{ route('admin.users.index') }}"
           class="sidebar-item {{ request()->routeIs('admin.users*') ? 'sidebar-item-active' : 'sidebar-item-inactive' }}">
            @include('layouts.partials.icon', ['name' => 'users', 'class' => 'w-5 h-5 shrink-0'])
            Pengguna
        </a>
        <a href="{{ route('admin.instructors.index') }}"
           class="sidebar-item {{ request()->routeIs('admin.instructors*') ? 'sidebar-item-active' : 'sidebar-item-inactive' }}">
            @include('layouts.partials.icon', ['name' => 'academic-cap', 'class' => 'w-5 h-5 shrink-0'])
            Manajemen Instruktur
        </a>
        <a href="{{ route('admin.instructor-applications.index') }}"
           class="sidebar-item {{ request()->routeIs('admin.instructor-applications*') ? 'sidebar-item-active' : 'sidebar-item-inactive' }}">
            @include('layouts.partials.icon', ['name' => 'academic-cap', 'class' => 'w-5 h-5 shrink-0'])
            Pengajuan Instruktur
            @php $pendingApps = \App\Models\InstructorApplication::pending()->count(); @endphp
            @if($pendingApps > 0)
                <span class="ml-auto text-[10px] px-1.5 py-0.5 rounded-full bg-red-500 text-white font-bold">{{ $pendingApps }}</span>
            @endif
        </a>
        <a href="{{ route('admin.testimonials.index') }}"
           class="sidebar-item {{ request()->routeIs('admin.testimonials*') ? 'sidebar-item-active' : 'sidebar-item-inactive' }}">
            @include('layouts.partials.icon', ['name' => 'chat-alt', 'class' => 'w-5 h-5 shrink-0'])
            Testimoni
        </a>

        {{-- Tampilan --}}
        <p class="sidebar-group-label">Tampilan</p>

        <a href="{{ route('admin.banners.index') }}"
           class="sidebar-item {{ request()->routeIs('admin.banners*') ? 'sidebar-item-active' : 'sidebar-item-inactive' }}">
            @include('layouts.partials.icon', ['name' => 'photograph', 'class' => 'w-5 h-5 shrink-0'])
            Banner
        </a>
        <a href="{{ route('admin.benefits.index') }}"
           class="sidebar-item {{ request()->routeIs('admin.benefits*') ? 'sidebar-item-active' : 'sidebar-item-inactive' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            Benefit & Layanan
        </a>
        <a href="{{ route('admin.landing-programs.index') }}"
           class="sidebar-item {{ request()->routeIs('admin.landing-programs*') ? 'sidebar-item-active' : 'sidebar-item-inactive' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            Program Unggulan
        </a>
        <a href="{{ route('admin.campuses.index') }}"
           class="sidebar-item {{ request()->routeIs('admin.campuses*') ? 'sidebar-item-active' : 'sidebar-item-inactive' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            Kampus Offline
        </a>
        <a href="{{ route('admin.galleries.index') }}"
           class="sidebar-item {{ request()->routeIs('admin.galleries*') ? 'sidebar-item-active' : 'sidebar-item-inactive' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Galeri Kegiatan
        </a>
        <a href="{{ route('admin.settings.index') }}"
           class="sidebar-item {{ request()->routeIs('admin.settings*') ? 'sidebar-item-active' : 'sidebar-item-inactive' }}">
            @include('layouts.partials.icon', ['name' => 'cog', 'class' => 'w-5 h-5 shrink-0'])
            Pengaturan
        </a>

        {{-- Keamanan --}}
        <p class="sidebar-group-label">Keamanan</p>

        <a href="{{ route('admin.audit-logs.index') }}"
           class="sidebar-item {{ request()->routeIs('admin.audit-logs*') ? 'sidebar-item-active' : 'sidebar-item-inactive' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Audit Log
        </a>
        <a href="{{ route('admin.backups.index') }}"
           class="sidebar-item {{ request()->routeIs('admin.backups*') ? 'sidebar-item-active' : 'sidebar-item-inactive' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10a2 2 0 002 2h12a2 2 0 002-2V7M4 7l8 6 8-6M4 7h16"/>
            </svg>
            Backup Manager
        </a>

    </nav>

    {{-- Footer sidebar --}}
    <div class="px-3 py-4 border-t border-slate-100 space-y-1">
        <a href="{{ route('home') }}" target="_blank"
           class="sidebar-item sidebar-item-inactive">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
            </svg>
            Lihat Situs
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sidebar-item sidebar-item-inactive hover:text-red-600 hover:bg-red-50 w-full transition-colors">
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
<div class="lg:pl-64 flex flex-col min-h-screen bg-gray-50">

    {{-- Top Header --}}
    <header class="sticky top-0 z-30 h-16 bg-white border-b border-gray-200 flex items-center px-4 sm:px-6 gap-4 shadow-sm">
        <button @click="sidebarOpen = true"
                class="lg:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        {{-- Breadcrumb / page header slot --}}
        <div class="flex-1 min-w-0 flex items-center gap-2">
            <span class="text-xs font-bold bg-red-100 text-red-700 px-2 py-0.5 rounded-full uppercase tracking-wider shrink-0">
                Admin
            </span>
            @yield('page-header')
        </div>

        {{-- Right actions --}}
        <div class="flex items-center gap-2">
            {{-- Global search --}}
            <div x-data="{ searchOpen: false }" class="relative">
                <button @click="searchOpen = !searchOpen"
                        class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>
                <div x-show="searchOpen" x-cloak @click.outside="searchOpen = false" @keydown.escape.window="searchOpen = false"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="absolute right-0 mt-2 w-72 bg-white rounded-xl shadow-xl ring-1 ring-gray-200 z-50 overflow-hidden">
                    <div class="flex items-center gap-2 px-4 py-3 border-b border-gray-100">
                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" placeholder="Cari di admin panel..." autofocus
                               class="flex-1 text-sm outline-none text-gray-900 placeholder-gray-400">
                    </div>
                    <div class="px-4 py-3 text-xs text-gray-400 text-center">Tekan Enter untuk mencari</div>
                </div>
            </div>

            {{-- Notification --}}
            <x-notification-dropdown />

            {{-- Admin avatar dropdown --}}
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                        class="flex items-center gap-2 px-2 py-1 rounded-xl hover:bg-gray-100 transition-colors">
                    <img src="{{ avatarUrl(auth()->user()) }}"
                         alt="{{ auth()->user()->name }}"
                         class="w-8 h-8 rounded-full object-cover ring-2 ring-red-100">
                    <div class="hidden sm:block text-left">
                        <p class="text-xs font-semibold text-gray-900 leading-tight max-w-[100px] truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-red-600 font-medium">Administrator</p>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 hidden sm:block" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="open" x-cloak @click.outside="open = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="absolute right-0 mt-2 w-52 rounded-xl bg-white shadow-xl ring-1 ring-gray-200 overflow-hidden z-50">
                    <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                        <p class="text-xs text-gray-500">Masuk sebagai</p>
                        <p class="text-sm font-bold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                    </div>
                    <div class="py-1">
                        <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Pengaturan
                        </a>
                        <a href="{{ route('home') }}" target="_blank" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            Lihat Situs
                        </a>
                    </div>
                    <div class="border-t border-gray-100">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- Flash Messages --}}
    @if(session('success') || session('error') || session('warning') || session('info'))
        <div class="px-4 sm:px-6 pt-4 space-y-2">
            @if(session('success'))
                <div x-data="{ s: true }" x-show="s" x-transition class="flex items-center justify-between gap-3 rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
                    <div class="flex items-center gap-2"><svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg> {{ session('success') }}</div>
                    <button @click="s = false"><svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
            @endif
            @if(session('error'))
                <div x-data="{ s: true }" x-show="s" x-transition class="flex items-center justify-between gap-3 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
                    <div class="flex items-center gap-2"><svg class="w-4 h-4 text-red-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/></svg> {{ session('error') }}</div>
                    <button @click="s = false"><svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
            @endif
            @if(session('warning'))
                <div x-data="{ s: true }" x-show="s" x-transition class="flex items-center justify-between gap-3 rounded-xl bg-yellow-50 border border-yellow-200 px-4 py-3 text-sm text-yellow-800">
                    <div class="flex items-center gap-2"><svg class="w-4 h-4 text-yellow-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg> {{ session('warning') }}</div>
                    <button @click="s = false"><svg class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
            @endif
            @if(session('info'))
                <div x-data="{ s: true }" x-show="s" x-transition class="flex items-center justify-between gap-3 rounded-xl bg-blue-50 border border-blue-200 px-4 py-3 text-sm text-blue-800">
                    <span>{{ session('info') }}</span>
                    <button @click="s = false"><svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
            @endif
        </div>
    @endif

    {{-- Content --}}
    <main class="flex-1 px-4 sm:px-6 py-6">
        @yield('content')
    </main>

    <footer class="border-t border-gray-200 bg-white px-4 sm:px-6 py-3">
        <div class="flex items-center justify-between text-xs text-gray-400">
            <span>&copy; {{ date('Y') }} Skolah.com — Admin Panel v1.0</span>
            <span>Laravel {{ app()->version() }}</span>
        </div>
    </footer>
</div>
{{-- ─── END MAIN ───────────────────────────────────────────────────────────── --}}

@livewireScripts(['nonce' => $cspNonce ?? ''])
@stack('scripts')
</body>
</html>
