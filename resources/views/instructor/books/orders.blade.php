@extends('layouts.instructor')

@section('title', 'Daftar Pembeli - ' . $book->title)

@section('page-header')
    <div class="flex items-center gap-3">
        <a href="{{ route('instructor.books.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-lg font-bold text-gray-900">Daftar Pembeli</h1>
            <p class="text-sm text-gray-500">{{ $book->title }}</p>
        </div>
    </div>
@endsection

@section('content')
<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Pembeli</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Tgl Order</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Harga</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Status</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($orders as $orderItem)
                    @php $order = $orderItem->order; @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ avatarUrl($order->user) }}" class="w-8 h-8 rounded-full object-cover border border-gray-100" alt="">
                                <div>
                                    <p class="font-bold text-gray-900 leading-none">{{ $order->user->name }}</p>
                                    <p class="text-[10px] text-gray-400 mt-1 uppercase font-bold">{{ $order->order_number }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center text-gray-500 text-xs">
                            {{ $order->created_at->format('d M Y H:i') }}
                        </td>
                        <td class="px-6 py-4 text-right font-bold text-gray-900">
                            {{ rupiah($orderItem->price * $orderItem->quantity) }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase 
                                {{ $order->status === 'paid' ? 'bg-green-50 text-green-700' : 'bg-yellow-50 text-yellow-700' }}">
                                {{ $order->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button class="px-3 py-1.5 rounded-lg bg-gray-50 text-gray-500 text-[10px] font-bold uppercase hover:bg-gray-100 transition-colors">
                                Detail
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400 font-medium">Belum ada pesanan untuk buku ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($orders->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
