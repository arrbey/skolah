@extends('layouts.admin')

@section('title', 'Edit Banner')

@section('page-header')
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.banners.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <span class="text-base font-semibold text-gray-900">Edit Banner: {{ $banner->title }}</span>
    </div>
@endsection

@section('content')
    <div class="max-w-xl">
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Judul <span class="text-red-500">*</span></label>
                        <input type="text" name="title" value="{{ old('title', $banner->title) }}" required
                               class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 @error('title') border-red-400 @enderror">
                        @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <x-image-upload 
                        name="image" 
                        :value="$banner->image_url" 
                        label="Gambar Banner" 
                        info="{{ $banner->position === 'hero' ? '1920 x 800 px (Hero)' : '1200 x 600 px (Promo)' }}" 
                        aspect="{{ $banner->position === 'hero' ? 'aspect-[21/9]' : 'aspect-video' }}"
                    />

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Link URL</label>
                        <input type="url" name="link" value="{{ old('link', $banner->link) }}" placeholder="https://..."
                               class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 @error('link') border-red-400 @enderror">
                        @error('link') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Posisi</label>
                            <select name="position" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                                <option value="hero" {{ old('position', $banner->position) === 'hero' ? 'selected' : '' }}>Banner Utama Atas (Hero)</option>
                                <option value="promo" {{ old('position', $banner->position) === 'promo' ? 'selected' : '' }}>Banner Promo & Bundle</option>
                                <option value="sidebar" {{ old('position', $banner->position) === 'sidebar' ? 'selected' : '' }}>Sidebar</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
                            <input type="number" name="order" value="{{ old('order', $banner->order) }}" min="0"
                                   class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $banner->is_active) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <label for="is_active" class="text-sm text-gray-700">Aktif</label>
                    </div>
                </div>

                <div class="mt-6 flex items-center gap-3">
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary-600 text-white text-sm font-medium hover:bg-primary-700">Update</button>
                    <a href="{{ route('admin.banners.index') }}" class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
