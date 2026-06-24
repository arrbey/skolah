@extends('layouts.app')

@section('title', 'Pembayaran Buku — ' . $book->title)

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
            <h1 class="text-2xl font-bold text-white">Pembayaran Buku</h1>
            <p class="text-gray-400 mt-1 text-sm">Selesaikan pembayaran untuk mendapatkan buku kamu</p>
        </div>

        {{-- Order Summary Card --}}
        <div class="bg-gray-900 border border-white/10 rounded-2xl overflow-hidden mb-6">
            <div class="flex items-start gap-4 p-6">
                @if($book->cover_image)
                <x-picture
                    :src="storageUrl($book->cover_image)"
                    :alt="$book->title"
                    class="w-20 h-28 rounded-xl object-cover flex-shrink-0 ring-1 ring-white/10" />
                @endif
                <div class="flex-1 min-w-0">
                    <span class="text-xs font-medium px-2 py-0.5 rounded-full
                        {{ $book->type === 'digital' ? 'bg-sky-500/20 text-sky-300' : ($book->type === 'both' ? 'bg-violet-500/20 text-violet-300' : 'bg-amber-500/20 text-amber-300') }}">
                        {{ $book->type_label }}
                    </span>
                    <h2 class="text-base font-semibold text-white mt-1 mb-0.5">{{ $book->title }}</h2>
                    <p class="text-xs text-gray-500">{{ $book->author ?? $book->instructor->name ?? '' }}</p>
                </div>
            </div>

            <div class="px-6 pb-6">
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
            onclick="triggerBookPayment()">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
            Bayar Sekarang
        </button>

        <p class="text-center text-xs text-gray-500 mt-4">
            Pembayaran aman diproses oleh <span class="text-gray-400 font-medium">Midtrans</span>
        </p>

        <div class="flex items-center justify-center gap-2 mt-4 text-xs text-gray-600">
            GoPay · OVO · DANA · Transfer Bank · Kartu Kredit
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ $snapUrl }}" data-client-key="{{ $clientKey }}"></script>
<script nonce="{{ $cspNonce ?? '' }}">
    const SNAP_TOKEN   = @json($snapToken);
    const SUCCESS_URL  = @json(route('book.checkout.success'));
    const FAILED_URL   = @json(route('book.checkout.failed'));

    function triggerBookPayment() {
        window.snap.pay(SNAP_TOKEN, {
            onSuccess: function(result) {
                window.location.href = SUCCESS_URL + '?order_id=' + result.order_id;
            },
            onPending: function(result) {
                window.location.href = '/dashboard/orders?pending=1';
            },
            onError: function(result) {
                window.location.href = FAILED_URL + '?order_id=' + result.order_id;
            },
            onClose: function() {
                // User tutup popup — tetap di halaman
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(triggerBookPayment, 600);
    });
</script>
@endpush
