@extends('layouts.admin')

@section('title', 'Kelola Pesanan')

@section('page-header')
    <span class="text-base font-semibold text-gray-900">Pesanan</span>
@endsection

@section('content')
    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 font-medium">Total</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 font-medium">Lunas</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['paid'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 font-medium">Pending</p>
            <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $stats['pending'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 font-medium">Gagal</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['failed'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 font-medium">Revenue</p>
            <p class="text-2xl font-bold text-primary-600 mt-1">{{ rupiah_short($stats['revenue']) }}</p>
        </div>
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-4 mb-6">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <input type="text" name="search" placeholder="Cari order / user..." value="{{ request('search') }}"
                   class="flex-1 min-w-[180px] rounded-xl border border-gray-300 px-4 py-2 text-sm focus:ring-2 focus:ring-primary-500">
            <select name="status" class="rounded-xl border border-gray-300 px-4 py-2 text-sm">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Lunas</option>
                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Gagal</option>
                <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>Refund</option>
            </select>
            <input type="date" name="from" value="{{ request('from') }}" class="rounded-xl border border-gray-300 px-3 py-2 text-sm">
            <input type="date" name="to" value="{{ request('to') }}" class="rounded-xl border border-gray-300 px-3 py-2 text-sm">
            <button type="submit" class="px-4 py-2 rounded-xl bg-primary-600 text-white text-sm font-medium hover:bg-primary-700">Filter</button>
            @if(request()->hasAny(['search','status','from','to']))
                <a href="{{ route('admin.orders.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Reset</a>
            @endif
        </form>
    </div>

    {{-- Export --}}
    <div class="flex items-center gap-3 mb-4">
        <a href="{{ route('admin.orders.export', array_merge(request()->query(), ['format' => 'excel'])) }}" class="px-4 py-2 rounded-xl border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50">Export Excel</a>
        <a href="{{ route('admin.orders.export', array_merge(request()->query(), ['format' => 'pdf'])) }}" class="px-4 py-2 rounded-xl border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50">Export PDF</a>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Order</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">User</th>
                        <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Total</th>
                        <th class="text-center px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Pembayaran</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                        <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 font-mono text-xs text-gray-700">{{ $order->order_number }}</td>
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-2">
                                    <img src="{{ avatarUrl($order->user?->avatar, $order->user?->name ?? 'U') }}" class="w-7 h-7 rounded-full object-cover" alt="">
                                    <span class="text-gray-700">{{ $order->user?->name ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-3 text-right font-semibold text-gray-900">{{ $order->total_formatted }}</td>
                            <td class="px-6 py-3 text-center">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $order->status_color }}">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-gray-600">{{ $order->payment_method ?? '-' }}</td>
                            <td class="px-6 py-3 text-gray-500">{{ $order->created_at?->translatedFormat('d M Y H:i') }}</td>
                            <td class="px-6 py-3 text-right">
                                <a href="{{ route('admin.orders.show', $order) }}" class="px-3 py-1 rounded-lg bg-gray-100 text-gray-700 text-xs font-medium hover:bg-gray-200">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-6 py-8 text-center text-gray-400">Tidak ada pesanan ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $orders->withQueryString()->links() }}</div>
@endsection
