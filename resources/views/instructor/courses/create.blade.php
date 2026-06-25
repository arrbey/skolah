@extends('layouts.instructor')

@section('title', 'Buat Kursus Baru')

@section('page-header')
    <div class="flex items-center gap-3">
        <a href="{{ route('instructor.courses.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-lg font-bold text-gray-900">Buat Kursus Baru</h1>
            <p class="text-sm text-gray-500">Isi form di bawah untuk membuat kursus</p>
        </div>
    </div>
@endsection

@section('content')
<form method="POST" action="{{ route('instructor.courses.store') }}" enctype="multipart/form-data" class="space-y-6" id="instructor-course-create-form">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Main Content ─────────────────────────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Basic Info --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-5">
                <h2 class="text-base font-bold text-gray-900">Informasi Dasar</h2>

                {{-- Title --}}
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul Kursus <span class="text-red-500">*</span></label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" required
                           placeholder="Contoh: Belajar Laravel dari Nol"
                           class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('title') border-red-500 @enderror">
                    @error('title')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi <span class="text-red-500">*</span></label>
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
            </div>

            {{-- SEO --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-5">
                <h2 class="text-base font-bold text-gray-900">SEO (Opsional)</h2>

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

            {{-- Publish --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-5">
                <h2 class="text-base font-bold text-gray-900">Publikasi</h2>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select id="status" name="status" required
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                </div>

                <div>
                    <label for="level" class="block text-sm font-medium text-gray-700 mb-1">Level <span class="text-red-500">*</span></label>
                    <select id="level" name="level" required
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="beginner" {{ old('level', 'beginner') === 'beginner' ? 'selected' : '' }}>Pemula</option>
                        <option value="intermediate" {{ old('level') === 'intermediate' ? 'selected' : '' }}>Menengah</option>
                        <option value="advanced" {{ old('level') === 'advanced' ? 'selected' : '' }}>Mahir</option>
                    </select>
                </div>

                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <select id="category_id" name="category_id"
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
                    <label for="institution_id" class="block text-sm font-medium text-gray-700 mb-1">Lembaga (Opsional)</label>
                    <select id="institution_id" name="institution_id"
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Umum (Tanpa Lembaga)</option>
                        @foreach($institutions as $inst)
                            <option value="{{ $inst->id }}" {{ old('institution_id') == $inst->id ? 'selected' : '' }}>{{ $inst->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-[10px] text-gray-400 mt-1 italic">Kosongkan jika Anda instruktur umum / independen.</p>
                </div>

                <div>
                    <label for="language" class="block text-sm font-medium text-gray-700 mb-1">Bahasa</label>
                    <input type="text" id="language" name="language" value="{{ old('language', 'Bahasa Indonesia') }}"
                           class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
            </div>

            {{-- Pricing --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-5">
                <h2 class="text-base font-bold text-gray-900">Harga</h2>

                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" id="price" name="price" value="{{ old('price', 0) }}" min="0" required
                           class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('price') border-red-500 @enderror">
                    @error('price')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-400 mt-1">Isi 0 untuk kursus gratis.</p>
                </div>

                <div>
                    <label for="discount_price" class="block text-sm font-medium text-gray-700 mb-1">Harga Diskon (Rp)</label>
                    <input type="number" id="discount_price" name="discount_price" value="{{ old('discount_price') }}" min="0"
                           class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    @error('discount_price')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-400 mt-1">Kosongkan jika tidak ada diskon.</p>
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex gap-3">
                <button type="submit"
                        id="instructor-course-submit"
                        class="flex-1 px-5 py-3 rounded-xl bg-primary-600 text-white text-sm font-semibold hover:bg-primary-700 transition-colors text-center">
                    Simpan Kursus
                </button>
                <a href="{{ route('instructor.courses.index') }}"
                   class="px-5 py-3 rounded-xl bg-gray-100 text-gray-700 text-sm font-medium hover:bg-gray-200 transition-colors text-center">
                    Batal
                </a>
            </div>
        </div>
    </div>
</form>
@endsection

@include('partials.tinymce')

@push('scripts')
<script>
    document.getElementById('instructor-course-create-form')?.addEventListener('submit', function () {
        const button = document.getElementById('instructor-course-submit');
        if (!button) return;

        button.disabled = true;
        button.classList.add('opacity-70', 'cursor-not-allowed');
        button.textContent = 'Menyimpan...';
    });
</script>
@endpush
