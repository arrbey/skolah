@extends('layouts.admin')

@section('title', 'Tambah Bundle Baru')

@section('content')
<form action="{{ route('admin.bundles.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-5">
                <h2 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3">Informasi Utama</h2>
                
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Judul Bundle <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('title') border-red-500 @enderror"
                           placeholder="Contoh: Paket Mahir Laravel & Vue.js">
                    @error('title') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Deskripsi</label>
                    <textarea name="description" rows="5" 
                              class="tinymce w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Pilih Kursus <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-2 max-h-[400px] overflow-y-auto p-3 border border-slate-100 rounded-xl bg-slate-50">
                        @foreach($courses as $course)
                            <label class="relative flex items-center gap-3 p-3 bg-white border border-slate-200 rounded-xl cursor-pointer hover:border-blue-300 hover:bg-blue-50 transition-all group">
                                <input type="checkbox" name="course_ids[]" value="{{ $course->id }}" 
                                       class="w-5 h-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                       {{ is_array(old('course_ids')) && in_array($course->id, old('course_ids')) ? 'checked' : '' }}>
                                <div class="flex items-center gap-3">
                                    <img src="{{ $course->thumbnail_url }}" class="w-10 h-10 rounded object-cover">
                                    <span class="text-sm font-bold text-slate-700 group-hover:text-blue-700 truncate">{{ $course->title }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('course_ids') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-5">
                <h2 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3">Harga & Status</h2>
                
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Status</label>
                    <select name="status" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Instruktur (Opsional)</label>
                    <select name="instructor_id" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Administrator (Default) --</option>
                        @foreach($instructors as $inst)
                            <option value="{{ $inst->id }}" {{ old('instructor_id') == $inst->id ? 'selected' : '' }}>{{ $inst->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Harga Normal (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="price" value="{{ old('price', 0) }}" required
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Harga Diskon (Rp)</label>
                    <input type="number" name="discount_price" value="{{ old('discount_price') }}"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                    <p class="text-[10px] text-slate-400 mt-1 italic">Biarkan kosong jika tidak ada diskon.</p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-5">
                <h2 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3">Media</h2>
                <x-image-upload name="thumbnail" label="Thumbnail Bundle" info="Rasio 16:9 disarankan" />
            </div>

            <div class="flex flex-col gap-3 pt-4">
                <button type="submit" class="w-full py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-all shadow-md shadow-blue-600/20">
                    Simpan Bundle
                </button>
                <a href="{{ route('admin.bundles.index') }}" class="w-full py-3 bg-slate-100 text-slate-600 font-bold rounded-xl hover:bg-slate-200 transition-all text-center">
                    Batal
                </a>
            </div>
        </div>
    </div>
</form>
@endsection

@include('partials.tinymce')
