@extends('layouts.app')

@section('content')

{{-- ── Hero / Page Header ─────────────────────────────────────────────────── --}}
<section class="relative bg-white pt-28 pb-16 overflow-hidden border-b border-slate-100">
    {{-- Decorative background --}}
    <div class="absolute inset-0 pointer-events-none overflow-hidden" aria-hidden="true">
        <div class="absolute -top-[10%] -right-[10%] w-[40%] h-[40%] bg-blue-100/50 rounded-full mix-blend-multiply filter blur-[80px] opacity-60"></div>
        <div class="absolute top-[30%] -left-[5%] w-[35%] h-[35%] bg-purple-100/50 rounded-full mix-blend-multiply filter blur-[80px] opacity-50"></div>
        <div class="absolute inset-0 opacity-[0.35]" style="background-image: radial-gradient(#cbd5e1 1px, transparent 1px); background-size: 40px 40px;"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 z-10">

        <x-breadcrumb :items="[['label' => 'Kursus Online']]" theme="light" />

        <div class="mt-4 flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
            <div>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900">
                    Kursus <span class="text-blue-600">Online</span>
                </h1>
                <p class="mt-2 text-slate-500 text-sm max-w-lg">
                    {{ number_format($totalCount) }}+ kursus dari instruktur terbaik Indonesia — temukan yang sesuai dengan karir impianmu.
                </p>
            </div>
            <div class="flex items-center gap-3 text-sm text-slate-500">
                <span>📚 {{ number_format($totalCount) }} kursus</span>
                <span>·</span>
                <span>🏷️ {{ count($categories) }} kategori</span>
            </div>
        </div>
    </div>
</section>

