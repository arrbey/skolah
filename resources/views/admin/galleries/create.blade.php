@extends('layouts.admin')

@section('title', 'Tambah Foto Galeri')

@section('content')
<div class="p-6 max-w-2xl">
    <div class="mb-6">
        <a href="{{ route('admin.galleries.index') }}" class="text-sm text-blue-600 hover:underline">← Kembali ke Daftar</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Tambah Foto Baru</h1>
    </div>

    <form action="{{ route('admin.galleries.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 space-y-6">
        @csrf

        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <ul class="list-disc list-inside text-sm text-red-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif
        
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Judul / Caption Singkat</label>
            <input type="text" name="title" value="{{ old('title') }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Contoh: Keseruan Workshop">
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Deskripsi Tambahan</label>
            <textarea name="content" rows="3" class="tinymce w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Teks kecil di bawah judul kartu..."></textarea>
        </div>

        <x-image-upload 
            name="image" 
            label="Foto Kegiatan" 
            info="1200 x 800 px (4:3)" 
            aspect="aspect-[4/3]"
            :required="true"
        />

        <div class="flex items-center gap-6 pt-4">
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" checked id="is_active" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <label for="is_active" class="text-sm font-bold text-gray-700">Aktifkan</label>
            </div>
            <div class="flex items-center gap-2">
                <label class="text-sm font-bold text-gray-700">Urutan:</label>
                <input type="number" name="order" value="0" class="w-20 px-3 py-1 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
        </div>

        <div class="pt-6 border-t">
            <button type="submit" class="w-full py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 shadow-lg transition">Simpan ke Galeri</button>
        </div>
    </form>
</div>
@endsection

@include('partials.tinymce')
