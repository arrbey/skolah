@extends('layouts.admin')

@section('title', 'Tambah Membership Plan')

@section('page-header')
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.memberships.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <span class="text-base font-semibold text-gray-900">Tambah Membership Plan</span>
    </div>
@endsection

@section('content')
    <div class="max-w-xl">
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <form action="{{ route('admin.memberships.store') }}" method="POST">
                @csrf
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Plan <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="Pro Plan"
                               class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 @error('name') border-red-400 @enderror">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea name="description" rows="3" placeholder="Deskripsi singkat plan ini..."
                                  class="tinymce w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 @error('description') border-red-400 @enderror">{{ old('description') }}</textarea>
                        @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Harga Bulanan (Rp) <span class="text-red-500">*</span></label>
                            <input type="number" name="price_monthly" value="{{ old('price_monthly') }}" required min="0"
                                   class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 @error('price_monthly') border-red-400 @enderror">
                            @error('price_monthly') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Harga Tahunan (Rp) <span class="text-red-500">*</span></label>
                            <input type="number" name="price_yearly" value="{{ old('price_yearly') }}" required min="0"
                                   class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 @error('price_yearly') border-red-400 @enderror">
                            @error('price_yearly') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fitur / Benefit</label>
                        <textarea name="features_text" rows="5" placeholder="Satu fitur per baris, contoh:&#10;Akses semua kursus&#10;Download sertifikat&#10;Support prioritas"
                                  class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 @error('features_text') border-red-400 @enderror">{{ old('features_text') }}</textarea>
                        <p class="text-xs text-gray-400 mt-1">Tulis satu fitur per baris</p>
                        @error('features_text') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Pilih Course --}}
                    <div x-data="{ search: '', open: false }">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Course yang Termasuk</label>
                        <p class="text-xs text-gray-400 mb-2">Pilih course yang bisa diakses oleh member plan ini</p>

                        <div class="relative mb-2">
                            <input type="text" x-model="search" @focus="open = true" placeholder="Cari course..."
                                   class="w-full rounded-xl border border-gray-300 px-4 py-2 text-sm focus:ring-2 focus:ring-primary-500">
                            <svg class="absolute right-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>

                        <div class="border border-gray-200 rounded-xl max-h-56 overflow-y-auto bg-white divide-y divide-gray-50">
                            @forelse($courses as $course)
                                <label x-show="!search || '{{ strtolower($course->title) }}'.includes(search.toLowerCase())"
                                       class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 cursor-pointer transition-colors">
                                    <input type="checkbox" name="course_ids[]" value="{{ $course->id }}"
                                           {{ in_array($course->id, old('course_ids', [])) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-primary-600 focus:ring-primary-500 shrink-0">
                                    <span class="text-sm text-gray-700 truncate">{{ $course->title }}</span>
                                </label>
                            @empty
                                <p class="text-sm text-gray-400 text-center py-4">Belum ada course yang dipublish.</p>
                            @endforelse
                        </div>
                        @error('course_ids') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Promo Code Bonus --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Promo Code Bonus</label>
                        <select name="promo_code_id" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                            <option value="">— Tidak ada promo —</option>
                            @foreach($promoCodes as $promo)
                                <option value="{{ $promo->id }}" {{ old('promo_code_id') == $promo->id ? 'selected' : '' }}>
                                    {{ $promo->code }} ({{ $promo->discount_type === 'percent' ? $promo->discount_value . '%' : rupiah($promo->discount_value) }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Promo code ini akan diberikan otomatis ke user yang berlangganan plan ini</p>
                        @error('promo_code_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center gap-6">
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            <label for="is_active" class="text-sm text-gray-700">Aktif</label>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="is_popular" id="is_popular" value="1" {{ old('is_popular') ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            <label for="is_popular" class="text-sm text-gray-700">Tandai Populer</label>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center gap-3">
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary-600 text-white text-sm font-medium hover:bg-primary-700">Simpan</button>
                    <a href="{{ route('admin.memberships.index') }}" class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@include('partials.tinymce')
