@extends('layouts.admin')

@section('title', 'Edit Foto Galeri')

@section('content')
<div class="p-6 max-w-2xl">
    <div class="mb-6">
        <a href="{{ route('admin.galleries.index') }}" class="text-sm text-blue-600 hover:underline">← Kembali ke Daftar</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Edit Foto Gallery</h1>
    </div>

    <form action="{{ route('admin.galleries.update', $gallery) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 space-y-6">
        @csrf
        @method('PUT')
        
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Judul / Caption Singkat</label>
            <input type="text" name="title" value="{{ old('title', $gallery->title) }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Contoh: Keseruan Workshop">
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Deskripsi Tambahan</label>
            <textarea name="content" rows="3" class="tinymce w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500" placeholder="Teks kecil di bawah judul kartu...">{{ old('content', $gallery->content) }}</textarea>
        </div>

        <x-image-upload 
            name="image" 
            :value="storageUrl($gallery->image)" 
            label="Foto Kegiatan" 
            info="1200 x 800 px (4:3)" 
            aspect="aspect-[4/3]"
        />

        <div class="flex items-center gap-6 pt-4">
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" {{ $gallery->is_active ? 'checked' : '' }} id="is_active" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <label for="is_active" class="text-sm font-bold text-gray-700">Aktifkan</label>
            </div>
            <div class="flex items-center gap-2">
                <label class="text-sm font-bold text-gray-700">Urutan:</label>
                <input type="number" name="order" value="{{ $gallery->order }}" class="w-20 px-3 py-1 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
        </div>

        <div class="pt-6 border-t">
            <button type="submit" class="w-full py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 shadow-lg transition">Perbarui Galeri</button>
        </div>
    </form>
</div>
@endsection

@include('partials.tinymce')
