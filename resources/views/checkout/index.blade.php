@extends('layouts.app')

@section('title', 'Checkout' . ' — ' . \App\Models\Setting::get('site_name', '' . \App\Models\Setting::get('site_name', 'Skolah.com') . ''))

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-8 lg:py-12">

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6">
            <a href="{{ route('home') }}" class="hover:text-primary-600 transition-colors">Beranda</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('cart') }}" class="hover:text-primary-600 transition-colors">Keranjang</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-900 font-medium">Checkout</span>
        </nav>

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Checkout</h1>
            <p class="text-gray-500 mt-1">Konfirmasi pesanan dan lakukan pembayaran</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8" x-data="checkoutApp()">

            {{-- ──── LEFT: Item List ──── --}}
            <div class="lg:col-span-2 space-y-4">

                {{-- Order Items --}}
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h2 class="text-lg font-semibold text-gray-900">
                            Ringkasan Pesanan
                            <span class="text-sm font-normal text-gray-500">({{ $cartItems->count() }} item)</span>
                        </h2>
                    </div>

                    <div class="divide-y divide-gray-100">
                        @foreach($cartItems as $item)
                        <div class="flex items-start gap-4 p-6">
                            {{-- Thumbnail --}}
                            <div class="w-20 h-14 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0">
                                @if($item->cartable)
                                    @php
                                        $thumb = $item->cartable->thumbnail_url
                                            ?? $item->cartable->cover_url
                                            ?? null;
                                    @endphp
                                    @if($thumb)
                                        <img src="{{ $thumb }}" alt="{{ $item->cartable->title ?? $item->cartable->name ?? '' }}"
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                @endif
                            </div>

                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <h3 class="font-medium text-gray-900 text-sm leading-tight">
                                    {{ $item->cartable->title ?? $item->cartable->name ?? 'Item' }}
                                </h3>
                                <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ match($item->item_type) {
                                        'course' => 'bg-blue-100 text-blue-700',
                                        'bootcamp' => 'bg-purple-100 text-purple-700',
                                        'book' => 'bg-amber-100 text-amber-700',
                                        'membership' => 'bg-emerald-100 text-emerald-700',
                                        default => 'bg-gray-100 text-gray-700',
                                    } }}">
                                    {{ $item->item_type_label }}
                                </span>
                                @if($item->quantity > 1)
                                    <span class="text-xs text-gray-500 ml-2">× {{ $item->quantity }}</span>
                                @endif
                            </div>

                            {{-- Price --}}
                            <div class="text-right flex-shrink-0">
                                <span class="text-sm font-semibold text-gray-900">{{ $item->subtotal_formatted }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Info Keamanan --}}
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-blue-800">Pembayaran Aman</p>
                        <p class="text-xs text-blue-600 mt-0.5">
                            Pembayaran diproses melalui Midtrans dengan enkripsi SSL 256-bit.
                            Data kartu kamu tidak disimpan di server kami.
                        </p>
                    </div>
                </div>
            </div>

            {{-- ──── RIGHT: Payment Summary ──── --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl border border-gray-200 p-6 lg:sticky lg:top-24">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Rincian Pembayaran</h2>

                    {{-- Subtotal --}}
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Subtotal ({{ $cartItems->count() }} item)</span>
                            <span class="text-gray-900 font-medium">{{ rupiah($subtotal) }}</span>
                        </div>

                        @if($discount > 0 && $promoCode)
                        <div class="flex justify-between text-green-600">
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                Diskon ({{ $promoCode->code }})
                            </span>
                            <span class="font-medium">-{{ rupiah($discount) }}</span>
                        </div>
                        @endif
                    </div>

                    {{-- Divider --}}
                    <div class="border-t border-gray-200 my-4"></div>

                    {{-- Total --}}
                    <div class="flex justify-between items-center mb-6">
                        <span class="text-base font-semibold text-gray-900">Total</span>
                        <span class="text-xl font-bold text-primary-600">{{ rupiah($total) }}</span>
                    </div>

                    {{-- Pay Button --}}
                    <button
                        @click="processPayment()"
                        :disabled="isProcessing"
                        class="w-full py-3.5 rounded-xl text-white text-sm font-semibold transition-all duration-200
                               bg-gradient-to-r from-primary-600 to-secondary-600 hover:from-primary-700 hover:to-secondary-700
                               disabled:opacity-60 disabled:cursor-not-allowed
                               flex items-center justify-center gap-2 shadow-lg shadow-primary-600/20"
                    >
                        <template x-if="!isProcessing">
                            <span class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                Bayar Sekarang
                            </span>
                        </template>
                        <template x-if="isProcessing">
                            <span class="flex items-center gap-2">
                                <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Memproses...
                            </span>
                        </template>
                    </button>

                    {{-- Error Message --}}
                    <div x-show="errorMessage" x-cloak
                         class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700"
                         x-text="errorMessage">
                    </div>

                    {{-- Back to cart --}}
                    <a href="{{ route('cart') }}"
                       class="block text-center text-sm text-gray-500 hover:text-primary-600 mt-4 transition-colors">
                        ← Kembali ke Keranjang
                    </a>

                    {{-- Metode pembayaran --}}
                    <div class="mt-6 pt-4 border-t border-gray-100">
                        <p class="text-xs text-gray-400 text-center mb-3">Metode pembayaran yang didukung</p>
                        <div class="flex items-center justify-center gap-3 flex-wrap">
                            <span class="px-2 py-1 bg-gray-50 rounded text-xs text-gray-500 font-medium">VISA</span>
                            <span class="px-2 py-1 bg-gray-50 rounded text-xs text-gray-500 font-medium">Mastercard</span>
                            <span class="px-2 py-1 bg-gray-50 rounded text-xs text-gray-500 font-medium">BCA</span>
                            <span class="px-2 py-1 bg-gray-50 rounded text-xs text-gray-500 font-medium">BNI</span>
                            <span class="px-2 py-1 bg-gray-50 rounded text-xs text-gray-500 font-medium">Mandiri</span>
                            <span class="px-2 py-1 bg-gray-50 rounded text-xs text-gray-500 font-medium">GoPay</span>
                            <span class="px-2 py-1 bg-gray-50 rounded text-xs text-gray-500 font-medium">OVO</span>
                            <span class="px-2 py-1 bg-gray-50 rounded text-xs text-gray-500 font-medium">QRIS</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
function checkoutApp() {
    return {
        isProcessing: false,
        errorMessage: '',

        async processPayment() {
            if (this.isProcessing) return;
            this.isProcessing = true;
            this.errorMessage = '';

            try {
                const response = await fetch('{{ route("checkout.process") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    this.errorMessage = data.message || 'Terjadi kesalahan. Silakan coba lagi.';
                    this.isProcessing = false;
                    return;
                }

                // ── REDIRECT MODE: Pindah ke halaman Midtrans (Bypass CSP) ──
                window.location.href = data.redirect_url;

            } catch (err) {
                console.error('Checkout error:', err);
                this.errorMessage = 'Terjadi kesalahan jaringan. Silakan coba lagi.';
                this.isProcessing = false;
            }
        },
    };
}
</script>
@endpush
