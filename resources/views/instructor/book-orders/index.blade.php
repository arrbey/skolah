@extends('layouts.instructor')

@section('title', 'Pengiriman Buku Fisik')

@section('page-header')
    <span class="text-base font-semibold text-gray-900">Monitoring Pengiriman Buku</span>
@endsection

@section('content')

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        @foreach([
            ['label' => 'Semua',        'val' => $stats['all'],        'color' => 'text-gray-900'],
            ['label' => 'Menunggu',     'val' => $stats['pending'],    'color' => 'text-yellow-600'],
            ['label' => 'Diproses',     'val' => $stats['processing'], 'color' => 'text-blue-600'],
            ['label' => 'Dikirim',      'val' => $stats['shipped'],    'color' => 'text-indigo-600'],
            ['label' => 'Terkirim',     'val' => $stats['delivered'],  'color' => 'text-green-600'],
        ] as $s)
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 font-medium">{{ $s['label'] }}</p>
            <p class="text-2xl font-bold {{ $s['color'] }} mt-1">{{ $s['val'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-4 mb-6">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <input type="text" name="search" placeholder="Cari nama user, buku, atau resi..."
                   value="{{ request('search') }}"
                   class="flex-1 min-w-[200px] rounded-xl border border-gray-300 px-4 py-2 text-sm focus:ring-2 focus:ring-primary-500">
            <select name="status" class="rounded-xl border border-gray-300 px-4 py-2 text-sm">
                <option value="">Semua Status</option>
                <option value="pending"    {{ request('status') === 'pending'    ? 'selected' : '' }}>Menunggu Pengiriman</option>
                <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Diproses</option>
                <option value="shipped"    {{ request('status') === 'shipped'    ? 'selected' : '' }}>Dalam Pengiriman</option>
                <option value="delivered"  {{ request('status') === 'delivered'  ? 'selected' : '' }}>Terkirim</option>
                <option value="cancelled"  {{ request('status') === 'cancelled'  ? 'selected' : '' }}>Dibatalkan</option>
            </select>
            <button type="submit" class="px-4 py-2 rounded-xl bg-primary-600 text-white text-sm font-medium hover:bg-primary-700">
                Filter
            </button>
            @if(request()->hasAny(['search','status']))
                <a href="{{ route('instructor.book-orders.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Reset</a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Buku / Pembeli</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kurir & Resi</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tgl. Order</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($bookOrders as $bo)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ $bo->book->cover_url }}" alt="" class="w-10 h-14 object-cover rounded shadow-sm shrink-0">
                                <div>
                                    <p class="font-semibold text-gray-900 line-clamp-1">{{ $bo->book->title }}</p>
                                    <p class="text-xs text-gray-500">{{ $bo->user->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $bo->user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @if($bo->courier)
                                <span class="inline-block px-2 py-0.5 rounded text-xs font-bold
                                    {{ $bo->courier === 'jne' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                    {{ $bo->courier === 'jnt' ? 'J&T' : strtoupper($bo->courier) }}
                                </span>
                            @else
                                <span class="text-gray-400 text-xs">—</span>
                            @endif
                            @if($bo->tracking_number)
                                <p class="text-xs font-mono text-gray-600 mt-1">{{ $bo->tracking_number }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $colors = [
                                    'pending'    => 'bg-yellow-100 text-yellow-700',
                                    'processing' => 'bg-blue-100 text-blue-700',
                                    'shipped'    => 'bg-indigo-100 text-indigo-700',
                                    'delivered'  => 'bg-green-100 text-green-700',
                                    'cancelled'  => 'bg-red-100 text-red-700',
                                ];
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $colors[$bo->shipping_status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $bo->status_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">
                            {{ $bo->order?->paid_at?->translatedFormat('d M Y') ?? '-' }}
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('instructor.book-orders.show', $bo) }}"
                               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-primary-50 text-primary-700 text-xs font-semibold hover:bg-primary-100 transition-colors">
                                Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center text-gray-400 text-sm">
                            Belum ada pesanan buku fisik.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bookOrders->hasPages())
        <div class="p-4 border-t border-gray-100">
            {{ $bookOrders->withQueryString()->links() }}
        </div>
        @endif
    </div>

@endsection
