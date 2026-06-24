@extends('layouts.app')

@section('title', 'Keranjang Belanja' . ' — ' . \App\Models\Setting::get('site_name', '' . \App\Models\Setting::get('site_name', 'Skolah.com') . ''))

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-8 lg:py-12">

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Keranjang Belanja</h1>
            <p class="text-gray-500 mt-1">{{ $cartItems->count() }} item di keranjang Anda</p>
        </div>

        @if($cartItems->isEmpty())
            {{-- Empty Cart --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center">
                <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h2 class="text-lg font-semibold text-gray-900 mb-1">Keranjang Kosong</h2>
                <p class="text-sm text-gray-500 mb-6">Belum ada item di keranjang. Yuk, mulai belajar!</p>
                <div class="flex flex-wrap items-center justify-center gap-3">
                    <a href="{{ route('courses.index') }}" class="px-6 py-2.5 rounded-xl bg-primary-600 text-white text-sm font-medium hover:bg-primary-700 transition-colors">Jelajahi Kursus</a>
                    <a href="{{ route('bootcamps.index') }}" class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition-colors">Lihat Bootcamp</a>
                    <a href="{{ route('books.index') }}" class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition-colors">Cari Buku</a>
                </div>
            </div>
        @else
            <div class="grid lg:grid-cols-3 gap-8">
                {{-- Cart Items --}}
                <div class="lg:col-span-2 space-y-4">
                    @foreach($cartItems as $item)
                        <div class="bg-white rounded-2xl border border-gray-200 p-4 sm:p-5 hover:shadow-sm transition-shadow">
                            <div class="flex gap-4">
                                {{-- Thumbnail --}}
                                <div class="shrink-0">
                                    @php
                                        $thumb = match($item->item_type) {
                                            'course'     => $item->cartable?->thumbnail_url,
                                            'bootcamp'   => $item->cartable?->thumbnail_url,
                                            'book'       => $item->cartable?->cover_url,
                                            'membership' => null,
                                            default      => null,
                                        };
                                    @endphp
                                    @if($thumb)
                                        <img src="{{ $thumb }}" alt="" class="w-20 h-20 sm:w-24 sm:h-16 rounded-xl object-cover">
                                    @else
                                        <div class="w-20 h-20 sm:w-24 sm:h-16 rounded-xl bg-gradient-to-br from-primary-500 to-purple-600 flex items-center justify-center">
                                            <svg class="w-8 h-8 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                {{-- Info --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="inline-flex px-2 py-0.5 rounded-md text-[10px] font-semibold uppercase tracking-wide
                                                    {{ match($item->item_type) {
                                                        'course'     => 'bg-blue-100 text-blue-700',
                                                        'bootcamp'   => 'bg-purple-100 text-purple-700',
                                                        'book'       => 'bg-amber-100 text-amber-700',
                                                        'membership' => 'bg-emerald-100 text-emerald-700',
                                                        default      => 'bg-gray-100 text-gray-600',
                                                    } }}
                                                ">{{ $item->item_type_label }}</span>
                                            </div>
                                            <h3 class="font-semibold text-gray-900 truncate">
                                                {{ $item->cartable?->title ?? $item->cartable?->name ?? 'Item tidak tersedia' }}
                                            </h3>
                                            @if($item->item_type === 'course' && $item->cartable?->instructor)
                                                <p class="text-xs text-gray-500 mt-0.5">oleh {{ $item->cartable->instructor->name }}</p>
                                            @elseif($item->item_type === 'book' && $item->cartable?->author)
                                                <p class="text-xs text-gray-500 mt-0.5">oleh {{ $item->cartable->author }}</p>
                                            @elseif($item->item_type === 'bootcamp' && $item->cartable?->start_date)
                                                <p class="text-xs text-gray-500 mt-0.5">{{ $item->cartable->start_date->translatedFormat('d M Y') }}</p>
                                            @endif
                                            @if($item->course_variant_id && $item->courseVariant)
                                                <span class="inline-flex items-center gap-1 text-xs bg-purple-50 text-purple-700 px-2 py-0.5 rounded-full mt-1 font-medium">
                                                    {{ $item->courseVariant->display_label }}
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Delete Button --}}
                                        <form action="{{ route('cart.remove', $item) }}" method="POST" class="shrink-0">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors" title="Hapus dari keranjang">
                                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>

                                    {{-- Bottom row: quantity + price --}}
                                    <div class="flex items-end justify-between mt-3">
                                        {{-- Quantity (hanya buku) --}}
                                        @if($item->item_type === 'book')
                                            <form action="{{ route('cart.update', $item) }}" method="POST" class="flex items-center gap-1" x-data="{ qty: {{ $item->quantity }} }">
                                                @csrf @method('PATCH')
                                                <button type="button" @click="qty = Math.max(1, qty - 1); $refs.qtyInput.value = qty; $el.closest('form').submit()" class="w-8 h-8 rounded-lg border border-gray-300 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition-colors text-sm font-bold">−</button>
                                                <input type="number" name="quantity" x-ref="qtyInput" :value="qty" min="1" max="99" readonly
                                                       class="w-12 h-8 text-center text-sm font-medium border border-gray-300 rounded-lg bg-gray-50">
                                                <button type="button" @click="qty = Math.min(99, qty + 1); $refs.qtyInput.value = qty; $el.closest('form').submit()" class="w-8 h-8 rounded-lg border border-gray-300 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition-colors text-sm font-bold">+</button>
                                            </form>
                                        @else
                                            <span class="text-xs text-gray-400">Qty: 1</span>
                                        @endif

                                        {{-- Price --}}
                                        <div class="text-right">
                                            @if($item->quantity > 1)
                                                <p class="text-xs text-gray-400">{{ rupiah($item->price) }} × {{ $item->quantity }}</p>
                                            @endif
                                            <p class="text-base font-bold text-gray-900">{{ $item->subtotal_formatted }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Order Summary Sidebar --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl border border-gray-200 p-6 sticky top-24"
                         x-data="{ discount: {{ $discount }}, subtotal: {{ $subtotal }} }"
                         @promo-applied.window="discount = $event.detail.discount">

                        <h2 class="text-lg font-bold text-gray-900 mb-5">Ringkasan Pesanan</h2>

                        {{-- Promo Code (Livewire) --}}
                        <div class="mb-5 pb-5 border-b border-gray-100">
                            @livewire('apply-promo-code')
                        </div>

                        {{-- Summary --}}
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal ({{ $cartItems->count() }} item)</span>
                                <span>{{ rupiah($subtotal) }}</span>
                            </div>

                            <div class="flex justify-between" x-show="discount > 0" x-cloak>
                                <span class="text-green-600">Diskon promo</span>
                                <span class="text-green-600 font-medium" x-text="'-Rp ' + new Intl.NumberFormat('id-ID').format(discount)">
                                    @if($discount > 0) -{{ rupiah($discount) }} @endif
                                </span>
                            </div>

                            <div class="pt-3 border-t border-gray-100 flex justify-between">
                                <span class="text-base font-bold text-gray-900">Total</span>
                                <span class="text-base font-bold text-gray-900" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(Math.max(0, subtotal - discount))">
                                    {{ rupiah($total) }}
                                </span>
                            </div>
                        </div>

                        {{-- Checkout Button --}}
                        <a href="{{ route('checkout') }}"
                           class="mt-6 w-full flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-primary-600 text-white font-semibold text-sm hover:bg-primary-700 transition-colors shadow-lg shadow-primary-600/25">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Lanjut ke Checkout
                        </a>

                        {{-- Trust badges --}}
                        <div class="mt-5 flex items-center justify-center gap-4 text-xs text-gray-400">
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                Pembayaran Aman
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                Garansi Akses
                            </span>
                        </div>

                        {{-- Continue Shopping --}}
                        <div class="mt-4 text-center">
                            <a href="{{ route('courses.index') }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                                ← Lanjut Belanja
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
