@extends('layouts.admin')

@section('title', 'Edit Flash Sale: ' . $flashSale->title)

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.flash-sales.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-slate-900 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali ke Daftar
    </a>
    <h1 class="text-2xl font-bold text-slate-900 mt-2">Edit & Kelola Item Flash Sale</h1>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    {{-- Left: Edit Details --}}
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden sticky top-6">
            <div class="p-4 border-b border-slate-100 bg-slate-50/50">
                <h3 class="font-bold text-slate-900 uppercase tracking-wider text-xs">Informasi Dasar</h3>
            </div>
            <form action="{{ route('admin.flash-sales.update', $flashSale) }}" method="POST" class="p-6">
                @csrf @method('PUT')
                
                <div class="space-y-6">
                    {{-- Title --}}
                    <div>
                        <label for="title" class="block text-sm font-bold text-slate-700 mb-2">Judul</label>
                        <input type="text" name="title" id="title" value="{{ old('title', $flashSale->title) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:border-blue-500 transition-all outline-none" required>
                    </div>

                    {{-- Description --}}
                    <div>
                        <label for="description" class="block text-sm font-bold text-slate-700 mb-2">Deskripsi</label>
                        <textarea name="description" id="description" rows="2" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:border-blue-500 transition-all outline-none">{{ old('description', $flashSale->description) }}</textarea>
                    </div>

                    {{-- Start At --}}
                    <div>
                        <label for="start_at" class="block text-sm font-bold text-slate-700 mb-2">Waktu Mulai</label>
                        <input type="datetime-local" name="start_at" id="start_at" value="{{ old('start_at', $flashSale->start_at->format('Y-m-d\TH:i')) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:border-blue-500 transition-all outline-none" required>
                    </div>

                    {{-- End At --}}
                    <div>
                        <label for="end_at" class="block text-sm font-bold text-slate-700 mb-2">Waktu Selesai</label>
                        <input type="datetime-local" name="end_at" id="end_at" value="{{ old('end_at', $flashSale->end_at->format('Y-m-d\TH:i')) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:border-blue-500 transition-all outline-none" required>
                    </div>

                    {{-- Is Active --}}
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $flashSale->is_active) ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                        <label for="is_active" class="text-sm font-bold text-slate-700">Aktifkan Flash Sale</label>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-slate-100">
                    <button type="submit" class="w-full px-6 py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">Perbarui Pengaturan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Right: Manage Items --}}
    <div class="lg:col-span-2 space-y-8">
        {{-- Add Item Form --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-4 border-b border-slate-100 bg-slate-50/50">
                <h3 class="font-bold text-slate-900 uppercase tracking-wider text-xs">Tambah Item ke Flash Sale</h3>
            </div>
            <form action="{{ route('admin.flash-sales.items.add', $flashSale) }}" method="POST" class="p-6" x-data="{ type: 'Course' }">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Item Type --}}
                    <div>
                        <label for="item_type" class="block text-sm font-bold text-slate-700 mb-2">Tipe Item</label>
                        <select name="item_type" id="item_type" x-model="type" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:border-blue-500 outline-none">
                            <option value="Course">Kursus</option>
                            <option value="Bootcamp">Bootcamp</option>
                            <option value="Book">Buku Digital/Fisik</option>
                        </select>
                    </div>

                    {{-- Item Selector --}}
                    <div>
                        <label for="item_id" class="block text-sm font-bold text-slate-700 mb-2">Pilih Item</label>
                        {{-- Courses List --}}
                        <select name="item_id" x-show="type === 'Course'" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:border-blue-500 outline-none">
                            <option value="">-- Pilih Kursus --</option>
                            @foreach($courses as $c)
                                <option value="{{ $c->id }}">{{ $c->title }} ({{ rupiah($c->price) }})</option>
                            @endforeach
                        </select>
                        {{-- Bootcamps List --}}
                        <select name="item_id" x-show="type === 'Bootcamp'" x-cloak class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:border-blue-500 outline-none">
                            <option value="">-- Pilih Bootcamp --</option>
                            @foreach($bootcamps as $b)
                                <option value="{{ $b->id }}">{{ $b->title }} ({{ rupiah($b->price) }})</option>
                            @endforeach
                        </select>
                        {{-- Books List --}}
                        <select name="item_id" x-show="type === 'Book'" x-cloak class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:border-blue-500 outline-none">
                            <option value="">-- Pilih Buku --</option>
                            @foreach($books as $book)
                                <option value="{{ $book->id }}">{{ $book->title }} ({{ rupiah($book->price) }})</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Flash Sale Price --}}
                    <div>
                        <label for="flash_sale_price" class="block text-sm font-bold text-slate-700 mb-2">Harga Flash Sale (Rp)</label>
                        <input type="number" name="flash_sale_price" id="flash_sale_price" placeholder="Contoh: 99000" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:border-blue-500 transition-all outline-none" required>
                    </div>

                    {{-- Limit Quantity --}}
                    <div>
                        <label for="limit_quantity" class="block text-sm font-bold text-slate-700 mb-2">Limit Pembeli (Kosongkan jika Unlimited)</label>
                        <input type="number" name="limit_quantity" id="limit_quantity" placeholder="Contoh: 50" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:border-blue-500 transition-all outline-none">
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2 bg-slate-900 text-white font-bold rounded-lg hover:bg-slate-800 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Tambahkan Item
                    </button>
                </div>
            </form>
        </div>

        {{-- Items List --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-4 border-b border-slate-100 bg-slate-50/50">
                <h3 class="font-bold text-slate-900 uppercase tracking-wider text-xs">Item dalam Flash Sale ini</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500">Item</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500">Harga Promo</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500">Stok/Terjual</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 text-right">Hapus</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($flashSale->items as $item)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-lg bg-slate-100 overflow-hidden shrink-0 border border-slate-200">
                                        <img src="{{ storageUrl($item->itemable->thumbnail ?: $item->itemable->image) }}" class="w-full h-full object-cover">
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 line-clamp-1">{{ $item->itemable->title ?: $item->itemable->name }}</div>
                                        <div class="text-[10px] font-black text-blue-600 uppercase tracking-widest">{{ $item->item_type_label }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-rose-600">{{ rupiah($item->flash_sale_price) }}</div>
                                <div class="text-[10px] text-slate-400 line-through">{{ rupiah($item->itemable->price) }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <div class="text-sm font-semibold text-slate-700">
                                        {{ $item->sold_quantity }} / {{ $item->limit_quantity ?: '∞' }} Terjual
                                    </div>
                                    @if($item->limit_quantity)
                                        <div class="w-24 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                            <div class="h-full bg-blue-600 rounded-full" style="width: {{ min(100, ($item->sold_quantity / $item->limit_quantity) * 100) }}%"></div>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('admin.flash-sales.items.remove', $item) }}" method="POST" onsubmit="return confirm('Hapus item ini dari flash sale?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-500 italic text-sm">
                                Belum ada item ditambahkan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
