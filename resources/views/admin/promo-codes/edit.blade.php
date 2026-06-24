@extends('layouts.admin')

@section('title', 'Edit Promo Code')

@section('page-header')
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.promo-codes.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <span class="text-base font-semibold text-gray-900">Edit Promo: {{ $promoCode->code }}</span>
    </div>
@endsection

@section('content')
    <div class="max-w-xl">
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <form action="{{ route('admin.promo-codes.update', $promoCode) }}" method="POST">
                @csrf @method('PUT')
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kode Promo <span class="text-red-500">*</span></label>
                        <input type="text" name="code" value="{{ old('code', $promoCode->code) }}" required
                               class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm uppercase focus:ring-2 focus:ring-primary-500 @error('code') border-red-400 @enderror">
                        @error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Diskon <span class="text-red-500">*</span></label>
                            <select name="discount_type" required class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                                <option value="percent" {{ old('discount_type', $promoCode->discount_type) === 'percent' ? 'selected' : '' }}>Persentase (%)</option>
                                <option value="fixed" {{ old('discount_type', $promoCode->discount_type) === 'fixed' ? 'selected' : '' }}>Nominal Tetap (Rp)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nilai Diskon <span class="text-red-500">*</span></label>
                            <input type="number" name="discount_value" value="{{ old('discount_value', $promoCode->discount_value) }}" required min="1"
                                   class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 @error('discount_value') border-red-400 @enderror">
                            @error('discount_value') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Berlaku Untuk <span class="text-red-500">*</span></label>
                        <select name="applicable_type" required class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                            @foreach(\App\Models\PromoCode::applicableTypes() as $value => $label)
                                <option value="{{ $value }}" {{ old('applicable_type', $promoCode->applicable_type) === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Pilih produk yang bisa menggunakan promo ini</p>
                        @error('applicable_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Min. Pembelian</label>
                            <input type="number" name="min_purchase" value="{{ old('min_purchase', $promoCode->min_purchase) }}" min="0"
                                   class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Max. Penggunaan</label>
                            <input type="number" name="max_uses" value="{{ old('max_uses', $promoCode->max_uses) }}" min="1"
                                   class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Berlaku Sampai</label>
                        <input type="date" name="expires_at" value="{{ old('expires_at', $promoCode->expires_at?->format('Y-m-d')) }}"
                               class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 @error('expires_at') border-red-400 @enderror">
                        @error('expires_at') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $promoCode->is_active) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <label for="is_active" class="text-sm text-gray-700">Aktif</label>
                    </div>

                    {{-- Usage info --}}
                    <div class="bg-gray-50 rounded-xl p-4 text-sm text-gray-600">
                        <p>Sudah digunakan: <strong>{{ $promoCode->used_count }}x</strong></p>
                    </div>
                </div>

                <div class="mt-6 flex items-center gap-3">
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary-600 text-white text-sm font-medium hover:bg-primary-700">Update</button>
                    <a href="{{ route('admin.promo-codes.index') }}" class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
