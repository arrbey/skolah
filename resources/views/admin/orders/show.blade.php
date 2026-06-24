@extends('layouts.admin')

@section('title', 'Detail Pesanan #' . $order->order_number)

@section('page-header')
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.orders.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <span class="text-base font-semibold text-gray-900">Pesanan #{{ $order->order_number }}</span>
    </div>
@endsection

@section('content')
    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Order Info --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Items --}}
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Item Pesanan</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500">Item</th>
                                <th class="text-center px-6 py-3 text-xs font-semibold text-gray-500">Qty</th>
                                <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500">Harga</th>
                                <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($order->items as $item)
                                <tr>
                                    <td class="px-6 py-3">
                                        <p class="font-medium text-gray-900">{{ $item->item_name }}</p>
                                        <p class="text-xs text-gray-400">{{ class_basename($item->itemable_type ?? '-') }}</p>
                                    </td>
                                    <td class="px-6 py-3 text-center text-gray-600">{{ $item->quantity }}</td>
                                    <td class="px-6 py-3 text-right text-gray-600">{{ rupiah($item->price) }}</td>
                                    <td class="px-6 py-3 text-right font-semibold text-gray-900">{{ rupiah($item->price * $item->quantity) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 space-y-1 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span>{{ $order->subtotal_formatted }}</span>
                    </div>
                    @if($order->discount_amount > 0)
                        <div class="flex justify-between text-green-600">
                            <span>Diskon {{ $order->promo_code ? '('.$order->promo_code.')' : '' }}</span>
                            <span>-{{ rupiah($order->discount_amount) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between font-bold text-gray-900 text-base pt-2 border-t border-gray-200">
                        <span>Total</span>
                        <span>{{ $order->total_formatted }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Status --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Status Pesanan</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Status</span>
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $order->status_color }}">{{ $order->status_label }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Metode</span>
                        <span class="text-gray-900">{{ $order->payment_method ?? '-' }}</span>
                    </div>
                    @if($order->midtrans_transaction_id)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Midtrans ID</span>
                            <span class="text-gray-900 font-mono text-xs">{{ $order->midtrans_transaction_id }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-500">Dibuat</span>
                        <span class="text-gray-900">{{ $order->created_at?->translatedFormat('d M Y H:i') }}</span>
                    </div>
                    @if($order->paid_at)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Dibayar</span>
                            <span class="text-gray-900">{{ $order->paid_at_formatted }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- User Info --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Info Pembeli</h3>
                <div class="flex items-center gap-3 mb-3">
                    <img src="{{ avatarUrl($order->user?->avatar, $order->user?->name ?? 'U') }}" class="w-10 h-10 rounded-full object-cover" alt="">
                    <div>
                        <p class="font-medium text-gray-900">{{ $order->user?->name ?? '-' }}</p>
                        <p class="text-xs text-gray-500">{{ $order->user?->email ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
