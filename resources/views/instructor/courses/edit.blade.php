@extends('layouts.instructor')

@section('title', 'Edit Kursus — ' . $course->title)

@section('page-header')
    <div class="flex items-center gap-3">
        <a href="{{ route('instructor.courses.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-lg font-bold text-gray-900">Edit Kursus</h1>
            <p class="text-sm text-gray-500 truncate max-w-xs">{{ $course->title }}</p>
        </div>
    </div>
@endsection

@section('content')
<form method="POST" action="{{ route('instructor.courses.update', $course->id) }}" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @method('PUT')

    {{-- Quick stats --}}
    <div class="grid grid-cols-3 gap-3">
        <div class="bg-white rounded-xl border border-gray-200 p-3 text-center">
            <p class="text-lg font-bold text-gray-900">{{ $course->sections_count }}</p>
            <p class="text-xs text-gray-500">Section</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-3 text-center">
            <p class="text-lg font-bold text-gray-900">{{ $course->enrollments_count }}</p>
            <p class="text-xs text-gray-500">Siswa</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-3 text-center">
            <p class="text-lg font-bold text-gray-900">{{ $course->reviews_count }}</p>
            <p class="text-xs text-gray-500">Review</p>
        </div>
    </div>

    {{-- Navigation tabs --}}
    <div class="flex gap-2 border-b border-gray-200 pb-0">
        <span class="px-4 py-2.5 text-sm font-semibold text-primary-600 border-b-2 border-primary-600">Detail Kursus</span>
        <a href="{{ route('instructor.courses.lessons', $course->id) }}"
           class="px-4 py-2.5 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:border-gray-300">
            Kelola Lesson
        </a>
        <a href="{{ route('instructor.courses.quizzes.index', $course->id) }}"
           class="px-4 py-2.5 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:border-gray-300">
            Pretest & Posttest
        </a>
        <a href="{{ route('instructor.courses.variants.index', $course->id) }}"
           class="px-4 py-2.5 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:border-gray-300">
            Varian Delivery
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Main Content ─────────────────────────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-6">

            <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-5">
                <h2 class="text-base font-bold text-gray-900">Informasi Dasar</h2>

                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul Kursus <span class="text-red-500">*</span></label>
                    <input type="text" id="title" name="title" value="{{ old('title', $course->title) }}" required
                           class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('title') border-red-500 @enderror">
                    <p class="text-xs text-gray-400 mt-1">Slug: <code class="bg-gray-100 px-1 rounded">{{ $course->slug }}</code></p>
                    @error('title')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi <span class="text-red-500">*</span></label>
                    <textarea id="description" name="description" rows="8" required
                              class="tinymce w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('description') border-red-500 @enderror">{{ old('description', $course->description) }}</textarea>
                    @error('description')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <x-image-upload 
                    name="thumbnail" 
                    :value="$course->thumbnail_url" 
                    label="Thumbnail Kursus" 
                    info="1280 x 720 px (16:9)" 
                    aspect="aspect-video"
                />
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-5">
                <h2 class="text-base font-bold text-gray-900">SEO (Opsional)</h2>
                <div>
                    <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
                    <input type="text" id="meta_title" name="meta_title" value="{{ old('meta_title', $course->meta_title) }}"
                           class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                    <textarea id="meta_description" name="meta_description" rows="2"
                              class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('meta_description', $course->meta_description) }}</textarea>
                </div>
            </div>
        </div>

        {{-- ── Sidebar ──────────────────────────────────────────────────────── --}}
        <div class="space-y-6">

            <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-5">
                <h2 class="text-base font-bold text-gray-900">Publikasi</h2>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="status" name="status" required
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="draft" {{ old('status', $course->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status', $course->status) === 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                </div>

                <div>
                    <label for="level" class="block text-sm font-medium text-gray-700 mb-1">Level</label>
                    <select id="level" name="level" required
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="beginner" {{ old('level', $course->level) === 'beginner' ? 'selected' : '' }}>Pemula</option>
                        <option value="intermediate" {{ old('level', $course->level) === 'intermediate' ? 'selected' : '' }}>Menengah</option>
                        <option value="advanced" {{ old('level', $course->level) === 'advanced' ? 'selected' : '' }}>Mahir</option>
                    </select>
                </div>

                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <select id="category_id" name="category_id"
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $course->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @foreach($cat->children as $child)
                                <option value="{{ $child->id }}" {{ old('category_id', $course->category_id) == $child->id ? 'selected' : '' }}>&nbsp;&nbsp;└ {{ $child->name }}</option>
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
                            <option value="{{ $inst->id }}" {{ old('institution_id', $course->institution_id) == $inst->id ? 'selected' : '' }}>{{ $inst->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-[10px] text-gray-400 mt-1 italic">Kosongkan jika Anda instruktur umum / independen.</p>
                </div>

                <div>
                    <label for="language" class="block text-sm font-medium text-gray-700 mb-1">Bahasa</label>
                    <input type="text" id="language" name="language" value="{{ old('language', $course->language) }}"
                           class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-5">
                <h2 class="text-base font-bold text-gray-900">Harga</h2>

                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp)</label>
                    <input type="number" id="price" name="price" value="{{ old('price', $course->price) }}" min="0" required
                           class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    @error('price')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="discount_price" class="block text-sm font-medium text-gray-700 mb-1">Harga Diskon (Rp)</label>
                    <input type="number" id="discount_price" name="discount_price" value="{{ old('discount_price', $course->discount_price) }}" min="0"
                           class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    @error('discount_price')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                        class="flex-1 px-5 py-3 rounded-xl bg-primary-600 text-white text-sm font-semibold hover:bg-primary-700 transition-colors text-center">
                    Simpan Perubahan
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
