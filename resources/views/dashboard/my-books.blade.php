@extends('layouts.dashboard')

@section('title', 'Buku Saya')

@section('page-header')
    <h1 class="text-lg font-bold text-gray-900">Buku Saya</h1>
@endsection

@section('content')
<div class="space-y-6">

    {{-- ═══ FILTER TABS ═══════════════════════════════════════════════════════ --}}
    <div class="flex items-center gap-2 flex-wrap">
        @foreach([
            ['key' => 'all',      'label' => 'Semua',     'count' => $stats['all']],
            ['key' => 'digital',  'label' => 'E-Book',    'count' => $stats['digital']],
            ['key' => 'physical', 'label' => 'Fisik',     'count' => $stats['physical']],
        ] as $tab)
            <a href="{{ route('dashboard.my-books', ['filter' => $tab['key']]) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold transition-colors
                      {{ $filter === $tab['key']
                          ? 'bg-primary-600 text-white shadow-sm'
                          : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' }}">
                {{ $tab['label'] }}
                <span class="text-xs px-1.5 py-0.5 rounded-full
                      {{ $filter === $tab['key'] ? 'bg-white/20' : 'bg-gray-100 text-gray-500' }}">
                    {{ $tab['count'] }}
                </span>
            </a>
        @endforeach
    </div>

    {{-- ═══ BOOK CARDS ════════════════════════════════════════════════════════ --}}
    @if($bookOrders->isNotEmpty())
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($bookOrders as $bookOrder)
                @php $book = $bookOrder->book; @endphp
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-md transition-shadow group">
                    {{-- Cover --}}
                    <a href="{{ route('dashboard.my-book-detail', $bookOrder->id) }}" class="block relative">
                        <img src="{{ $book->cover_url }}" alt="{{ $book->title }}"
                             class="w-full h-52 object-cover group-hover:scale-105 transition-transform duration-300">
                        <div class="absolute top-2 left-2">
                            <span class="px-2 py-1 rounded-lg text-xs font-bold
                                {{ $bookOrder->purchase_type === 'digital' ? 'bg-indigo-500/90 text-white' : 'bg-amber-500/90 text-white' }}">
                                {{ $bookOrder->purchase_type === 'digital' ? '📱 E-Book' : '📦 Fisik' }}
                            </span>
                        </div>
                    </a>

                    <div class="p-4">
                        <a href="{{ route('dashboard.my-book-detail', $bookOrder->id) }}">
                            <h3 class="text-sm font-bold text-gray-900 mb-1 line-clamp-2 group-hover:text-primary-600 transition-colors">
                                {{ $book->title }}
                            </h3>
                        </a>
                        <p class="text-xs text-gray-500 mb-3">{{ $book->author ?? '-' }}</p>

                        {{-- Book details --}}
                        <div class="space-y-1.5 mb-4">
                            @if($book->pages)
                                <div class="flex items-center gap-2 text-xs text-gray-600">
                                    <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    {{ $book->pages }} halaman
                                </div>
                            @endif
                            <div class="flex items-center gap-2 text-xs text-gray-600">
                                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                                </svg>
                                Qty: {{ $bookOrder->quantity }} · {{ rupiah($bookOrder->price) }}
                            </div>
                        </div>

                        {{-- Shipping status (physical) --}}
                        @if($bookOrder->purchase_type === 'physical')
                            <div class="bg-gray-50 rounded-lg p-3 border border-gray-100 mb-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-[10px] text-gray-400 uppercase font-semibold tracking-wider">Status Pengiriman</p>
                                        <p class="text-xs font-semibold text-gray-700 mt-0.5">{{ $bookOrder->status_label }}</p>
                                    </div>
                                    @if($bookOrder->tracking_number)
                                        <span class="text-[10px] font-mono text-gray-500">{{ $bookOrder->tracking_number }}</span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- Download button (digital) --}}
                        @if($bookOrder->purchase_type === 'digital' && $book->file_path)
                            <a href="{{ route('books.download', $book->slug) }}"
                               class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-primary-600 text-white text-xs font-semibold hover:bg-primary-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Download E-Book
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $bookOrders->withQueryString()->links() }}
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                @include('layouts.partials.icon', ['name' => 'library', 'class' => 'w-8 h-8 text-gray-400'])
            </div>
            <h3 class="text-base font-bold text-gray-900 mb-1">Belum Ada Buku</h3>
            <p class="text-sm text-gray-500 mb-4">Kamu belum membeli buku apapun.</p>
            <a href="{{ route('books.index') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary-600 text-white text-sm font-semibold hover:bg-primary-700 transition-colors">
                Jelajahi Buku
            </a>
        </div>
    @endif

</div>
@endsection
