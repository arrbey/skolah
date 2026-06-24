@extends('layouts.app')

@section('title', 'Pembayaran Berhasil' . ' — ' . \App\Models\Setting::get('site_name', '' . \App\Models\Setting::get('site_name', 'Skolah.com') . ''))

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-12 lg:py-20">

        {{-- Success Card --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

            {{-- Header --}}
            <div class="bg-gradient-to-r from-emerald-500 to-teal-500 px-6 py-8 text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-white">Pembayaran Berhasil! 🎉</h1>
                <p class="text-emerald-100 mt-2 text-sm">Terima kasih! Pesananmu telah diproses.</p>
            </div>

            {{-- Body --}}
            <div class="p-6 lg:p-8">

                @if($order)
                {{-- Order Info --}}
                <div class="bg-gray-50 rounded-xl p-5 mb-6">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">No. Order</span>
                            <p class="font-semibold text-gray-900 mt-0.5">{{ $order->order_number }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">Status</span>
                            <p class="mt-0.5">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $order->status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                    @if($order->status === 'paid')
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    @endif
                                    {{ $order->status_label }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <span class="text-gray-500">Total Bayar</span>
                            <p class="font-bold text-primary-600 text-lg mt-0.5">{{ $order->total_formatted }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">Tanggal</span>
                            <p class="font-semibold text-gray-900 mt-0.5">
                                {{ $order->paid_at_formatted ?? tanggal_waktu_indo($order->created_at) }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Items --}}
                @if($order->items->isNotEmpty())
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Item yang Dibeli</h3>
                <div class="space-y-3 mb-6">
                    @foreach($order->items as $item)
                    <div class="flex items-center justify-between py-3 px-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold
                                {{ match($item->item_type) {
                                    'course' => 'bg-blue-100 text-blue-600',
                                    'bootcamp' => 'bg-purple-100 text-purple-600',
                                    'book' => 'bg-amber-100 text-amber-600',
                                    'membership' => 'bg-emerald-100 text-emerald-600',
                                    default => 'bg-gray-100 text-gray-600',
                                } }}">
                                {{ match($item->item_type) {
                                    'course' => '📚',
                                    'bootcamp' => '🎓',
                                    'book' => '📖',
                                    'membership' => '⭐',
                                    default => '📦',
                                } }}
                            </span>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $item->item_name }}</p>
                                <p class="text-xs text-gray-500">{{ $item->item_type_label }}{{ $item->quantity > 1 ? ' × ' . $item->quantity : '' }}</p>
                            </div>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">{{ $item->subtotal_formatted }}</span>
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Discount info --}}
                @if($order->discount_amount > 0)
                <div class="flex items-center gap-2 p-3 bg-green-50 border border-green-200 rounded-lg mb-6 text-sm">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    <span class="text-green-700">
                        Kamu hemat <strong>{{ $order->discount_amount_formatted }}</strong>
                        @if($order->promo_code)
                            dengan kode promo <strong>{{ $order->promo_code }}</strong>
                        @endif
                    </span>
                </div>
                @endif

                @else
                {{-- No order data --}}
                <div class="text-center py-4">
                    <p class="text-gray-500 text-sm">Data pesanan sedang diproses. Kamu akan menerima email konfirmasi segera.</p>
                </div>
                @endif

                {{-- Info Notice --}}
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="text-sm">
                            <p class="font-medium text-blue-800">Apa selanjutnya?</p>
                            <ul class="text-blue-600 mt-1 space-y-1">
                                <li>• Email konfirmasi pembayaran akan dikirim ke emailmu</li>
                                <li>• Kursus & bootcamp langsung bisa diakses di dashboard</li>
                                <li>• Buku fisik akan dikirim dalam 1-3 hari kerja</li>
                                <li>• Membership aktif setelah pembayaran dikonfirmasi</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- CTA Buttons --}}
                <div class="flex flex-col sm:flex-row items-center gap-3">
                    <a href="{{ route('dashboard') }}"
                       class="w-full sm:w-auto text-center px-6 py-3 rounded-xl bg-gradient-to-r from-primary-600 to-secondary-600 text-white font-semibold text-sm hover:from-primary-700 hover:to-secondary-700 transition-all shadow-lg shadow-primary-600/20">
                        Mulai Belajar →
                    </a>
                    <a href="{{ route('dashboard.orders') }}"
                       class="w-full sm:w-auto text-center px-6 py-3 rounded-xl border border-gray-300 text-gray-700 font-medium text-sm hover:bg-gray-50 transition-colors">
                        Lihat Riwayat Pesanan
                    </a>
                    <a href="{{ route('courses.index') }}"
                       class="w-full sm:w-auto text-center px-6 py-3 rounded-xl text-gray-500 text-sm hover:text-primary-600 transition-colors">
                        Jelajahi Kursus Lain
                    </a>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection
