@extends('layouts.admin')

@section('title', 'Edit Kampus Offline')

@section('content')
<div class="p-6 max-w-4xl">
    <div class="mb-6">
        <a href="{{ route('admin.campuses.index') }}" class="text-sm text-blue-600 hover:underline">← Kembali ke Daftar</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Edit Kampus: {{ $campus->name }}</h1>
    </div>

    <form action="{{ route('admin.campuses.update', $campus) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 space-y-6">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Nama Kampus</label>
                <input type="text" name="name" value="{{ old('name', $campus->name) }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Contoh: Jakarta Sudirman Campus">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Tagline</label>
                <input type="text" name="tagline" value="{{ old('tagline', $campus->tagline) }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Contoh: Urban Innovation Hub">
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Deskripsi Singkat</label>
            <textarea name="description" rows="3" class="tinymce w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">{{ old('description', $campus->description) }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Alamat Lengkap</label>
            <input type="text" name="address" value="{{ old('address', $campus->address) }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Jl. Jend. Sudirman...">
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Link Google Maps (untuk Petunjuk Arah)</label>
            <input type="text" name="map_link" value="{{ old('map_link', $campus->map_link) }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="https://maps.google.com/...">
        </div>

        <div x-data="{ features: {{ json_encode($campus->features ?? ['']) }} }">
            <label class="block text-sm font-bold text-gray-700 mb-2">Fasilitas Unggulan</label>
            <template x-for="(f, i) in features" :key="i">
                <div class="flex gap-2 mb-2">
                    <input type="text" name="features[]" x-model="features[i]" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Contoh: High-Speed WiFi" required>
                    <button type="button" @click="features.splice(i, 1)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg">✕</button>
                </div>
            </template>
            <button type="button" @click="features.push('')" class="text-sm text-blue-600 font-bold">+ Tambah Fasilitas</button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4">
            <x-image-upload 
                name="image" 
                :value="storageUrl($campus->image)" 
                label="Foto Kampus" 
                info="800 x 600 px (4:3)" 
                aspect="aspect-[4/3]"
            />
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Urutan Tampilan</label>
                <input type="number" name="order" value="{{ $campus->order }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
        </div>

        <div class="flex items-center gap-6 pt-6">
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" {{ $campus->is_active ? 'checked' : '' }} id="is_active" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <label for="is_active" class="text-sm font-bold text-gray-700">Aktif & Tampilkan di Beranda</label>
            </div>
        </div>

        <div class="pt-6 border-t">
            <button type="submit" class="px-8 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 shadow-lg transition">Perbarui Kampus</button>
        </div>
    </form>
</div>
@endsection

@include('partials.tinymce')
