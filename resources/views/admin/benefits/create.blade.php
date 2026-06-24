@extends('layouts.admin')

@section('title', 'Tambah Benefit')

@section('page-header')
    <h1 class="text-lg font-bold text-gray-900">Tambah Benefit</h1>
@endsection

@section('content')
<div class="max-w-2xl">
    <form action="{{ route('admin.benefits.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-4">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Judul Benefit <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required placeholder="Contoh: 1.5 Juta+ Member"
                       class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500">
                @error('title') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Subtitle / Deskripsi Singkat</label>
                <input type="text" name="subtitle" value="{{ old('subtitle') }}" placeholder="Contoh: Belajar bersama setiap hari"
                       class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500">
                @error('subtitle') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Emoji Icon</label>
                    <input type="text" name="icon" value="{{ old('icon') }}" placeholder="👨‍🎓"
                           class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500">
                    <p class="mt-1 text-[10px] text-gray-400">Kosongkan jika ingin menggunakan gambar ilustrasi.</p>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Urutan</label>
                    <input type="number" name="order" value="{{ old('order') }}"
                           class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <x-image-upload 
                name="image" 
                label="Gambar Ilustrasi" 
                info="200 x 200 px (Square)" 
                aspect="aspect-square w-32"
            />

            <div class="flex items-center gap-3 pt-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}
                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <label for="is_active" class="text-sm font-medium text-gray-700">Tampilkan Benefit ini</label>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white text-sm font-bold rounded-xl hover:bg-blue-700 transition-all shadow-sm">
                Simpan Benefit
            </button>
            <a href="{{ route('admin.benefits.index') }}" class="px-6 py-2.5 bg-white border border-gray-200 text-gray-600 text-sm font-bold rounded-xl hover:bg-gray-50 transition-all">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
