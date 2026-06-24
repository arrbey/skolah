@extends('layouts.admin')

@section('title', 'Tambah Lembaga')

@section('content')
<div class="max-w-3xl">
    <div class="mb-6">
        <a href="{{ route('admin.institutions.index') }}" class="text-sm text-primary-600 hover:font-bold transition-all">← Kembali ke Daftar</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Tambah Lembaga Baru</h1>
        <p class="text-sm text-gray-500">Buat entitas lembaga baru untuk mengelompokkan kursus dan bootcamp.</p>
    </div>

    <form action="{{ route('admin.institutions.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-8 space-y-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Nama Lembaga <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Sekolah Ekspor"
                       class="w-full rounded-xl border-gray-200 focus:border-primary-500 focus:ring-primary-500 py-3 px-4">
                @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <x-image-upload 
                name="logo" 
                label="Logo Lembaga" 
                info="Format Square (1:1) direkomendasikan. Maks 2MB." 
                aspect="aspect-square w-32"
            />

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Deskripsi Singkat</label>
                <textarea name="description" rows="4" placeholder="Jelaskan tentang lembaga ini..."
                          class="w-full rounded-xl border-gray-200 focus:border-primary-500 focus:ring-primary-500 py-3 px-4">{{ old('description') }}</textarea>
                @error('description') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <div class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                    <label for="is_active" class="ml-3 text-sm font-bold text-gray-700">Aktifkan Lembaga</label>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="px-8 py-3 bg-primary-600 text-white text-sm font-bold rounded-xl hover:bg-primary-700 transition-all shadow-md">
                Simpan Lembaga
            </button>
            <a href="{{ route('admin.institutions.index') }}" class="px-8 py-3 bg-white border border-gray-200 text-gray-600 text-sm font-bold rounded-xl hover:bg-gray-50 transition-all">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
