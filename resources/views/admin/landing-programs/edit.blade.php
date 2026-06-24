@extends('layouts.admin')

@section('title', 'Edit Program Unggulan')

@section('content')
<div class="p-6 max-w-4xl">
    <div class="mb-6">
        <a href="{{ route('admin.landing-programs.index') }}" class="text-sm text-blue-600 hover:underline">← Kembali ke Daftar</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Edit Program: {{ $program->title }}</h1>
    </div>

    <form action="{{ route('admin.landing-programs.update', $program) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 space-y-6">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Judul Program</label>
                <input type="text" name="title" value="{{ old('title', $program->title) }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Contoh: E-learning">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Subtitle / Tagline</label>
                <input type="text" name="subtitle" value="{{ old('subtitle', $program->subtitle) }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Contoh: Pelajari ratusan skill sekali bayar">
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Deskripsi Singkat</label>
            <textarea name="description" rows="3" class="tinymce w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">{{ old('description', $program->description) }}</textarea>
        </div>

        <div x-data="{ features: {{ json_encode($program->features ?? ['']) }} }">
            <label class="block text-sm font-bold text-gray-700 mb-2">Poin-poin Fitur</label>
            <template x-for="(f, i) in features" :key="i">
                <div class="flex gap-2 mb-2">
                    <input type="text" name="features[]" x-model="features[i]" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Poin fitur..." required>
                    <button type="button" @click="features.splice(i, 1)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg">✕</button>
                </div>
            </template>
            <button type="button" @click="features.push('')" class="text-sm text-blue-600 font-bold">+ Tambah Poin</button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Tombol Label</label>
                <input type="text" name="button_text" value="{{ old('button_text', $program->button_text) }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Tombol Link</label>
                <input type="text" name="button_link" value="{{ old('button_link', $program->button_link) }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4">
            <x-image-upload 
                name="image" 
                :value="storageUrl($program->image)" 
                label="Ilustrasi Program" 
                info="600 x 600 px (Square)" 
                aspect="aspect-square"
            />
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Posisi Ilustrasi</label>
                <select name="alignment" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="left" {{ $program->alignment == 'left' ? 'selected' : '' }}>Kiri (Teks di Kanan)</option>
                    <option value="right" {{ $program->alignment == 'right' ? 'selected' : '' }}>Kanan (Teks di Kiri)</option>
                </select>
            </div>
        </div>

        <div class="flex items-center gap-6 pt-6">
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" {{ $program->is_active ? 'checked' : '' }} id="is_active" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <label for="is_active" class="text-sm font-bold text-gray-700">Tampilkan Secara Publik</label>
            </div>
            <div class="flex items-center gap-2">
                <label class="text-sm font-bold text-gray-700">Urutan:</label>
                <input type="number" name="order" value="{{ $program->order }}" class="w-20 px-3 py-1 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
        </div>

        <div class="pt-6 border-t">
            <button type="submit" class="px-8 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 shadow-lg transition">Perbarui Program</button>
        </div>
    </form>
</div>
@endsection

@include('partials.tinymce')
