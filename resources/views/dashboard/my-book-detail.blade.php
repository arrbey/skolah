@extends('layouts.dashboard')

@section('title', 'Detail Buku — ' . $bookOrder->book->title)

@section('page-header')
    <div class="flex items-center gap-3">
        <a href="{{ route('dashboard.my-books') }}"
           class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-lg font-bold text-gray-900">Detail Buku</h1>
            <p class="text-xs text-gray-500 mt-0.5">{{ $bookOrder->book->title }}</p>
        </div>
    </div>
@endsection

@section('content')
@php
    $book  = $bookOrder->book;
    $order = $bookOrder->order;
    $isDigital = $bookOrder->purchase_type === 'digital';
@endphp

<div class="max-w-4xl mx-auto space-y-6">

    {{-- ═══ STATUS BANNER ══════════════════════════════════════════════════ --}}
    @if($isDigital)
        <div class="rounded-xl border border-indigo-200 bg-indigo-50 p-4 flex items-center gap-3">
            <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 shrink-0"></span>
            <span class="text-sm font-semibold text-indigo-800">📱 E-Book — Siap diunduh kapan saja</span>
        </div>
    @else
        @php
            $statusColor = match($bookOrder->shipping_status) {
                'pending'    => ['bg' => 'bg-yellow-50',  'border' => 'border-yellow-200',  'text' => 'text-yellow-800',  'dot' => 'bg-yellow-500'],
                'processing' => ['bg' => 'bg-blue-50',   'border' => 'border-blue-200',    'text' => 'text-blue-800',    'dot' => 'bg-blue-500 animate-pulse'],
                'shipped'    => ['bg' => 'bg-indigo-50', 'border' => 'border-indigo-200',  'text' => 'text-indigo-800',  'dot' => 'bg-indigo-500 animate-pulse'],
                'delivered'  => ['bg' => 'bg-green-50',  'border' => 'border-green-200',   'text' => 'text-green-800',   'dot' => 'bg-green-500'],
                'cancelled'  => ['bg' => 'bg-red-50',    'border' => 'border-red-200',     'text' => 'text-red-800',     'dot' => 'bg-red-500'],
                default      => ['bg' => 'bg-gray-50',   'border' => 'border-gray-200',    'text' => 'text-gray-700',    'dot' => 'bg-gray-400'],
            };
        @endphp
        <div class="rounded-xl border {{ $statusColor['border'] }} {{ $statusColor['bg'] }} p-4 flex items-center gap-3">
            <span class="w-2.5 h-2.5 rounded-full {{ $statusColor['dot'] }} shrink-0"></span>
            <div>
                <span class="text-sm font-semibold {{ $statusColor['text'] }}">
                    {{ $bookOrder->status_label }}
                </span>
                @if($bookOrder->tracking_number)
                    <p class="text-xs text-gray-500 mt-0.5">
                        No. Resi: <span class="font-mono font-semibold text-gray-700">{{ $bookOrder->tracking_number }}</span>
                    </p>
                @endif
            </div>
        </div>
    @endif

    {{-- ═══ MAIN CARD ══════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="p-6 flex flex-col sm:flex-row gap-6">
            {{-- Cover Buku --}}
            <div class="shrink-0">
                <img src="{{ $book->cover_url }}" alt="{{ $book->title }}"
                     class="w-36 h-48 object-cover rounded-lg shadow-md border border-gray-200">
                <div class="mt-2 text-center">
                    <span class="px-2 py-1 rounded-lg text-xs font-bold
                        {{ $isDigital ? 'bg-indigo-100 text-indigo-700' : 'bg-amber-100 text-amber-700' }}">
                        {{ $isDigital ? '📱 E-Book' : '📦 Fisik' }}
                    </span>
                </div>
            </div>

            {{-- Info Buku --}}
            <div class="flex-1 min-w-0">
                <h2 class="text-xl font-bold text-gray-900 mb-1">{{ $book->title }}</h2>

                <div class="flex flex-wrap gap-x-4 gap-y-1 text-sm text-gray-500 mb-4">
                    @if($book->author)
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            {{ $book->author }}
                        </span>
                    @endif
                    @if($book->publisher)
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                            </svg>
                            {{ $book->publisher }}
                        </span>
                    @endif
                    @if($book->pages)
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            {{ $book->pages }} halaman
                        </span>
                    @endif
                    @if($book->isbn)
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.24M16.75 9l.07.07M16.75 15l.07-.07M8.25 15l.07.07M8.25 9l.07-.07"/>
                            </svg>
                            ISBN: {{ $book->isbn }}
                        </span>
                    @endif
                </div>

                @if($book->description)
                    <p class="text-sm text-gray-600 leading-relaxed line-clamp-4">
                        {{ strip_tags($book->description) }}
                    </p>
                @endif

                {{-- Download button --}}
                @if($isDigital && $book->file_path)
                    <div class="mt-4">
                        <a href="{{ route('books.download', $book->slug) }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary-600 text-white text-sm font-semibold hover:bg-primary-700 transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Download E-Book
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ═══ DETAIL PEMBELIAN ════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-sm font-bold text-gray-900">📋 Detail Pembelian</h3>
        </div>
        <div class="p-6">
            <div class="grid sm:grid-cols-2 gap-4">
                <div class="space-y-3">
                    <div>
                        <p class="text-[11px] uppercase tracking-wider font-semibold text-gray-400">Nomor Order</p>
                        <p class="text-sm font-mono font-semibold text-gray-800 mt-0.5">{{ $order->order_number }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] uppercase tracking-wider font-semibold text-gray-400">Tanggal Pembelian</p>
                        <p class="text-sm text-gray-800 mt-0.5">
                            {{ $order->paid_at ? \Carbon\Carbon::parse($order->paid_at)->translatedFormat('d F Y, H:i') : '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-[11px] uppercase tracking-wider font-semibold text-gray-400">Tipe Produk</p>
                        <p class="text-sm text-gray-800 mt-0.5">{{ $isDigital ? 'E-Book (Digital)' : 'Buku Fisik' }}</p>
                    </div>
                </div>
                <div class="space-y-3">
                    <div>
                        <p class="text-[11px] uppercase tracking-wider font-semibold text-gray-400">Jumlah</p>
                        <p class="text-sm text-gray-800 mt-0.5">{{ $bookOrder->quantity }} eksemplar</p>
                    </div>
                    <div>
                        <p class="text-[11px] uppercase tracking-wider font-semibold text-gray-400">Harga Satuan</p>
                        <p class="text-sm text-gray-800 mt-0.5">{{ rupiah($bookOrder->price) }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] uppercase tracking-wider font-semibold text-gray-400">Total Harga</p>
                        <p class="text-base font-bold text-primary-600 mt-0.5">{{ rupiah($bookOrder->total_price) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ INFORMASI PENGIRIMAN (hanya buku fisik) ═════════════════════════ --}}
    @if(!$isDigital)
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-900">🚚 Informasi Pengiriman</h3>
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                    {{ match($bookOrder->shipping_status) {
                        'pending'    => 'bg-yellow-100 text-yellow-700',
                        'processing' => 'bg-blue-100 text-blue-700',
                        'shipped'    => 'bg-indigo-100 text-indigo-700',
                        'delivered'  => 'bg-green-100 text-green-700',
                        'cancelled'  => 'bg-red-100 text-red-700',
                        default      => 'bg-gray-100 text-gray-600',
                    } }}">
                    {{ $bookOrder->status_label }}
                </span>
            </div>
            <div class="p-6 space-y-4">

                {{-- Kurir & Resi --}}
                <div class="grid sm:grid-cols-2 gap-4">
                    @if($bookOrder->courier)
                    <div>
                        <p class="text-[11px] uppercase tracking-wider font-semibold text-gray-400">Kurir</p>
                        <span class="inline-flex items-center gap-1.5 mt-1 px-3 py-1 rounded-lg text-sm font-bold
                            {{ $bookOrder->courier === 'jne' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                            {{ $bookOrder->courier_label }}
                        </span>
                    </div>
                    @endif
                    @if($bookOrder->tracking_number)
                    <div>
                        <p class="text-[11px] uppercase tracking-wider font-semibold text-gray-400">Nomor Resi</p>
                        <p class="text-sm font-mono font-bold text-gray-800 mt-0.5">{{ $bookOrder->tracking_number }}</p>
                        <a href="{{ $bookOrder->courier === 'jne'
                                ? 'https://www.jne.co.id/id/tracking/trace?awb=' . $bookOrder->tracking_number
                                : 'https://jet.co.id/track?awb=' . $bookOrder->tracking_number }}"
                           target="_blank" class="text-xs text-primary-600 hover:underline mt-0.5 inline-block">
                            Lacak Paket →
                        </a>
                    </div>
                    @endif
                </div>

                @if($bookOrder->shipping_address)
                    @php $addr = $bookOrder->shipping_address; @endphp
                    <div>
                        <p class="text-[11px] uppercase tracking-wider font-semibold text-gray-400 mb-1.5">Alamat Pengiriman</p>
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-100 text-sm text-gray-700 space-y-0.5">
                            @if(!empty($addr['name']))
                                <p class="font-semibold text-gray-900">{{ $addr['name'] }}</p>
                            @endif
                            @if(!empty($addr['phone']))
                                <p class="text-gray-500">{{ $addr['phone'] }}</p>
                            @endif
                            @if(!empty($addr['address']))
                                <p>{{ $addr['address'] }}</p>
                            @endif
                            @if(!empty($addr['city']) || !empty($addr['province']))
                                <p>{{ implode(', ', array_filter([$addr['city'] ?? null, $addr['province'] ?? null])) }}</p>
                            @endif
                            @if(!empty($addr['postal_code']))
                                <p>{{ $addr['postal_code'] }}</p>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Foto Bukti Terima --}}
                @if($bookOrder->delivery_photo)
                <div>
                    <p class="text-[11px] uppercase tracking-wider font-semibold text-gray-400 mb-2">📸 Foto Bukti Terima</p>
                    <img src="{{ $bookOrder->delivery_photo_url }}" alt="Bukti terima"
                         class="max-w-xs rounded-xl border border-gray-200 shadow-sm">
                    @if($bookOrder->delivered_at)
                        <p class="text-xs text-gray-400 mt-1.5">
                            Diterima: {{ $bookOrder->delivered_at->translatedFormat('d F Y, H:i') }}
                        </p>
                    @endif
                </div>
                @endif

                {{-- Timeline Progress --}}
                <div>
                    <p class="text-[11px] uppercase tracking-wider font-semibold text-gray-400 mb-3">Progress Pengiriman</p>
                    @php
                        $steps = [
                            ['key' => 'pending',    'icon' => '🕐', 'label' => 'Menunggu Pengiriman'],
                            ['key' => 'processing', 'icon' => '📦', 'label' => 'Diproses'],
                            ['key' => 'shipped',    'icon' => '🚚', 'label' => 'Dalam Pengiriman'],
                            ['key' => 'delivered',  'icon' => '✅', 'label' => 'Terkirim'],
                        ];
                        $statusOrder = ['pending' => 0, 'processing' => 1, 'shipped' => 2, 'delivered' => 3];
                        $currentIdx  = $statusOrder[$bookOrder->shipping_status] ?? 0;
                    @endphp
                    <div class="flex items-start gap-0">
                        @foreach($steps as $i => $step)
                            @php $stepIdx = $statusOrder[$step['key']] ?? 0; @endphp
                            <div class="flex-1 flex flex-col items-center relative">
                                @if($i > 0)
                                    <div class="absolute top-4 right-1/2 w-full h-0.5 -translate-y-1/2
                                        {{ $stepIdx <= $currentIdx ? 'bg-primary-500' : 'bg-gray-200' }}"></div>
                                @endif
                                <div class="relative z-10 w-8 h-8 rounded-full flex items-center justify-center text-base
                                    {{ $stepIdx < $currentIdx  ? 'bg-primary-600 text-white shadow-md' :
                                       ($stepIdx === $currentIdx ? 'bg-primary-600 text-white shadow-md ring-4 ring-primary-100' : 'bg-gray-100 text-gray-400') }}">
                                    {{ $step['icon'] }}
                                </div>
                                <p class="text-[10px] text-center mt-1.5 font-medium
                                    {{ $stepIdx <= $currentIdx ? 'text-primary-700' : 'text-gray-400' }}">
                                    {{ $step['label'] }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Riwayat Update --}}
                @if($bookOrder->histories->isNotEmpty())
                <div>
                    <p class="text-[11px] uppercase tracking-wider font-semibold text-gray-400 mb-3">Riwayat Update</p>
                    <div class="relative pl-5 space-y-3">
                        @foreach($bookOrder->histories as $i => $history)
                        @php
                            $isLast = $i === $bookOrder->histories->count() - 1;
                            $dotColor = match($history->status) {
                                'pending'    => 'bg-yellow-400',
                                'processing' => 'bg-blue-500',
                                'shipped'    => 'bg-indigo-500',
                                'delivered'  => 'bg-green-500',
                                'cancelled'  => 'bg-red-500',
                                default      => 'bg-gray-400',
                            };
                        @endphp
                        <div class="relative">
                            @if(!$isLast)
                            <div class="absolute left-[-13px] top-3.5 bottom-[-12px] w-0.5 bg-gray-200"></div>
                            @endif
                            <div class="absolute left-[-17px] top-1 w-2.5 h-2.5 rounded-full {{ $dotColor }} ring-2 ring-white"></div>
                            <div class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                                <div class="flex items-start justify-between gap-2 flex-wrap">
                                    <p class="text-xs font-semibold text-gray-900">{{ $history->status_label }}</p>
                                    <p class="text-[11px] text-gray-400">{{ $history->created_at->translatedFormat('d M Y, H:i') }}</p>
                                </div>
                                @if($history->courier || $history->tracking_number || $history->note)
                                <div class="mt-1 space-y-0.5 text-[11px] text-gray-500">
                                    @if($history->courier)        <p>Kurir: <span class="font-semibold">{{ $history->courier_label }}</span></p>@endif
                                    @if($history->tracking_number)<p>Resi: <span class="font-mono font-semibold text-gray-700">{{ $history->tracking_number }}</span></p>@endif
                                    @if($history->note)           <p class="italic">"{{ $history->note }}"</p>@endif
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

            </div>
        </div>
    @endif

    {{-- ═══ TOMBOL AKSI ════════════════════════════════════════════════════ --}}
    <div class="flex flex-wrap items-center gap-3 pb-4">
        <a href="{{ route('dashboard.my-books') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-gray-200 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke Buku Saya
        </a>

        <a href="{{ route('books.show', $book->slug) }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-primary-200 bg-primary-50 text-sm font-semibold text-primary-700 hover:bg-primary-100 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            Lihat Halaman Buku
        </a>

        @if($isDigital && $book->file_path)
            <a href="{{ route('books.download', $book->slug) }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary-600 text-white text-sm font-semibold hover:bg-primary-700 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Download E-Book
            </a>
        @endif
    </div>

</div>
@endsection
