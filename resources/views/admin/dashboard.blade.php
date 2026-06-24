@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('page-header')
    <span class="text-base font-semibold text-gray-900">Dashboard</span>
@endsection

@section('content')
    {{-- Stat Cards Row 1 --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow border border-slate-100 relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-br from-green-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="relative flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-slate-500 mb-1">Total Revenue</p>
                    <p class="text-3xl font-black text-slate-900 tracking-tight">{{ rupiah($totalRevenue) }}</p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-green-100 flex items-center justify-center shrink-0 shadow-inner group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="relative mt-4 flex items-center text-xs">
                <span class="text-green-600 font-bold bg-green-50 px-2 py-1 rounded-md">{{ rupiah($monthRevenue) }}</span>
                <span class="text-slate-400 ml-2">pendapatan bulan ini</span>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow border border-slate-100 relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="relative flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-slate-500 mb-1">Total Pengguna</p>
                    <p class="text-3xl font-black text-slate-900 tracking-tight">{{ number_format($totalUsers) }}</p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-blue-100 flex items-center justify-center shrink-0 shadow-inner group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
            </div>
            <div class="relative mt-4 flex items-center text-xs">
                <span class="text-blue-600 font-bold bg-blue-50 px-2 py-1 rounded-md">+{{ $newUsersThisWeek }}</span>
                <span class="text-slate-400 ml-2">pengguna minggu ini</span>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow border border-slate-100 relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="relative flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-slate-500 mb-1">Order Hari Ini</p>
                    <p class="text-3xl font-black text-slate-900 tracking-tight">{{ $totalOrdersToday }}</p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-purple-100 flex items-center justify-center shrink-0 shadow-inner group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
            </div>
            <div class="relative mt-4 flex items-center text-xs">
                <span class="text-purple-600 font-bold bg-purple-50 px-2 py-1 rounded-md">{{ $pendingOrders }} pending</span>
                <span class="text-slate-400 ml-2">menunggu proses</span>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow border border-slate-100 relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-br from-yellow-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="relative flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-slate-500 mb-1">Member Aktif</p>
                    <p class="text-3xl font-black text-slate-900 tracking-tight">{{ $activeMembers }}</p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-yellow-100 flex items-center justify-center shrink-0 shadow-inner group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                </div>
            </div>
            <div class="relative mt-4 flex items-center text-xs">
                <span class="text-slate-400">Total member langganan</span>
            </div>
        </div>
    </div>

    {{-- Stat Cards Row 2 (products) --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-primary-100 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-900">{{ $totalCourses }}</p>
                <p class="text-xs text-gray-500">Kursus</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-secondary-100 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-secondary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-900">{{ $totalBootcamps }}</p>
                <p class="text-xs text-gray-500">Bootcamp</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-900">{{ $totalBooks }}</p>
                <p class="text-xs text-gray-500">Buku</p>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    {{-- SECTION 1: Quick Action Panel --}}
    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Aksi Cepat</p>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <a href="{{ route('admin.courses.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl border border-dashed border-primary-300 hover:bg-primary-50 hover:border-primary-400 transition-all group">
                <div class="w-8 h-8 bg-primary-100 rounded-lg flex items-center justify-center group-hover:bg-primary-200 transition-colors flex-shrink-0">
                    <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                </div>
                <span class="text-sm font-medium text-gray-700 group-hover:text-primary-700">Kelola Kursus</span>
            </a>
            <a href="{{ route('admin.bootcamps.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl border border-dashed border-purple-300 hover:bg-purple-50 hover:border-purple-400 transition-all group">
                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center group-hover:bg-purple-200 transition-colors flex-shrink-0">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                </div>
                <span class="text-sm font-medium text-gray-700 group-hover:text-purple-700">Kelola Bootcamp</span>
            </a>
            <a href="{{ route('admin.promo-codes.create') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl border border-dashed border-green-300 hover:bg-green-50 hover:border-green-400 transition-all group">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-200 transition-colors flex-shrink-0">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                </div>
                <span class="text-sm font-medium text-gray-700 group-hover:text-green-700">Promo Code</span>
            </a>
            <a href="{{ route('admin.orders.index') }}?status=pending" class="flex items-center gap-3 px-4 py-3 rounded-xl border border-dashed border-orange-300 hover:bg-orange-50 hover:border-orange-400 transition-all group">
                <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center group-hover:bg-orange-200 transition-colors flex-shrink-0">
                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <span class="text-sm font-medium text-gray-700 group-hover:text-orange-700">Order Pending</span>
            </a>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    {{-- SECTION 2: Alert Cards (perlu tindakan) --}}
    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    @if($alertBootcampPending > 0 || $alertBookUnprocessed > 0 || $alertInstructorPending > 0 || $alertOrderExpired > 0)
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

        @if($alertInstructorPending > 0)
        <a href="{{ route('admin.instructor-applications.index') }}" class="group bg-yellow-50 border border-yellow-200 rounded-2xl p-4 flex items-start gap-3 hover:bg-yellow-100 hover:border-yellow-300 transition-all">
            <div class="w-9 h-9 bg-yellow-200 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5 group-hover:bg-yellow-300 transition-colors">
                <svg class="w-5 h-5 text-yellow-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-yellow-800">{{ $alertInstructorPending }}</p>
                <p class="text-xs font-medium text-yellow-700 leading-tight">Lamaran Instruktur<br>menunggu review</p>
            </div>
        </a>
        @endif

        @if($alertBootcampPending > 0)
        <a href="{{ route('admin.bootcamps.index') }}" class="group bg-purple-50 border border-purple-200 rounded-2xl p-4 flex items-start gap-3 hover:bg-purple-100 hover:border-purple-300 transition-all">
            <div class="w-9 h-9 bg-purple-200 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5 group-hover:bg-purple-300 transition-colors">
                <svg class="w-5 h-5 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-purple-800">{{ $alertBootcampPending }}</p>
                <p class="text-xs font-medium text-purple-700 leading-tight">Pendaftar Bootcamp<br>belum terkonfirmasi</p>
            </div>
        </a>
        @endif

        @if($alertBookUnprocessed > 0)
        <a href="{{ route('admin.book-orders.index') }}" class="group bg-blue-50 border border-blue-200 rounded-2xl p-4 flex items-start gap-3 hover:bg-blue-100 hover:border-blue-300 transition-all">
            <div class="w-9 h-9 bg-blue-200 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5 group-hover:bg-blue-300 transition-colors">
                <svg class="w-5 h-5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-blue-800">{{ $alertBookUnprocessed }}</p>
                <p class="text-xs font-medium text-blue-700 leading-tight">Pesanan Buku<br>belum diproses</p>
            </div>
        </a>
        @endif

        @if($alertOrderExpired > 0)
        <a href="{{ route('admin.orders.index') }}?status=pending" class="group bg-red-50 border border-red-200 rounded-2xl p-4 flex items-start gap-3 hover:bg-red-100 hover:border-red-300 transition-all">
            <div class="w-9 h-9 bg-red-200 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5 group-hover:bg-red-300 transition-colors">
                <svg class="w-5 h-5 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-red-800">{{ $alertOrderExpired }}</p>
                <p class="text-xs font-medium text-red-700 leading-tight">Order Pending<br>lebih dari 24 jam</p>
            </div>
        </a>
        @endif

    </div>
    @endif

    {{-- Chart + Top Courses --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        {{-- Revenue Chart --}}
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow border border-slate-100 flex flex-col">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-extrabold text-slate-900">Pendapatan 30 Hari Terakhir</h2>
                    <p class="text-sm text-slate-500">Melihat tren transaksi di platform Anda.</p>
                </div>
                <a href="{{ route('admin.analytics') }}" class="text-sm text-blue-600 hover:text-blue-700 font-bold bg-blue-50 px-4 py-2 rounded-xl transition-colors">Lihat Analitik Lebih Dalam →</a>
            </div>
            <div style="height: 300px;" class="w-full">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        {{-- Top Courses --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow border border-slate-100">
            <h2 class="text-lg font-extrabold text-slate-900 mb-6">Kursus Terpopuler</h2>
            <div class="space-y-4">
                @forelse($topCourses as $i => $course)
                    <div class="flex items-center gap-4 group">
                        <span class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 text-sm font-black flex items-center justify-center shrink-0 group-hover:bg-blue-600 group-hover:text-white transition-colors">{{ $i + 1 }}</span>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-bold text-slate-900 line-clamp-2 leading-snug">{{ $course->title }}</p>
                            <p class="text-xs text-blue-600 font-semibold mt-1">{{ number_format($course->total_students) }} Siswa Aktif</p>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center bg-slate-50 rounded-xl border border-dashed border-slate-200">
                        <p class="text-sm text-slate-400 font-medium">Belum ada data kursus.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    {{-- SECTION 3: Revenue Breakdown per Kategori --}}
    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-base font-bold text-gray-900">Revenue per Kategori Produk</h2>
                <p class="text-xs text-gray-400 mt-0.5">Total semua waktu berdasarkan jenis produk</p>
            </div>
            <a href="{{ route('admin.analytics') }}" class="text-xs text-primary-600 hover:text-primary-700 font-medium">Analitik Lengkap →</a>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($revenueBreakdown as $item)
            <div class="bg-gray-50 rounded-xl p-4">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium text-gray-600">{{ $item['label'] }}</span>
                    <span class="text-xs font-bold text-gray-500 bg-white border border-gray-200 rounded-lg px-2 py-0.5">{{ $item['pct'] }}%</span>
                </div>
                <p class="text-lg font-bold text-gray-900 mb-3">{{ rupiah($item['value']) }}</p>
                <div class="w-full bg-gray-200 rounded-full h-1.5">
                    <div class="{{ $item['color'] }} h-1.5 rounded-full transition-all duration-700"
                         style="width: {{ $item['pct'] }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Recent Orders Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-bold text-gray-900">Pesanan Terbaru</h2>
            <a href="{{ route('admin.orders.index') }}" class="text-xs text-primary-600 hover:text-primary-700 font-medium">Lihat Semua →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Order</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">User</th>
                        <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Total</th>
                        <th class="text-center px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentOrders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 font-mono text-xs text-gray-700">{{ $order->order_number }}</td>
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-2">
                                    <img src="{{ $order->user?->avatar_url }}" class="w-6 h-6 rounded-full" alt="">
                                    <span class="text-gray-900 font-medium truncate max-w-[120px]">{{ $order->user?->name ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-3 text-right font-semibold text-gray-900">{{ $order->total_formatted }}</td>
                            <td class="px-6 py-3 text-center">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold
                                    {{ $order->status === 'paid' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                    {{ $order->status === 'failed' ? 'bg-red-100 text-red-700' : '' }}
                                    {{ $order->status === 'refunded' ? 'bg-purple-100 text-purple-700' : '' }}
                                ">{{ $order->status_label }}</span>
                            </td>
                            <td class="px-6 py-3 text-right text-gray-500 text-xs">{{ $order->created_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-8 text-center text-gray-400">Belum ada pesanan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script nonce="{{ $cspNonce ?? '' }}">
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    
    // Create modern smooth gradient background
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(37, 99, 235, 0.4)');   // blue-600 max opacity
    gradient.addColorStop(0.8, 'rgba(37, 99, 235, 0.0)');   // fade to clear completely

    // Adjust global chart fonts
    Chart.defaults.font.family = "'Inter', 'sans-serif'";
    Chart.defaults.color = '#64748b'; // slate-500

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($chartData['labels']),
            datasets: [{
                label: 'Pendapatan',
                data: @json($chartData['data']),
                borderColor: '#2563EB', // blue-600
                backgroundColor: gradient,
                fill: true,
                tension: 0.4, // Smooth curve
                borderWidth: 3,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#2563EB',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointHoverBackgroundColor: '#2563EB',
                pointHoverBorderColor: '#ffffff',
                pointHoverBorderWidth: 3,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index',
            },
            plugins: {
                legend: { display: false },
                tooltip: { 
                    backgroundColor: '#0f172a',
                    titleFont: { size: 13, weight: 'bold' },
                    bodyFont: { size: 14, weight: 'bold' },
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: { 
                        label: function(ctx) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw);
                        } 
                    } 
                }
            },
            scales: {
                y: { 
                    beginAtZero: true, 
                    border: { display: false },
                    ticks: { 
                        maxTicksLimit: 6,
                        callback: v => {
                            if(v >= 1000000) return 'Rp ' + (v/1000000) + ' Jt';
                            if(v >= 1000) return 'Rp ' + (v/1000) + ' rb';
                            return 'Rp ' + v;
                        },
                        font: { size: 11, weight: '600' }
                    }, 
                    grid: { color: '#f1f5f9', borderDash: [4, 4] } 
                },
                x: { 
                    grid: { display: false },
                    border: { display: false },
                    ticks: { maxTicksLimit: 8, font: { size: 11, weight: '600' } } 
                }
            }
        }
    });
});
</script>
@endpush
