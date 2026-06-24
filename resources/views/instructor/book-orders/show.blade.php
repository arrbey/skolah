@extends('layouts.instructor')

@section('title', 'Detail Pengiriman — ' . $bookOrder->book->title)

@section('page-header')
    <div class="flex items-center gap-3">
        <a href="{{ route('instructor.book-orders.index') }}"
           class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-base font-bold text-gray-900">Detail Pengiriman Buku</h1>
            <p class="text-xs text-gray-500">{{ $bookOrder->book->title }}</p>
        </div>
    </div>
@endsection

@section('content')
@php
    $book  = $bookOrder->book;
    $user  = $bookOrder->user;
    $order = $bookOrder->order;
    $statusColors = [
        'pending'    => ['bg' => 'bg-yellow-100',  'text' => 'text-yellow-700',  'border' => 'border-yellow-200'],
        'processing' => ['bg' => 'bg-blue-100',    'text' => 'text-blue-700',    'border' => 'border-blue-200'],
        'shipped'    => ['bg' => 'bg-indigo-100',  'text' => 'text-indigo-700',  'border' => 'border-indigo-200'],
        'delivered'  => ['bg' => 'bg-green-100',   'text' => 'text-green-700',   'border' => 'border-green-200'],
        'cancelled'  => ['bg' => 'bg-red-100',     'text' => 'text-red-700',     'border' => 'border-red-200'],
    ];
    $sc = $statusColors[$bookOrder->shipping_status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'border' => 'border-gray-200'];
@endphp

@if(session('success'))
<div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-800 font-medium flex items-center gap-2">
    ✅ {{ session('success') }}
</div>
@endif

