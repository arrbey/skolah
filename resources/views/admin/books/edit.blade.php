@extends('layouts.admin')

@section('title', 'Edit Buku: ' . $book->title)

@section('page-header')
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.books.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <span class="text-base font-semibold text-gray-900">Edit Buku</span>
    </div>
@endsection

@section('content')
<form action="{{ route('admin.books.update', $book) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="grid lg:grid-cols-3 gap-6">

        {{-- Kolom Kiri: Info Utama --}}
        <div class="lg:col-span-2 space-y-5">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-5">
                <h3 class="font-semibold text-gray-900">Informasi Buku</h3>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Judul Buku <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $book->title) }}" required
                           class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 @error('title') border-red-400 @enderror">
                    @error('title')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Penulis <span class="text-red-500">*</span></label>
                        <input type="text" name="author" value="{{ old('author', $book->author) }}" required
                               class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 @error('author') border-red-400 @enderror">
                        @error('author')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Penerbit</label>
                        <input type="text" name="publisher" value="{{ old('publisher', $book->publisher) }}"
                               class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">ISBN</label>
                        <input type="text" name="isbn" value="{{ old('isbn', $book->isbn) }}"
                               class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Jumlah Halaman</label>
                        <input type="number" name="pages" value="{{ old('pages', $book->pages) }}" min="1"
                               class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi</label>
                    <textarea name="description" rows="5" class="tinymce w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">{{ old('description', $book->description) }}</textarea>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Instruktur / Pemilik</label>
                        <select name="instructor_id" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                            <option value="">— Tanpa Instruktur —</option>
                            @foreach($instructors as $ins)
                                <option value="{{ $ins->id }}" {{ old('instructor_id', $book->instructor_id) == $ins->id ? 'selected' : '' }}>{{ $ins->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Lembaga</label>
                        <select name="institution_id" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                            <option value="">— Umum / Tanpa Lembaga —</option>
                            @foreach($institutions as $inst)
                                <option value="{{ $inst->id }}" {{ old('institution_id', $book->institution_id) == $inst->id ? 'selected' : '' }}>{{ $inst->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Harga & Tipe --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-5">
                <h3 class="font-semibold text-gray-900">Harga & Tipe</h3>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Harga Normal (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" name="price" value="{{ old('price', $book->price) }}" min="0" required
                               class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 @error('price') border-red-400 @enderror">
                        @error('price')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Harga Diskon (Rp)</label>
                        <input type="number" name="discount_price" value="{{ old('discount_price', $book->discount_price) }}" min="0"
                               class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                    </div>
                </div>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipe Buku <span class="text-red-500">*</span></label>
                        <select name="type" required class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                            <option value="digital"  {{ old('type', $book->type)==='digital'  ? 'selected' : '' }}>Digital (PDF/eBook)</option>
                            <option value="physical" {{ old('type', $book->type)==='physical' ? 'selected' : '' }}>Fisik (Dikirim)</option>
                            <option value="both"     {{ old('type', $book->type)==='both'     ? 'selected' : '' }}>Keduanya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Stok</label>
                        <input type="number" name="stock" value="{{ old('stock', $book->stock) }}" min="0"
                               class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                    </div>
                </div>
            </div>

            {{-- SEO --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-4">
                <h3 class="font-semibold text-gray-900">SEO</h3>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Meta Title</label>
                    <input type="text" name="meta_title" value="{{ old('meta_title', $book->meta_title) }}" maxlength="255"
                           class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Meta Description</label>
                    <textarea name="meta_description" rows="2" maxlength="500" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">{{ old('meta_description', $book->meta_description) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan --}}
        <div class="space-y-5">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 space-y-4">
                <h3 class="font-semibold text-gray-900 text-sm">Status Publikasi</h3>
                <select name="status" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                    <option value="draft"     {{ old('status', $book->status)==='draft'     ? 'selected':'' }}>Draft</option>
                    <option value="published" {{ old('status', $book->status)==='published' ? 'selected':'' }}>Published</option>
                </select>
                <button type="submit" class="w-full py-2.5 rounded-xl bg-primary-600 text-white text-sm font-semibold hover:bg-primary-700 transition-colors">
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.books.index') }}" class="block text-center text-sm text-gray-500 hover:text-gray-700">Batal</a>
            </div>

            {{-- Current Cover --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 space-y-3">
                <x-image-upload 
                    name="cover_image" 
                    :value="storageUrl($book->cover_image)" 
                    label="Cover Buku" 
                    info="600 x 900 px (Portait 2:3)" 
                    aspect="aspect-[2/3]"
                />
            </div>

            {{-- Current File --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 space-y-3">
                <h3 class="font-semibold text-gray-900 text-sm">File Buku Digital</h3>
                @if($book->file_path)
                    <div class="flex items-center gap-2 p-3 bg-green-50 rounded-xl">
                        <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span class="text-xs text-green-700 truncate">File PDF sudah ada</span>
                    </div>
                    <p class="text-xs text-gray-400">Upload baru untuk mengganti file</p>
                @else
                    <p class="text-xs text-gray-400">Belum ada file digital. Upload PDF (maks 50MB).</p>
                @endif
                <input type="file" name="file_path" accept=".pdf"
                       class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-gray-50 file:text-gray-700 file:text-xs file:font-medium hover:file:bg-gray-100 cursor-pointer">
                @error('file_path')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>
</form>
@endsection

@include('partials.tinymce')