@if($activeFlashSale && $activeFlashSale->isRunning && $activeFlashSale->items->count())
    <section class="py-8 bg-white overflow-hidden" 
             x-data="{ 
                endTime: new Date('{{ $activeFlashSale->end_at->toIso8601String() }}').getTime(),
                days: 0, hours: 0, minutes: 0, seconds: 0,
                updateTimer() {
                    let now = new Date().getTime();
                    let distance = this.endTime - now;
                    if (distance < 0) {
                        this.days = 0; this.hours = 0; this.minutes = 0; this.seconds = 0;
                        return;
                    }
                    this.days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    this.hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    this.minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    this.seconds = Math.floor((distance % (1000 * 60)) / 1000);
                }
             }" 
             x-init="updateTimer(); setInterval(() => updateTimer(), 1000)">
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gradient-to-br from-rose-600 via-pink-600 to-rose-700 rounded-3xl p-6 md:p-8 shadow-xl shadow-rose-200 relative overflow-hidden">
                {{-- Decorative Elements --}}
                <div class="absolute top-0 right-0 w-48 h-48 bg-white/10 rounded-full -mr-24 -mt-24 blur-3xl"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-rose-900/20 rounded-full -ml-24 -mb-24 blur-3xl"></div>
                
                <div class="flex flex-col lg:flex-row items-center justify-between gap-6 relative z-10">
                    <div class="text-center lg:text-left">
                        <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-white text-[10px] font-black uppercase tracking-widest mb-3 border border-white/30">
                            <span class="w-1.5 h-1.5 rounded-full bg-yellow-400 animate-ping"></span>
                            Promo Terbatas
                        </div>
                        <h2 class="text-2xl md:text-3xl font-black text-white tracking-tight">
                            ⚡ {{ $activeFlashSale->title }}
                        </h2>
                        <p class="mt-2 text-rose-50 text-sm opacity-90 max-w-lg">
                            {{ $activeFlashSale->description ?: 'Kejar diskon spesial hari ini sebelum waktu habis!' }}
                        </p>
                    </div>
                    
                    {{-- Countdown Timer --}}
                    <div class="flex items-center gap-2 sm:gap-4">
                        <div class="flex flex-col items-center">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 bg-white rounded-xl flex items-center justify-center shadow-lg">
                                <span class="text-lg sm:text-xl font-black text-rose-600" x-text="String(days).padStart(2, '0')">00</span>
                            </div>
                            <span class="mt-1 text-[8px] font-black text-white uppercase tracking-wider">Hari</span>
                        </div>
                        <span class="text-xl font-black text-white mt-[-1.5rem]">:</span>
                        <div class="flex flex-col items-center">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 bg-white rounded-xl flex items-center justify-center shadow-lg">
                                <span class="text-lg sm:text-xl font-black text-rose-600" x-text="String(hours).padStart(2, '0')">00</span>
                            </div>
                            <span class="mt-1 text-[8px] font-black text-white uppercase tracking-wider">Jam</span>
                        </div>
                        <span class="text-xl font-black text-white mt-[-1.5rem]">:</span>
                        <div class="flex flex-col items-center">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 bg-white rounded-xl flex items-center justify-center shadow-lg">
                                <span class="text-lg sm:text-xl font-black text-rose-600" x-text="String(minutes).padStart(2, '0')">00</span>
                            </div>
                            <span class="mt-1 text-[8px] font-black text-white uppercase tracking-wider">Menit</span>
                        </div>
                        <span class="text-xl font-black text-white mt-[-1.5rem]">:</span>
                        <div class="flex flex-col items-center">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 bg-white rounded-xl flex items-center justify-center shadow-lg">
                                <span class="text-lg sm:text-xl font-black text-rose-600" x-text="String(seconds).padStart(2, '0')">00</span>
                            </div>
                            <span class="mt-1 text-[8px] font-black text-white uppercase tracking-wider">Detik</span>
                        </div>
                    </div>
                </div>
                
                {{-- Flash Sale Items Horizontal Scroll --}}
                <div class="mt-8 relative group">
                    <div class="overflow-x-auto pb-4 scrollbar-hide flex gap-4 snap-x">
                        @foreach($activeFlashSale->items as $fsItem)
                            @php $item = $fsItem->itemable; @endphp
                            @if($item)
                                <div class="snap-start shrink-0 w-[240px] bg-white rounded-2xl p-3 shadow-lg border border-rose-100/50 hover:-translate-y-1 transition-all duration-300">
                                    {{-- Image --}}
                                    <div class="relative aspect-video rounded-xl overflow-hidden mb-3">
                                        <x-picture
                                            :src="storageUrl($item->thumbnail ?: $item->image)"
                                            :alt="$item->title ?: $item->name"
                                            class="w-full h-full object-cover" />
                                        <div class="absolute top-2 left-2 px-2 py-0.5 bg-rose-600 text-white text-[8px] font-black uppercase tracking-widest rounded-full">
                                            @php
                                                $discountPercent = round((($item->price - $fsItem->flash_sale_price) / $item->price) * 100);
                                            @endphp
                                            -{{ $discountPercent }}%
                                        </div>
                                    </div>
                                    
                                    {{-- Content --}}
                                    <div class="px-1">
                                        <h3 class="font-bold text-slate-900 text-xs line-clamp-1">{{ $item->title ?: $item->name }}</h3>
                                        <div class="mt-2 flex items-center justify-between">
                                            <div>
                                                <p class="text-[8px] text-slate-400 line-through">{{ rupiah($item->price) }}</p>
                                                <p class="text-sm font-black text-rose-600">{{ rupiah($fsItem->flash_sale_price) }}</p>
                                            </div>
                                            <form action="{{ route('cart.add') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $item->id }}">
                                                <input type="hidden" name="type" value="{{ strtolower($fsItem->item_type_label === 'Kursus' ? 'course' : ($fsItem->item_type_label === 'Bootcamp' ? 'bootcamp' : 'book')) }}">
                                                <button type="submit" class="w-8 h-8 bg-slate-900 text-white rounded-lg flex items-center justify-center hover:bg-rose-600 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                        
                                        @if($fsItem->limit_quantity)
                                            @php $percent = round(($fsItem->sold_quantity / $fsItem->limit_quantity) * 100); @endphp
                                            <div class="mt-3">
                                                <div class="w-full h-1 bg-rose-50 rounded-full overflow-hidden">
                                                    <div class="h-full bg-rose-500 rounded-full" style="width: {{ $percent }}%"></div>
                                                </div>
                                                <div class="flex justify-between mt-1 text-[7px] font-bold text-slate-400 uppercase">
                                                    <span>Terjual {{ $percent }}%</span>
                                                    <span>Sisa {{ $fsItem->remaining_quantity }}</span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        
        <style>
            .scrollbar-hide::-webkit-scrollbar { display: none; }
            .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        </style>
    </section>
