@extends('layouts.admin')

@section('title', 'Edit Artikel')

@section('page-header')
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.posts.index') }}" class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-lg font-bold text-gray-900">Edit Artikel</h1>
    </div>
@endsection

@section('content')
<div class="max-w-4xl">
    <form action="{{ route('admin.posts.update', $post) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-6">
            {{-- Title --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Judul Artikel</label>
                <input type="text" name="title" value="{{ old('title', $post->title) }}" required
                       placeholder="Masukkan judul yang menarik..."
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                @error('title') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                {{-- Category --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Kategori</label>
                    <select name="category" required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        @foreach(['Tips & Trick', 'Tutorial', 'Berita', 'Inspirasi', 'Update Platform'] as $cat)
                            <option value="{{ $cat }}" {{ old('category', $post->category) === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Status Publikasi</label>
                    <select name="status" required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="published" {{ old('status', $post->status) === 'published' ? 'selected' : '' }}>Terbitkan</option>
                        <option value="draft" {{ old('status', $post->status) === 'draft' ? 'selected' : '' }}>Simpan sebagai Draft</option>
                    </select>
                </div>
            </div>

            {{-- Thumbnail --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Gambar Utama (Thumbnail)</label>
                <div class="flex items-center justify-center w-full">
                    <label class="flex flex-col items-center justify-center w-full h-48 border-2 border-gray-300 border-dashed rounded-2xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors overflow-hidden relative">
                        <div id="preview-container" class="flex flex-col items-center justify-center pt-5 pb-6 {{ $post->thumbnail ? 'hidden' : '' }}">
                            <svg class="w-8 h-8 mb-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            <p class="mb-2 text-sm text-gray-500 font-semibold">Klik untuk ganti gambar</p>
                        </div>
                        <img id="image-preview" src="{{ $post->thumbnail_url }}" class="absolute inset-0 w-full h-full object-cover {{ $post->thumbnail ? '' : 'hidden' }}">
                        <input type="file" name="thumbnail" class="hidden" accept="image/*" onchange="previewImage(this)">
                    </label>
                </div>
                @error('thumbnail') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Content --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Konten Artikel</label>
                <textarea name="content" rows="15" required
                          placeholder="Tuliskan isi artikelmu di sini..."
                          class="tinymce w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary-500 focus:border-transparent font-mono text-sm">{{ old('content', $post->content) }}</textarea>
                @error('content') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.posts.index') }}" 
               class="px-6 py-2.5 rounded-xl border border-gray-200 text-sm font-bold text-gray-600 hover:bg-gray-50 transition-colors">
                Batal
            </a>
            <button type="submit" 
                    class="px-8 py-2.5 rounded-xl bg-primary-600 text-white text-sm font-bold hover:bg-primary-700 transition-all shadow-lg shadow-primary-200">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

@include('partials.tinymce')

<script nonce="{{ $cspNonce ?? '' }}">
    function previewImage(input) {
        const preview = document.getElementById('image-preview');
        const container = document.getElementById('preview-container');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                container.classList.add('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
