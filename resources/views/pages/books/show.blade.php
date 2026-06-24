@extends('layouts.app')

@section('title', ($book->meta_title ?? $book->title) . ' | Skolah.com')

@php
    $jsonLd = [
        '@context' => 'https://schema.org',
        '@type' => 'Book',
        'name' => $book->title,
        'author' => [
            '@type' => 'Person',
            'name' => $book->author ?? $book->instructor->name ?? 'Skolah.com',
        ],
        'description' => Str::limit(strip_tags($book->description), 200),
        'bookFormat' => $book->is_digital ? 'EBook' : 'Paperback',
        'image' => $book->cover_image ? storageUrl($book->cover_image) : '',
        'offers' => [
            '@type' => 'Offer',
            'price' => (string) $book->effective_price,
            'priceCurrency' => 'IDR',
            'availability' => $book->is_in_stock ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            'url' => route('books.show', $book->slug),
        ],
        'provider' => [
            '@type' => 'Organization',
            'name' => \App\Models\Setting::get('site_name', 'Skolah.com'),
            'url' => config('app.url'),
        ],
    ];
    if ($book->isbn) { $jsonLd['isbn'] = $book->isbn; }
    if ($book->pages) { $jsonLd['numberOfPages'] = $book->pages; }
    if ($book->publisher) {
        $jsonLd['publisher'] = ['@type' => 'Organization', 'name' => $book->publisher];
    }
@endphp