@endif

{{-- ── Content ─────────────────────────────────────────────────────────────── --}}
<section class="py-10 bg-white min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Flash messages --}}
        @if(session('success'))
            <x-alert type="success" :message="session('success')" class="mb-6"/>
        @endif

        {{-- Hidden form for sort-only changes (carries existing filters) --}}
        <form id="sort-form" method="GET" action="{{ route('courses.index') }}" class="hidden">
            @if(request('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif
            @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
            @if(request('level'))<input type="hidden" name="level" value="{{ request('level') }}">@endif
            @if(request('price'))<input type="hidden" name="price" value="{{ request('price') }}">@endif
            @if(request('rating'))<input type="hidden" name="rating" value="{{ request('rating') }}">@endif
            <input type="hidden" name="sort" id="sort-hidden-value" value="{{ $sort }}">
        </form>

        {{-- Main form for filter sidebar inputs (uses form="course-filter-form") --}}
        <form id="course-filter-form" method="GET" action="{{ route('courses.index') }}" class="hidden">
            <input type="hidden" name="sort" value="{{ $sort }}">
        </form>

        {{-- ══ Mobile: Filter drawer trigger ═══════════════════════════════ --}}
        <div class="lg:hidden flex items-center justify-between mb-4">
            <p class="text-sm text-gray-500">
                Menampilkan <span class="font-bold text-gray-900">{{ number_format($courses->total()) }}</span> kursus
            </p>
            <button type="button" onclick="document.getElementById('mobile-filter-drawer').classList.toggle('hidden')"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 text-sm font-semibold text-gray-700 hover:border-[#6C63FF] hover:text-[#6C63FF] transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
                Filter
            </button>
        </div>

        {{-- ══ Mobile filter drawer ════════════════════════════════════════ --}}
        <div id="mobile-filter-drawer" class="lg:hidden hidden fixed inset-0 z-50 flex">
            <div class="flex-1 bg-black/50" onclick="this.parentElement.classList.add('hidden')"></div>
            <div class="w-72 bg-white h-full overflow-y-auto shadow-2xl flex flex-col">
                <div class="flex items-center justify-between p-4 border-b">
                    <h3 class="font-bold text-gray-900">Filter Kursus</h3>
                    <button onclick="document.getElementById('mobile-filter-drawer').classList.add('hidden')" class="p-1 text-gray-400 hover:text-gray-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="p-4 flex-1 overflow-y-auto">
                    @include('livewire.partials.course-filter-body')
                </div>
            </div>
        </div>

        {{-- ══ Main layout ═════════════════════════════════════════════════ --}}
        <div class="flex gap-7">

            {{-- ── Desktop sidebar ─────────────────────────────────────── --}}
            <aside id="course-filters" class="hidden lg:flex flex-col w-64 shrink-0 space-y-6">
                <div class="bg-white rounded-2xl border border-gray-100 p-5 sticky top-24">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-gray-900 text-sm">Filter Kursus</h3>
                        @if(request()->hasAny(['q','category','level','price','rating']))
                            <a href="{{ route('courses.index') }}" class="text-[10px] text-red-500 font-bold uppercase hover:underline">Hapus</a>
                        @endif
                    </div>
                    @include('livewire.partials.course-filter-body')
                </div>
            </aside>

            {{-- ── Desktop main ────────────────────────────────────────── --}}
            <div id="course-list" class="flex-1 min-w-0">
                {{-- Search & Sort --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                    <div>
                        <p class="text-sm text-gray-500">
                            Menampilkan <span class="font-bold text-gray-900">{{ number_format($courses->total()) }}</span> kursus
                            @if(request('q'))
                                untuk "<span class="font-semibold text-[#6C63FF]">{{ request('q') }}</span>"
                            @endif
                        </p>
                    </div>

                    {{-- Sort dropdown (changes hidden input + submits form) --}}
                    <div class="flex items-center gap-2">
                        <label class="text-xs text-gray-500 whitespace-nowrap">Urutkan:</label>
                        <select onchange="document.getElementById('sort-hidden-value').value=this.value; document.getElementById('sort-form').submit();"
                                class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#6C63FF]/40 focus:border-[#6C63FF] transition-colors bg-white">
                            @foreach(['popular' => 'Terpopuler', 'newest' => 'Terbaru', 'rating' => 'Rating Tertinggi', 'price_asc' => 'Harga: Rendah ke Tinggi', 'price_desc' => 'Harga: Tinggi ke Rendah'] as $val => $lbl)
                                <option value="{{ $val }}" {{ $sort === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Active filter badges --}}
                @if(request()->hasAny(['category','level','price','rating']))
                <div class="flex flex-wrap gap-2 mb-4">
                    @if(request('category'))
                        <span class="inline-flex items-center gap-1.5 bg-[#6C63FF]/10 text-[#6C63FF] text-xs font-semibold px-3 py-1.5 rounded-full">
                            Kategori: {{ request('category') }}
                            <a href="{{ request()->fullUrlWithoutQuery('category') }}" class="hover:opacity-70">✕</a>
                        </span>
                    @endif
                    @if(request('level'))
                        <span class="inline-flex items-center gap-1.5 bg-amber-100 text-amber-700 text-xs font-semibold px-3 py-1.5 rounded-full">
                            Level: {{ ['beginner'=>'Pemula','intermediate'=>'Menengah','advanced'=>'Mahir'][request('level')] ?? request('level') }}
                            <a href="{{ request()->fullUrlWithoutQuery('level') }}" class="hover:opacity-70">✕</a>
                        </span>
                    @endif
                    @if(request('price'))
                        <span class="inline-flex items-center gap-1.5 bg-green-100 text-green-700 text-xs font-semibold px-3 py-1.5 rounded-full">
                            {{ request('price') === 'free' ? '🆓 Gratis' : '💳 Berbayar' }}
                            <a href="{{ request()->fullUrlWithoutQuery('price') }}" class="hover:opacity-70">✕</a>
                        </span>
                    @endif
                    @if(request('rating'))
                        <span class="inline-flex items-center gap-1.5 bg-amber-100 text-amber-700 text-xs font-semibold px-3 py-1.5 rounded-full">
                            ⭐ ≥ {{ request('rating') }}
                            <a href="{{ request()->fullUrlWithoutQuery('rating') }}" class="hover:opacity-70">✕</a>
                        </span>
                    @endif
                </div>
                @endif

                {{-- Course grid --}}
                @if($courses->count())
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                        @foreach($courses as $course)
                            <div class="course-card">
                                <x-course-card :course="$course"/>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-8">
                        {{ $courses->links() }}
                    </div>
                @else
                    <div class="py-20 text-center rounded-2xl border-2 border-dashed border-gray-200">
                        <p class="text-5xl mb-4">🔍</p>
                        <p class="font-bold text-gray-700 text-lg">Tidak ada kursus ditemukan</p>
                        <p class="text-sm text-gray-400 mt-2 mb-5">Coba ubah filter atau kata kunci pencarian kamu.</p>
                        <a href="{{ route('courses.index') }}"
                           class="inline-block px-6 py-2.5 rounded-xl text-sm font-bold text-white"
                           style="background:#6C63FF">
                            Reset Filter
                        </a>
                    </div>
                @endif
            </div>
        </div>

    </div>
</section>

@endsection
