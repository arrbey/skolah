@extends('layouts.app')

@section('title', 'Pembayaran Bootcamp — ' . $bootcamp->title)

@section('content')
<div class="min-h-screen bg-gray-950 pt-28 pb-16">
    <div class="max-w-2xl mx-auto px-4 sm:px-6">

        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-purple-500/10 ring-1 ring-purple-500/30 mb-4">
                <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white">Konfirmasi Pembayaran</h1>
            <p class="text-gray-400 mt-1 text-sm">Selesaikan pembayaran untuk mendapatkan akses bootcamp</p>
        </div>

        {{-- Order Summary Card --}}
        <div class="bg-gray-900 border border-white/10 rounded-2xl overflow-hidden mb-6">
            {{-- Thumbnail strip --}}
            @if($bootcamp->thumbnail)
            <div class="relative h-28 overflow-hidden">
                <x-picture
                    :src="storageUrl($bootcamp->thumbnail)"
                    :alt="$bootcamp->title"
                    class="w-full h-full object-cover opacity-60" />
                <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/60 to-transparent"></div>
                <div class="absolute bottom-3 left-4">
                    <span class="text-xs font-medium px-2 py-1 rounded-full
                        {{ $bootcamp->type === 'online' ? 'bg-sky-500/20 text-sky-300 ring-1 ring-sky-500/30' : 'bg-amber-500/20 text-amber-300 ring-1 ring-amber-500/30' }}">
                        {{ $bootcamp->type === 'online' ? '🌐 Online' : '📍 Offline' }}
                    </span>
                </div>
            </div>
            @endif

            <div class="p-6">
                <h2 class="text-lg font-semibold text-white mb-1">{{ $bootcamp->title }}</h2>
                <p class="text-gray-400 text-sm mb-4">
                    Bersama {{ $bootcamp->instructor->name ?? 'Instruktur' }}
                </p>

                {{-- Details --}}
                <div class="space-y-2 mb-5">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Tanggal</span>
                        <span class="text-white">{{ tanggal_indo($bootcamp->start_date) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Platform</span>
                        <span class="text-white">{{ $bootcamp->platform ?? 'TBA' }}</span>
                    </div>
                    @if($bootcamp->type === 'offline' && $bootcamp->location)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Lokasi</span>
                        <span class="text-white text-right max-w-xs">{{ $bootcamp->location }}</span>
                    </div>
                    @endif
                </div>

                <div class="border-t border-white/10 pt-4 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Subtotal</span>
                        <span class="text-white">{{ rupiah($order->subtotal) }}</span>
                    </div>
                    @if($order->discount_amount > 0)
                    <div class="flex justify-between text-sm">
                        <span class="text-green-400">Diskon</span>
                        <span class="text-green-400">-{{ rupiah($order->discount_amount) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-base font-semibold pt-2 border-t border-white/10">
                        <span class="text-white">Total</span>
                        <span class="text-purple-400 text-lg">{{ rupiah($order->total) }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Order number --}}
        <p class="text-center text-xs text-gray-500 mb-6">
            No. Order: <span class="font-mono text-gray-400">{{ $order->order_number }}</span>
        </p>

        {{-- Pay Button --}}
        <button id="pay-btn"
            class="w-full py-4 px-6 rounded-xl font-semibold text-white text-base
                   bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500
                   transition-all duration-200 shadow-lg shadow-purple-500/20 flex items-center justify-center gap-2"
            onclick="triggerPayment()">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
            Bayar Sekarang
        </button>

        <p class="text-center text-xs text-gray-500 mt-4">
            Pembayaran aman diproses oleh
            <span class="text-gray-400 font-medium">Midtrans</span>
        </p>

        <div class="flex items-center justify-center gap-4 mt-4 opacity-40">
            <img src="https://upload.wikimedia.org/wikipedia/commons/b/ba/Stripe_Logo%2C_revised_2016.svg" alt="" class="h-4 grayscale">
            <span class="text-gray-600">|</span>
            <span class="text-xs text-gray-500">GoPay · OVO · DANA · Transfer Bank · Kartu Kredit</span>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Midtrans Snap.js --}}
<script src="{{ $snapUrl }}" data-client-key="{{ $clientKey }}"></script>
<script nonce="{{ $cspNonce ?? '' }}">
    const SNAP_TOKEN      = @json($snapToken);
    const SUCCESS_URL     = @json(route('bootcamp.checkout.success'));
    const FAILED_URL      = @json(route('bootcamp.checkout.failed'));
    const BOOTCAMP_URL    = @json(route('bootcamps.show', $bootcamp->slug));

    function triggerPayment() {
        window.snap.pay(SNAP_TOKEN, {
            onSuccess: function(result) {
                window.location.href = SUCCESS_URL + '?order_id=' + result.order_id;
            },
            onPending: function(result) {
                // Pending = arahkan ke dashboard orders
                window.location.href = '/dashboard/orders?pending=1';
            },
            onError: function(result) {
                window.location.href = FAILED_URL + '?order_id=' + result.order_id;
            },
            onClose: function() {
                // User tutup popup tanpa bayar — tetap di halaman ini
            }
        });
    }

    // Auto-trigger popup saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(triggerPayment, 600);
    });
</script>
@endpush
