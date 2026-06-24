@extends('layouts.admin')

@section('title', 'Buat Kursus Baru')

@section('page-header')
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.courses.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-lg font-bold text-gray-900">Buat Kursus Baru</h1>
            <p class="text-sm text-gray-500">Isi form di bawah untuk membuat kursus dan menugaskan instruktur</p>
        </div>
    </div>
@endsection

@section('content')
<form method="POST" action="{{ route('admin.courses.store') }}" enctype="multipart/form-data" class="space-y-6">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Main Content ─────────────────────────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Basic Info --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-5">
                <h2 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3">Informasi Dasar</h2>

                {{-- Title --}}
                <div>
                    <label for="title" class="block text-sm font-bold text-gray-700 mb-1">Judul Kursus <span class="text-red-500">*</span></label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" required
                           placeholder="Contoh: Belajar Laravel dari Nol"
                           class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('title') border-red-500 @enderror">
                    @error('title')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-bold text-gray-700 mb-1">Deskripsi <span class="text-red-500">*</span></label>
                    <textarea id="description" name="description" rows="8" required
                              placeholder="Jelaskan apa yang akan dipelajari siswa..."
                              class="tinymce w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Thumbnail --}}
                <x-image-upload 
                    name="thumbnail" 
                    label="Thumbnail Kursus" 
                    info="1280 x 720 px (16:9)" 
                    aspect="aspect-video"
                    :required="true"
                />

                {{-- Trailer YouTube --}}
                <div class="pt-4 border-t border-gray-100">
                    <label for="trailer_url" class="block text-sm font-bold text-gray-700 mb-1">Link Trailer YouTube (Opsional)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/></svg>
                        </div>
                        <input type="url" id="trailer_url" name="trailer_url" value="{{ old('trailer_url') }}"
                               placeholder="https://www.youtube.com/watch?v=..."
                               class="w-full rounded-xl border border-gray-300 pl-10 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('trailer_url') border-red-500 @enderror">
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1 italic">Video ini akan muncul sebagai preview sebelum siswa membeli kursus.</p>
                    @error('trailer_url')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- SEO --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-5">
                <h2 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3">SEO (Opsional)</h2>

                <div>
                    <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
                    <input type="text" id="meta_title" name="meta_title" value="{{ old('meta_title') }}"
                           placeholder="Judul untuk mesin pencari"
                           class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                    <textarea id="meta_description" name="meta_description" rows="2"
                              placeholder="Deskripsi singkat untuk mesin pencari (maks 160 karakter)"
                              class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('meta_description') }}</textarea>
                </div>
            </div>
        </div>

        {{-- ── Sidebar ──────────────────────────────────────────────────────── --}}
        <div class="space-y-6">

            {{-- Assignment --}}
            <div class="bg-primary-50 rounded-2xl border border-primary-100 p-6 space-y-5">
                <h2 class="text-base font-bold text-primary-900 border-b border-primary-200 pb-3">Penugasan & Lembaga</h2>

                <div>
                    <label for="institution_id" class="block text-sm font-bold text-primary-700 mb-1">Pilih Lembaga</label>
                    <select id="institution_id" name="institution_id"
                            class="w-full rounded-xl border-primary-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">-- Umum / Tanpa Lembaga --</option>
                        @foreach($institutions as $inst)
                            <option value="{{ $inst->id }}" {{ old('institution_id') == $inst->id ? 'selected' : '' }}>{{ $inst->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="instructor_id" class="block text-sm font-bold text-primary-700 mb-1">Instruktur Pengajar</label>
                    <select id="instructor_id" name="instructor_id"
                            class="w-full rounded-xl border-primary-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">-- Administrator (Default) --</option>
                        @foreach($instructors as $inst)
                            <option value="{{ $inst->id }}" {{ old('instructor_id') == $inst->id ? 'selected' : '' }}>{{ $inst->name }}</option>
                        @endforeach
                    </select>
                    @error('instructor_id')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Publish --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-5">
                <h2 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3">Pengaturan</h2>

                <div>
                    <label for="status" class="block text-sm font-bold text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select id="status" name="status" required
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                </div>

                <div>
                    <label for="category_id" class="block text-sm font-bold text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                    <select id="category_id" name="category_id" required
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @foreach($cat->children as $child)
                                <option value="{{ $child->id }}" {{ old('category_id') == $child->id ? 'selected' : '' }}>&nbsp;&nbsp;└ {{ $child->name }}</option>
                            @endforeach
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="level" class="block text-sm font-bold text-gray-700 mb-1">Level <span class="text-red-500">*</span></label>
                    <select id="level" name="level" required
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="beginner" {{ old('level', 'beginner') === 'beginner' ? 'selected' : '' }}>Pemula</option>
                        <option value="intermediate" {{ old('level') === 'intermediate' ? 'selected' : '' }}>Menengah</option>
                        <option value="advanced" {{ old('level') === 'advanced' ? 'selected' : '' }}>Mahir</option>
                    </select>
                </div>
            </div>

            {{-- Pricing --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-5">
                <h2 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3">Harga</h2>

                <div>
                    <label for="price" class="block text-sm font-bold text-gray-700 mb-1">Harga (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" id="price" name="price" value="{{ old('price', 0) }}" min="0" required
                           class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('price') border-red-500 @enderror">
                    <p class="text-[10px] text-gray-400 mt-1 italic">Isi 0 untuk kursus gratis.</p>
                </div>

                <div>
                    <label for="discount_price" class="block text-sm font-bold text-gray-700 mb-1">Harga Diskon (Rp)</label>
                    <input type="number" id="discount_price" name="discount_price" value="{{ old('discount_price') }}" min="0"
                           class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex flex-col gap-3 pt-4">
                <button type="submit"
                        class="w-full px-5 py-3 rounded-xl bg-primary-600 text-white text-sm font-bold hover:bg-primary-700 transition-all shadow-md">
                    Simpan Kursus
                </button>
                <a href="{{ route('admin.courses.index') }}"
                   class="w-full px-5 py-3 rounded-xl bg-white border border-gray-200 text-gray-600 text-sm font-bold hover:bg-gray-50 transition-all text-center">
                    Batal
                </a>
            </div>
        </div>
    </div>
</form>
@endsection

@include('partials.tinymce')
