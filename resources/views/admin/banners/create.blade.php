@extends('layouts.admin')

@section('title', 'Tambah Banner')

@section('page-header')
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.banners.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <span class="text-base font-semibold text-gray-900">Tambah Banner</span>
    </div>
@endsection

@section('content')
    <div class="max-w-xl">
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Judul <span class="text-red-500">*</span></label>
                        <input type="text" name="title" value="{{ old('title') }}" required
                               class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 @error('title') border-red-400 @enderror">
                        @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <x-image-upload 
                        name="image" 
                        label="Gambar Banner" 
                        info="1920x800px (Hero) / 1200x600px (Promo)" 
                        aspect="aspect-video"
                        :required="true"
                    />

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Link URL</label>
                        <input type="url" name="link" value="{{ old('link') }}" placeholder="https://..."
                               class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 @error('link') border-red-400 @enderror">
                        @error('link') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Posisi</label>
                            <select name="position" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                                <option value="hero" {{ old('position') === 'hero' ? 'selected' : '' }}>Banner Utama Atas (Hero)</option>
                                <option value="promo" {{ old('position') === 'promo' ? 'selected' : '' }}>Banner Promo & Bundle</option>
                                <option value="sidebar" {{ old('position') === 'sidebar' ? 'selected' : '' }}>Sidebar</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
                            <input type="number" name="order" value="{{ old('order', 0) }}" min="0"
                                   class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <label for="is_active" class="text-sm text-gray-700">Aktif</label>
                    </div>
                </div>

                <div class="mt-6 flex items-center gap-3">
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary-600 text-white text-sm font-medium hover:bg-primary-700">Simpan</button>
                    <a href="{{ route('admin.banners.index') }}" class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
