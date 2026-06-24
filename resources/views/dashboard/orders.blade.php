@extends('layouts.dashboard')

@section('title', 'Riwayat Order')

@section('page-header')
    <h1 class="text-lg font-bold text-gray-900">Riwayat Order</h1>
@endsection

@section('content')
<div class="space-y-6">

    {{-- ═══ FILTER TABS ═══════════════════════════════════════════════════════ --}}
    <div class="flex items-center gap-2 flex-wrap">
        @foreach([
            ['key' => 'all',     'label' => 'Semua',              'count' => $stats['all']],
            ['key' => 'paid',    'label' => 'Lunas',              'count' => $stats['paid']],
            ['key' => 'pending', 'label' => 'Menunggu Bayar',     'count' => $stats['pending']],
            ['key' => 'failed',  'label' => 'Gagal',              'count' => $stats['failed']],
        ] as $tab)
            <a href="{{ route('dashboard.orders', ['filter' => $tab['key']]) }}"
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

    {{-- ═══ ORDERS TABLE ══════════════════════════════════════════════════════ --}}
    @if($orders->isNotEmpty())
        {{-- Desktop table --}}
        <div class="hidden md:block bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left">
                            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">No. Order</th>
                            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Item</th>
                            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Total</th>
                            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($orders as $order)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3.5">
                                    <span class="font-mono text-xs text-gray-600">{{ $order->order_number }}</span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div>
                                        @foreach($order->items->take(2) as $item)
                                            <div class="flex items-center gap-1.5 {{ !$loop->first ? 'mt-1' : '' }}">
                                                <span class="text-[10px] px-1.5 py-0.5 rounded font-bold uppercase
                                                    @if($item->item_type === 'course') bg-blue-50 text-blue-600
                                                    @elseif($item->item_type === 'bootcamp') bg-purple-50 text-purple-600
                                                    @elseif($item->item_type === 'book') bg-amber-50 text-amber-600
                                                    @elseif($item->item_type === 'membership') bg-pink-50 text-pink-600
                                                    @else bg-gray-50 text-gray-600 @endif">
                                                    {{ $item->item_type_label }}
                                                </span>
                                                <span class="text-xs text-gray-700 truncate max-w-[200px]">{{ $item->item_name }}</span>
                                            </div>
                                        @endforeach
                                        @if($order->items->count() > 2)
                                            <span class="text-[10px] text-gray-400 mt-0.5 block">+{{ $order->items->count() - 2 }} item lainnya</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-xs text-gray-500">
                                    {{ tanggal_singkat_indo($order->created_at) }}
                                    @if($order->paid_at)
                                        <br><span class="text-green-600">Dibayar {{ tanggal_singkat_indo($order->paid_at) }}</span>
                                    @elseif($order->status === 'pending' && $order->payment_expires_at)
                                        <br>
                                        @if($order->is_expired)
                                            <span class="text-red-600 font-semibold">⏰ Kedaluwarsa</span>
                                        @else
                                            <span class="text-orange-600">⏳ {{ $order->time_remaining }} lagi</span>
                                        @endif
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 font-semibold text-gray-900">{{ $order->total_formatted }}</td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold
                                        @if($order->status === 'paid') bg-green-50 text-green-700
                                        @elseif($order->status === 'pending') bg-yellow-50 text-yellow-700
                                        @elseif($order->status === 'failed') bg-red-50 text-red-700
                                        @elseif($order->status === 'refunded') bg-purple-50 text-purple-700
                                        @else bg-gray-50 text-gray-700 @endif">
                                        {{ $order->status_label }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    @if($order->status === 'pending' && !$order->is_expired)
                                        <a href="{{ route('dashboard.orders.pay', $order) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold
                                                  bg-primary-600 text-white hover:bg-primary-700 transition-colors shadow-sm">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                            </svg>
                                            Bayar
                                        </a>
                                    @endif

                                    @if($order->status === 'paid')
                                        <a href="{{ route('dashboard.orders.invoice', $order) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold
                                                  bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            Invoice
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Mobile cards --}}
        <div class="md:hidden space-y-3">
            @foreach($orders as $order)
                <div class="bg-white rounded-xl border border-gray-200 p-4">
                    <div class="flex items-center justify-between mb-3">
                        <span class="font-mono text-xs text-gray-500">{{ $order->order_number }}</span>
                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold
                            @if($order->status === 'paid') bg-green-50 text-green-700
                            @elseif($order->status === 'pending') bg-yellow-50 text-yellow-700
                            @elseif($order->status === 'failed') bg-red-50 text-red-700
                            @else bg-gray-50 text-gray-700 @endif">
                            {{ $order->status_label }}
                        </span>
                    </div>

                    @foreach($order->items->take(2) as $item)
                        <div class="flex items-center gap-1.5 {{ !$loop->first ? 'mt-1' : '' }}">
                            <span class="text-[10px] px-1.5 py-0.5 rounded font-bold uppercase
                                @if($item->item_type === 'course') bg-blue-50 text-blue-600
                                @elseif($item->item_type === 'bootcamp') bg-purple-50 text-purple-600
                                @elseif($item->item_type === 'book') bg-amber-50 text-amber-600
                                @elseif($item->item_type === 'membership') bg-pink-50 text-pink-600
                                @else bg-gray-50 text-gray-600 @endif">
                                {{ $item->item_type_label }}
                            </span>
                            <span class="text-xs text-gray-700 truncate">{{ $item->item_name }}</span>
                        </div>
                    @endforeach

                    <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100">
                        <div>
                            <span class="text-xs text-gray-400">{{ tanggal_singkat_indo($order->created_at) }}</span>
                            @if($order->status === 'pending' && $order->payment_expires_at)
                                <br>
                                @if($order->is_expired)
                                    <span class="text-[10px] text-red-600 font-semibold">⏰ Kedaluwarsa</span>
                                @else
                                    <span class="text-[10px] text-orange-600">⏳ {{ $order->time_remaining }} lagi</span>
                                @endif
                            @endif
                        </div>
                        <span class="text-sm font-bold text-gray-900">{{ $order->total_formatted }}</span>
                    </div>

                    @if($order->status === 'pending' && !$order->is_expired)
                        <a href="{{ route('dashboard.orders.pay', $order) }}"
                           class="mt-3 w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold
                                  bg-primary-600 text-white hover:bg-primary-700 transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            Bayar Sekarang
                        </a>
                    @endif

                    @if($order->status === 'paid')
                        <a href="{{ route('dashboard.orders.invoice', $order) }}"
                           class="mt-3 w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold
                                  bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Download Invoice
                        </a>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $orders->withQueryString()->links() }}
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                @include('layouts.partials.icon', ['name' => 'shopping-bag', 'class' => 'w-8 h-8 text-gray-400'])
            </div>
            <h3 class="text-base font-bold text-gray-900 mb-1">Belum Ada Transaksi</h3>
            <p class="text-sm text-gray-500 mb-4">Riwayat pembelian kamu akan muncul di sini.</p>
            <a href="{{ route('courses.index') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary-600 text-white text-sm font-semibold hover:bg-primary-700 transition-colors">
                Mulai Belanja
            </a>
        </div>
    @endif

</div>
@endsection
