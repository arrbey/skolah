@extends('layouts.app')

@section('title', 'Book Store — Koleksi Buku Terbaik' . ' | ' . \App\Models\Setting::get('site_name', '' . \App\Models\Setting::get('site_name', 'Skolah.com') . ''))

@section('content')

    {{-- ─── HERO ───────────────────────────────────────────────────────────────── --}}
    <section class="relative bg-white pt-28 pb-16 overflow-hidden border-b border-slate-100">
        {{-- Decorative background --}}
        <div class="absolute inset-0 pointer-events-none overflow-hidden" aria-hidden="true">
            <div class="absolute -top-[10%] -right-[10%] w-[40%] h-[40%] bg-blue-100/50 rounded-full mix-blend-multiply filter blur-[80px] opacity-60"></div>
            <div class="absolute top-[30%] -left-[5%] w-[35%] h-[35%] bg-purple-100/50 rounded-full mix-blend-multiply filter blur-[80px] opacity-50"></div>
            <div class="absolute inset-0 opacity-[0.35]" style="background-image: radial-gradient(#cbd5e1 1px, transparent 1px); background-size: 40px 40px;"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 z-10">
            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-2 text-xs text-slate-400 mb-6">
                <a href="{{ route('home') }}" class="hover:text-blue-600 transition">Beranda</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-slate-700 font-medium">Book Store</span>
            </nav>

            <div class="max-w-3xl">
                <h1 id="book-store-title" class="text-4xl lg:text-5xl font-extrabold text-slate-900 leading-tight mb-4">
                    Book <span class="text-blue-600">Store</span>
                </h1>
                <p class="text-lg text-slate-500 leading-relaxed mb-8 max-w-2xl">
                    Koleksi buku fisik dan e-book terbaik untuk menunjang pembelajaran kamu.
                    Dari penulis berkualitas, tersedia dalam format digital dan cetak.
                </p>

                {{-- Stats --}}
                <div class="flex flex-wrap gap-4">
                    <div class="flex items-center gap-3 bg-white px-5 py-3 rounded-xl shadow-sm border border-slate-200">
                        <div class="w-10 h-10 flex items-center justify-center rounded-lg bg-purple-50">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xl font-bold text-slate-900">{{ $stats['total'] }}</p>
                            <p class="text-xs text-slate-500">Total Buku</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 bg-white px-5 py-3 rounded-xl shadow-sm border border-slate-200">
                        <div class="w-10 h-10 flex items-center justify-center rounded-lg bg-sky-50">
                            <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xl font-bold text-slate-900">{{ $stats['digital'] }}</p>
                            <p class="text-xs text-slate-500">E-Book</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 bg-white px-5 py-3 rounded-xl shadow-sm border border-slate-200">
                        <div class="w-10 h-10 flex items-center justify-center rounded-lg bg-amber-50">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xl font-bold text-slate-900">{{ $stats['physical'] }}</p>
                            <p class="text-xs text-slate-500">Buku Fisik</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ─── FEATURED BOOKS (with discount) ─────────────────────────────────────── --}}
    @if($featuredBooks->isNotEmpty())
    <section class="bg-white py-10 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    🔥 Sedang Diskon
                </h2>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5">
                @foreach($featuredBooks as $book)
                    <div class="relative">
                        {{-- Discount badge overlay --}}
                        @if($book->has_discount)
                            <div class="absolute -top-2 -right-2 z-10 bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-lg shadow-red-500/30">
                                -{{ $book->discount_percent }}%
                            </div>
                        @endif
                        <div class="book-card">
                            <x-book-card :book="$book" />
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ─── MAIN FILTER SECTION ────────────────────────────────────────────────── --}}
    <section class="bg-white py-10 min-h-screen" id="book-content">
        @livewire('book-filter')
    </section>

@endsection