@push('head')
{{-- JSON-LD Product Schema --}}
<script type="application/ld+json" nonce="{{ $cspNonce ?? '' }}">
{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endpush

@section('content')

    {{-- ─── HERO ───────────────────────────────────────────────────────────────── --}}
    <section class="relative bg-gradient-to-br from-gray-950 via-gray-900 to-gray-950 pt-28 pb-16 overflow-hidden">
        {{-- Blurred cover background --}}
        @if($book->cover_image)
        <div class="absolute inset-0">
            <img src="{{ storageUrl($book->cover_image) }}" alt="" class="w-full h-full object-cover opacity-[0.06] blur-sm scale-110">
        </div>
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-gray-950 via-gray-950/80 to-transparent"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-2 text-xs text-gray-500 mb-8">
                <a href="{{ route('home') }}" class="hover:text-white transition">Beranda</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="{{ route('books.index') }}" class="hover:text-white transition">Book Store</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-300 truncate max-w-xs">{{ $book->title }}</span>
            </nav>

            <div class="grid lg:grid-cols-12 gap-10 items-start">
                {{-- Cover Image --}}
                <div class="lg:col-span-4 xl:col-span-3">
                    <div class="relative group">
                        <div class="absolute -inset-2 bg-gradient-to-br from-purple-500/20 to-sky-500/20 rounded-2xl blur-xl opacity-50 group-hover:opacity-70 transition-opacity"></div>
                        <x-picture
                            :src="$book->cover_image ? storageUrl($book->cover_image) : 'https://placehold.co/400x540/6C63FF/ffffff?text=Buku'"
                            :alt="$book->title"
                            class="relative w-full max-w-sm mx-auto rounded-2xl shadow-2xl shadow-black/40 aspect-[3/4] object-cover" />

                        {{-- Type badge --}}
                        <span class="absolute top-4 left-4 text-xs font-semibold px-3 py-1.5 rounded-full backdrop-blur-sm
                            {{ $book->type === 'digital' ? 'bg-sky-500/20 text-sky-200 ring-1 ring-sky-400/30' : ($book->type === 'both' ? 'bg-violet-500/20 text-violet-200 ring-1 ring-violet-400/30' : 'bg-amber-500/20 text-amber-200 ring-1 ring-amber-400/30') }}">
                            {{ $book->type_label }}
                        </span>

                        {{-- Discount badge --}}
                        @if($book->has_discount)
                        <span class="absolute top-4 right-4 text-xs font-bold px-2.5 py-1 rounded-full bg-red-500 text-white shadow-lg shadow-red-500/30">
                            -{{ $book->discount_percent }}%
                        </span>
                        @endif
                    </div>
                </div>

                {{-- Book Info --}}
                <div class="lg:col-span-5 xl:col-span-6">
                    <h1 class="text-3xl lg:text-4xl font-bold text-white leading-tight mb-3">
                        {{ $book->title }}
                    </h1>

                    {{-- Author & Publisher --}}
                    <div class="flex flex-wrap items-center gap-3 text-sm text-gray-400 mb-4">
                        @if($book->author)
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                {{ $book->author }}
                            </span>
                        @endif
                        @if($book->publisher)
                            <span class="text-gray-700">·</span>
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                {{ $book->publisher }}
                            </span>
                        @endif
                    </div>

                    {{-- Specs badges --}}
                    <div class="flex flex-wrap gap-2 mb-6">
                        @if($book->pages)
                            <span class="text-xs px-3 py-1.5 rounded-full bg-white/5 text-gray-300 ring-1 ring-white/10">
                                📖 {{ $book->pages }} halaman
                            </span>
                        @endif
                        @if($book->isbn)
                            <span class="text-xs px-3 py-1.5 rounded-full bg-white/5 text-gray-300 ring-1 ring-white/10 font-mono">
                                ISBN: {{ $book->isbn }}
                            </span>
                        @endif
                        @if($book->is_physical)
                            <span class="text-xs px-3 py-1.5 rounded-full {{ $book->stock > 0 ? 'bg-green-500/10 text-green-300 ring-1 ring-green-500/30' : 'bg-red-500/10 text-red-300 ring-1 ring-red-500/30' }}">
                                {{ $book->stock > 0 ? "Stok: {$book->stock}" : 'Stok Habis' }}
                            </span>
                        @endif
                    </div>

                    {{-- Description --}}
                    <div x-data="{ expanded: false }" class="mb-6">
                        <h3 class="text-sm font-semibold text-white mb-3">Deskripsi</h3>
                        <div class="relative" :class="!expanded && 'max-h-48 overflow-hidden'">
                            <div class="prose prose-sm prose-invert prose-gray max-w-none text-gray-300 leading-relaxed">
                                {!! nl2br(e($book->description ?? 'Deskripsi buku belum tersedia.')) !!}
                            </div>
                            <div x-show="!expanded" class="absolute bottom-0 left-0 right-0 h-20 bg-gradient-to-t from-gray-950 to-transparent"></div>
                        </div>
                        <button @click="expanded = !expanded"
                            class="mt-2 text-sm text-purple-400 hover:text-purple-300 transition flex items-center gap-1">
                            <span x-text="expanded ? 'Sembunyikan' : 'Baca selengkapnya'"></span>
                            <svg class="w-4 h-4 transition-transform" :class="expanded && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Purchase Card (Desktop) --}}
                <div class="hidden lg:block lg:col-span-3">
                    <div class="sticky top-24">
                        @include('pages.books.partials.purchase-card')
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ─── DETAIL SECTIONS ────────────────────────────────────────────────────── --}}
    <section class="bg-gray-950 py-12 border-t border-white/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-12 gap-10">
                <div class="lg:col-span-8 xl:col-span-9 space-y-10">

                    {{-- ─── Spesifikasi Buku ──────────────────────────────────── --}}
                    <div>
                        <h2 class="text-lg font-bold text-white mb-5 flex items-center gap-2">
                            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Spesifikasi Buku
                        </h2>
                        <div class="grid sm:grid-cols-2 gap-4">
                            @php
                                $specs = collect([
                                    ['label' => 'Judul', 'value' => $book->title, 'icon' => '📚'],
                                    ['label' => 'Penulis', 'value' => $book->author, 'icon' => '✍️'],
                                    ['label' => 'Penerbit', 'value' => $book->publisher, 'icon' => '🏢'],
                                    ['label' => 'Jumlah Halaman', 'value' => $book->pages ? $book->pages . ' halaman' : null, 'icon' => '📖'],
                                    ['label' => 'ISBN', 'value' => $book->isbn, 'icon' => '🔢'],
                                    ['label' => 'Tipe', 'value' => $book->type_label, 'icon' => '📦'],
                                    ['label' => 'Stok', 'value' => $book->is_physical ? ($book->stock > 0 ? $book->stock . ' tersedia' : 'Habis') : 'Unlimited (Digital)', 'icon' => '📊'],
                                ])->filter(fn($s) => $s['value'] !== null);
                            @endphp

                            @foreach($specs as $spec)
                                <div class="flex items-start gap-3 bg-gray-900/50 rounded-xl p-4 ring-1 ring-white/5">
                                    <span class="text-lg">{{ $spec['icon'] }}</span>
                                    <div>
                                        <p class="text-xs text-gray-500">{{ $spec['label'] }}</p>
                                        <p class="text-sm text-white font-medium">{{ $spec['value'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- ─── Instructor / Author Info ──────────────────────────── --}}
                    @if($book->instructor)
                    <div>
                        <h2 class="text-lg font-bold text-white mb-5 flex items-center gap-2">
                            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Tentang Penulis
                        </h2>
                        <div class="bg-gray-900/50 rounded-2xl p-6 ring-1 ring-white/5 flex items-start gap-5">
                            <x-picture
                                :src="$book->instructor->avatar ? storageUrl($book->instructor->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($book->instructor->name) . '&background=6C63FF&color=fff&size=80'"
                                :alt="$book->instructor->name"
                                class="w-16 h-16 rounded-full object-cover ring-2 ring-purple-500/30 flex-shrink-0" />
                            <div>
                                <h3 class="text-base font-semibold text-white">{{ $book->instructor->name }}</h3>
                                @if($book->instructor->bio)
                                    <p class="text-sm text-gray-400 mt-1 leading-relaxed line-clamp-3">{{ $book->instructor->bio }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                </div>

                {{-- Spacer for desktop alignment --}}
                <div class="hidden lg:block lg:col-span-4 xl:col-span-3"></div>
            </div>
        </div>
    </section>

    {{-- ─── RELATED BOOKS ──────────────────────────────────────────────────────── --}}
    @if($relatedBooks->isNotEmpty())
    <section class="bg-gray-950 py-12 border-t border-white/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
                📚 Buku Terkait
            </h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5">
                @foreach($relatedBooks as $related)
                    <x-book-card :book="$related" />
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ─── Mobile Sticky Purchase Bar ─────────────────────────────────────────── --}}
    <div class="lg:hidden fixed bottom-0 inset-x-0 z-40" x-data="{ open: false }">
        {{-- Compact bar --}}
        <div class="bg-gray-900/95 backdrop-blur-xl border-t border-white/10 px-4 py-3 flex items-center justify-between">
            <div>
                @if($book->has_discount)
                    <p class="text-lg font-bold text-white">{{ rupiah($book->discount_price) }}</p>
                    <p class="text-xs text-gray-500 line-through">{{ rupiah($book->price) }}</p>
                @elseif($book->effective_price === 0)
                    <p class="text-lg font-bold text-green-400">Gratis</p>
                @else
                    <p class="text-lg font-bold text-white">{{ rupiah($book->price) }}</p>
                @endif
            </div>
            <button @click="open = !open"
                class="px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold text-sm rounded-xl shadow-lg shadow-purple-500/20">
                @if($hasPurchased)
                    {{ $book->is_digital ? 'Download' : 'Lihat Order' }}
                @else
                    Beli Sekarang
                @endif
            </button>
        </div>

        {{-- Slide-up card --}}
        <div x-show="open" x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full"
             class="absolute bottom-full inset-x-0 bg-gray-900 border-t border-white/10 rounded-t-2xl p-5 shadow-2xl"
             style="display: none;">
            <div class="flex justify-center mb-3">
                <div class="w-10 h-1 bg-gray-700 rounded-full"></div>
            </div>
            @include('pages.books.partials.purchase-card')
        </div>

        {{-- Overlay --}}
        <div x-show="open" @click="open = false"
             class="fixed inset-0 bg-black/40 -z-10" style="display: none;"></div>
    </div>

@endsection
