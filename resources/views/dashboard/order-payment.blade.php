@extends('layouts.dashboard')

@section('title', 'Bayar Order ' . $order->order_number)

@section('page-header')
    <div class="flex items-center gap-3">
        <a href="{{ route('dashboard.orders') }}"
           class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors">
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-lg font-bold text-gray-900">Selesaikan Pembayaran</h1>
            <p class="text-xs text-gray-500">Order {{ $order->order_number }}</p>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Order Summary Card --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-900">Ringkasan Pesanan</h2>
            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-50 text-yellow-700">
                Menunggu Pembayaran
            </span>
        </div>

        <div class="divide-y divide-gray-100">
            @foreach($order->items as $item)
                <div class="flex items-center gap-4 px-6 py-4">
                    {{-- Thumbnail --}}
                    <div class="w-14 h-10 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0">
                        @php
                            $thumb = null;
                            if ($item->itemable) {
                                $thumb = $item->itemable->thumbnail_url
                                    ?? $item->itemable->cover_url
                                    ?? null;
                            }
                        @endphp
                        @if($thumb)
                            <img src="{{ $thumb }}" alt="{{ $item->item_name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                    </div>

                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $item->item_name }}</p>
                        <span class="inline-block mt-0.5 text-[10px] px-1.5 py-0.5 rounded font-bold uppercase
                            @if($item->item_type === 'course') bg-blue-50 text-blue-600
                            @elseif($item->item_type === 'bootcamp') bg-purple-50 text-purple-600
                            @elseif($item->item_type === 'book') bg-amber-50 text-amber-600
                            @elseif($item->item_type === 'membership') bg-pink-50 text-pink-600
                            @else bg-gray-50 text-gray-600 @endif">
                            {{ $item->item_type_label }}
                        </span>
                    </div>

                    <span class="text-sm font-semibold text-gray-900 flex-shrink-0">{{ rupiah($item->price) }}</span>
                </div>
            @endforeach
        </div>

        {{-- Totals --}}
        <div class="px-6 py-4 bg-gray-50 space-y-2">
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Subtotal</span>
                <span class="text-gray-700">{{ rupiah($order->subtotal) }}</span>
            </div>
            @if($order->discount_amount > 0)
                <div class="flex justify-between text-sm text-green-600">
                    <span>Diskon{{ $order->promo_code ? ' (' . $order->promo_code . ')' : '' }}</span>
                    <span>-{{ rupiah($order->discount_amount) }}</span>
                </div>
            @endif
            <div class="flex justify-between text-base font-bold pt-2 border-t border-gray-200">
                <span class="text-gray-900">Total</span>
                <span class="text-primary-600">{{ rupiah($order->total) }}</span>
            </div>
        </div>
    </div>

    {{-- Pay Button --}}
    @if($order->payment_expires_at)
    <div x-data="countdownTimer()" x-init="start('{{ $order->payment_expires_at->toIso8601String() }}')"
         class="bg-white rounded-xl border border-gray-200 p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-sm font-medium text-gray-700">Batas Waktu Pembayaran</span>
            </div>
            <div x-show="!expired" class="flex items-center gap-1.5">
                <span class="inline-flex items-center justify-center min-w-[36px] px-2 py-1 rounded-lg bg-red-50 text-red-700 text-sm font-bold font-mono" x-text="hours"></span>
                <span class="text-red-400 font-bold">:</span>
                <span class="inline-flex items-center justify-center min-w-[36px] px-2 py-1 rounded-lg bg-red-50 text-red-700 text-sm font-bold font-mono" x-text="minutes"></span>
                <span class="text-red-400 font-bold">:</span>
                <span class="inline-flex items-center justify-center min-w-[36px] px-2 py-1 rounded-lg bg-red-50 text-red-700 text-sm font-bold font-mono" x-text="seconds"></span>
            </div>
            <div x-show="expired" class="text-sm font-bold text-red-600">
                ⏰ Waktu Habis
            </div>
        </div>
    </div>
    @endif

    <button id="pay-btn" onclick="triggerPayment()"
        class="w-full py-4 px-6 rounded-xl font-semibold text-white text-base
               bg-gradient-to-r from-primary-600 to-secondary-600 hover:from-primary-700 hover:to-secondary-700
               transition-all duration-200 shadow-lg shadow-primary-600/20 flex items-center justify-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
        </svg>
        Bayar Sekarang — {{ rupiah($order->total) }}
    </button>

    {{-- Info --}}
    <div class="flex items-start gap-3 bg-blue-50 border border-blue-200 rounded-xl p-4">
        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <p class="text-sm font-medium text-blue-800">Pembayaran aman melalui Midtrans</p>
            <p class="text-xs text-blue-600 mt-0.5">
                Mendukung Transfer Bank, QRIS, GoPay, OVO, DANA, Kartu Kredit/Debit, dan lainnya.
            </p>
        </div>
    </div>

    <p class="text-center text-xs text-gray-400">
        Pembayaran diproses oleh <span class="font-medium text-gray-500">Midtrans</span> dengan enkripsi SSL 256-bit.
    </p>

</div>
@endsection

@push('scripts')
{{-- Countdown Timer --}}
<script nonce="{{ $cspNonce ?? '' }}">
function countdownTimer() {
    return {
        hours: '00', minutes: '00', seconds: '00', expired: false,
        start(expiresAt) {
            const target = new Date(expiresAt).getTime();
            const tick = () => {
                const diff = target - Date.now();
                if (diff <= 0) {
                    this.expired = true;
                    this.hours = '00'; this.minutes = '00'; this.seconds = '00';
                    // Redirect setelah 3 detik
                    setTimeout(() => { window.location.href = ORDERS_URL; }, 3000);
                    return;
                }
                const h = Math.floor(diff / 3600000);
                const m = Math.floor((diff % 3600000) / 60000);
                const s = Math.floor((diff % 60000) / 1000);
                this.hours   = String(h).padStart(2, '0');
                this.minutes = String(m).padStart(2, '0');
                this.seconds = String(s).padStart(2, '0');
                requestAnimationFrame(() => setTimeout(tick, 1000));
            };
            tick();
        }
    }
}
</script>

{{-- Midtrans Snap.js --}}
<script src="{{ $snapUrl }}" data-client-key="{{ $clientKey }}"></script>
<script nonce="{{ $cspNonce ?? '' }}">
    const SNAP_TOKEN  = @json($snapToken);
    const SUCCESS_URL = @json(route('checkout.success'));
    const FAILED_URL  = @json(route('checkout.failed'));
    const ORDERS_URL  = @json(route('dashboard.orders'));

    function triggerPayment() {
        window.snap.pay(SNAP_TOKEN, {
            onSuccess: function (result) {
                window.location.href = SUCCESS_URL + '?order_id=' + (result.order_id || '');
            },
            onPending: function (result) {
                // Pembayaran pending (menunggu transfer, dll) — kembali ke orders
                window.location.href = ORDERS_URL + '?filter=pending';
            },
            onError: function (result) {
                window.location.href = FAILED_URL + '?order_id=' + (result.order_id || '');
            },
            onClose: function () {
                // User menutup popup tanpa menyelesaikan — tetap di halaman
            },
        });
    }

    // Auto-trigger Snap popup saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(triggerPayment, 600);
    });
</script>
@endpush