<div class="grid lg:grid-cols-3 gap-6">

    {{-- ═══ KOLOM KIRI (2/3) ══════════════════════════════════════════════ --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Buku & Pembeli --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 flex gap-4">
            <img src="{{ $book->cover_url }}" alt="" class="w-20 h-28 object-cover rounded-lg shadow-sm shrink-0">
            <div class="flex-1 min-w-0">
                <h2 class="text-base font-bold text-gray-900 mb-1">{{ $book->title }}</h2>
                <p class="text-xs text-gray-500 mb-3">{{ $book->author ?? '-' }}</p>
                <div class="grid sm:grid-cols-2 gap-2 text-xs">
                    <div><span class="text-gray-400">Pembeli:</span> <span class="font-medium text-gray-800">{{ $user->name }}</span></div>
                    <div><span class="text-gray-400">Email:</span> <span class="font-medium text-gray-800">{{ $user->email }}</span></div>
                    <div><span class="text-gray-400">No. Order:</span> <span class="font-mono font-medium text-gray-800">{{ $order?->order_number }}</span></div>
                    <div><span class="text-gray-400">Qty:</span> <span class="font-medium text-gray-800">{{ $bookOrder->quantity }} × {{ rupiah($bookOrder->price) }}</span></div>
                    <div><span class="text-gray-400">Total:</span> <span class="font-bold text-primary-600">{{ rupiah($bookOrder->total_price) }}</span></div>
                    <div>
                        <span class="text-gray-400">Kurir:</span>
                        @if($bookOrder->courier)
                            <span class="ml-1 px-2 py-0.5 rounded text-xs font-bold
                                {{ $bookOrder->courier === 'jne' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                {{ $bookOrder->courier_label }}
                            </span>
                        @else
                            <span class="text-gray-400">Belum ditentukan</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Alamat Pengiriman --}}
        @if($bookOrder->shipping_address)
        @php $addr = $bookOrder->shipping_address; @endphp
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-700">📍 Alamat Pengiriman</h3>
            </div>
            <div class="p-5 text-sm text-gray-700 space-y-0.5">
                @if(!empty($addr['name']))     <p class="font-bold">{{ $addr['name'] }}</p>@endif
                @if(!empty($addr['phone']))    <p class="text-gray-500">{{ $addr['phone'] }}</p>@endif
                @if(!empty($addr['address']))  <p>{{ $addr['address'] }}</p>@endif
                @if(!empty($addr['city']))     <p>{{ $addr['city'] }}{{ !empty($addr['province']) ? ', ' . $addr['province'] : '' }}</p>@endif
                @if(!empty($addr['postal_code'])) <p>{{ $addr['postal_code'] }}</p>@endif
            </div>
        </div>
        @endif

        {{-- Foto Bukti --}}
        @if($bookOrder->delivery_photo)
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-700">📸 Foto Bukti Terima</h3>
            </div>
            <div class="p-5">
                <img src="{{ $bookOrder->delivery_photo_url }}" alt="Bukti terima"
                     class="max-w-xs rounded-xl border border-gray-200 shadow-sm">
                @if($bookOrder->delivered_at)
                    <p class="text-xs text-gray-400 mt-2">Diterima: {{ $bookOrder->delivered_at->translatedFormat('d F Y, H:i') }}</p>
                @endif
            </div>
        </div>
        @endif

        {{-- History --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-700">📋 Riwayat Pengiriman</h3>
            </div>
            <div class="p-5">
                @if($bookOrder->histories->isEmpty())
                    <p class="text-sm text-gray-400">Belum ada riwayat perubahan status.</p>
                @else
                <div class="relative pl-6 space-y-4">
                    @foreach($bookOrder->histories as $i => $history)
                    @php
                        $isLast = $i === $bookOrder->histories->count() - 1;
                        $dotColor = match($history->status) {
                            'pending'    => 'bg-yellow-400',
                            'processing' => 'bg-blue-500',
                            'shipped'    => 'bg-indigo-500',
                            'delivered'  => 'bg-green-500',
                            'cancelled'  => 'bg-red-500',
                            default      => 'bg-gray-400',
                        };
                    @endphp
                    <div class="relative">
                        @if(!$isLast)
                        <div class="absolute left-[-16px] top-4 bottom-[-16px] w-0.5 bg-gray-200"></div>
                        @endif
                        <div class="absolute left-[-20px] top-1.5 w-2.5 h-2.5 rounded-full {{ $dotColor }} ring-2 ring-white"></div>
                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                            <div class="flex items-start justify-between gap-2 flex-wrap">
                                <p class="text-sm font-semibold text-gray-900">{{ $history->status_label }}</p>
                                <p class="text-xs text-gray-400 whitespace-nowrap">{{ $history->created_at->translatedFormat('d M Y, H:i') }}</p>
                            </div>
                            <div class="mt-1 space-y-0.5 text-xs text-gray-500">
                                @if($history->courier)    <p>Kurir: <span class="font-semibold">{{ $history->courier_label }}</span></p>@endif
                                @if($history->tracking_number) <p>Resi: <span class="font-mono font-semibold text-gray-700">{{ $history->tracking_number }}</span></p>@endif
                                @if($history->note)       <p class="italic">"{{ $history->note }}"</p>@endif
                                <p>Oleh: <span class="font-medium text-gray-600">{{ $history->actor_name ?? 'Sistem' }}</span></p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ═══ KOLOM KANAN (1/3) ══════════════════════════════════════════════ --}}
    <div class="space-y-5">

        <div class="bg-white rounded-xl border {{ $sc['border'] }} overflow-hidden">
            <div class="px-5 py-3 border-b {{ $sc['border'] }} {{ $sc['bg'] }}">
                <h3 class="text-sm font-semibold {{ $sc['text'] }}">Status Saat Ini</h3>
            </div>
            <div class="p-5 text-center">
                <span class="text-3xl">
                    {{ match($bookOrder->shipping_status) {
                        'pending'    => '🕐',
                        'processing' => '📦',
                        'shipped'    => '🚚',
                        'delivered'  => '✅',
                        'cancelled'  => '❌',
                        default      => '📋',
                    } }}
                </span>
                <p class="text-base font-bold {{ $sc['text'] }} mt-2">{{ $bookOrder->status_label }}</p>
                @if($bookOrder->shipped_at)
                    <p class="text-xs text-gray-400 mt-1">Dikirim: {{ $bookOrder->shipped_at->translatedFormat('d M Y') }}</p>
                @endif
                @if($bookOrder->delivered_at)
                    <p class="text-xs text-gray-400">Diterima: {{ $bookOrder->delivered_at->translatedFormat('d M Y') }}</p>
                @endif
            </div>
        </div>

        @if($bookOrder->shipping_status !== 'delivered' && $bookOrder->shipping_status !== 'cancelled')
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden" x-data="{ status: '{{ $bookOrder->shipping_status }}' }">
            <div class="px-5 py-3 border-b border-gray-100 bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-700">🔄 Update Status</h3>
            </div>
            <div class="p-5">
                <form action="{{ route('instructor.book-orders.update-status', $bookOrder) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status Baru</label>
                        <select name="shipping_status" x-model="status"
                                class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                            <option value="pending"    {{ $bookOrder->shipping_status === 'pending'    ? 'selected' : '' }}>🕐 Menunggu</option>
                            <option value="processing" {{ $bookOrder->shipping_status === 'processing' ? 'selected' : '' }}>📦 Diproses</option>
                            <option value="shipped"    {{ $bookOrder->shipping_status === 'shipped'    ? 'selected' : '' }}>🚚 Dikirim</option>
                            <option value="cancelled"  {{ $bookOrder->shipping_status === 'cancelled'  ? 'selected' : '' }}>❌ Batalkan</option>
                        </select>
                    </div>

                    <div x-show="status === 'shipped'" x-transition class="space-y-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kurir <span class="text-red-500">*</span></label>
                            <select name="courier" class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                                <option value="">-- Pilih Kurir --</option>
                                <option value="jne" {{ $bookOrder->courier === 'jne' ? 'selected' : '' }}>JNE</option>
                                <option value="jnt" {{ $bookOrder->courier === 'jnt' ? 'selected' : '' }}>J&T Express</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nomor Resi <span class="text-red-500">*</span></label>
                            <input type="text" name="tracking_number" value="{{ $bookOrder->tracking_number }}"
                                   placeholder="Masukkan nomor resi..."
                                   class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-primary-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Catatan (opsional)</label>
                        <textarea name="note" rows="2" placeholder="Catatan untuk pembeli..."
                                  class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm resize-none focus:ring-2 focus:ring-primary-500"></textarea>
                    </div>

                    <button type="submit"
                            class="w-full py-2.5 rounded-xl bg-primary-600 text-white text-sm font-semibold hover:bg-primary-700 transition-colors">
                        💾 Simpan & Kirim Notifikasi
                    </button>
                </form>
            </div>
        </div>

        @if($bookOrder->shipping_status === 'shipped')
        <div class="bg-white rounded-xl border border-green-200 overflow-hidden">
            <div class="px-5 py-3 border-b border-green-100 bg-green-50">
                <h3 class="text-sm font-semibold text-green-700">✅ Konfirmasi Terkirim</h3>
            </div>
            <div class="p-5">
                <form action="{{ route('instructor.book-orders.confirm-delivery', $bookOrder) }}" method="POST"
                      enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Foto Bukti Terima <span class="text-red-500">*</span></label>
                        <input type="file" name="delivery_photo" accept="image/*" required
                               class="w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                        <p class="text-[11px] text-gray-400 mt-1">Format: JPG, PNG, WEBP. Maks 3 MB.</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Catatan (opsional)</label>
                        <textarea name="note" rows="2" placeholder="Misal: Diterima oleh pemilik langsung"
                                  class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm resize-none focus:ring-2 focus:ring-green-500"></textarea>
                    </div>
                    <button type="submit"
                            class="w-full py-2.5 rounded-xl bg-green-600 text-white text-sm font-semibold hover:bg-green-700 transition-colors">
                        📸 Upload & Konfirmasi Terkirim
                    </button>
                </form>
            </div>
        </div>
        @endif

        @else
        <div class="bg-gray-50 rounded-xl border border-gray-200 p-5 text-center text-sm text-gray-500">
            @if($bookOrder->shipping_status === 'delivered') ✅ Pesanan sudah selesai terkirim.
            @else ❌ Pesanan ini telah dibatalkan.
            @endif
        </div>
        @endif
    </div>
</div>
@endsection
