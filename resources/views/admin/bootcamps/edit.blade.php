@extends('layouts.admin')

@section('title', 'Edit Bootcamp')

@section('page-header')
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.bootcamps.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-lg font-bold text-gray-900">Edit Bootcamp</h1>
            <p class="text-sm text-gray-500">{{ $bootcamp->title }}</p>
        </div>
    </div>
@endsection

@section('content')
<form method="POST" action="{{ route('admin.bootcamps.update', $bootcamp) }}" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Main Content ─────────────────────────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-6">

            <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-5">
                <h2 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3">Informasi Bootcamp</h2>

                <div>
                    <label for="title" class="block text-sm font-bold text-gray-700 mb-1">Judul <span class="text-red-500">*</span></label>
                    <input type="text" id="title" name="title" value="{{ old('title', $bootcamp->title) }}" required
                           class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label for="description" class="block text-sm font-bold text-gray-700 mb-1">Deskripsi <span class="text-red-500">*</span></label>
                    <textarea id="description" name="description" rows="6" required
                              class="tinymce w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('description', $bootcamp->description) }}</textarea>
                </div>

                <x-image-upload 
                    name="thumbnail" 
                    :value="$bootcamp->thumbnail_url"
                    label="Thumbnail Bootcamp" 
                    info="1280 x 720 px (16:9)" 
                    aspect="aspect-video"
                />
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="type" class="block text-sm font-bold text-gray-700 mb-1">Tipe <span class="text-red-500">*</span></label>
                        <select id="type" name="type" required
                                class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="online" {{ old('type', $bootcamp->type) === 'online' ? 'selected' : '' }}>Online</option>
                            <option value="offline" {{ old('type', $bootcamp->type) === 'offline' ? 'selected' : '' }}>Offline</option>
                        </select>
                    </div>
                    <div>
                        <label for="platform" class="block text-sm font-bold text-gray-700 mb-1">Platform / Lokasi</label>
                        <input type="text" id="platform" name="platform" value="{{ old('platform', $bootcamp->platform) }}"
                               class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="start_date" class="block text-sm font-bold text-gray-700 mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                        <input type="datetime-local" id="start_date" name="start_date" 
                               value="{{ old('start_date', $bootcamp->start_date?->format('Y-m-d\TH:i')) }}" required
                               class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-bold text-gray-700 mb-1">Tanggal Selesai <span class="text-red-500">*</span></label>
                        <input type="datetime-local" id="end_date" name="end_date" 
                               value="{{ old('end_date', $bootcamp->end_date?->format('Y-m-d\TH:i')) }}" required
                               class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-5">
                <h2 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3">SEO (Opsional)</h2>
                <div>
                    <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
                    <input type="text" id="meta_title" name="meta_title" value="{{ old('meta_title', $bootcamp->meta_title) }}"
                           class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                    <textarea id="meta_description" name="meta_description" rows="2"
                              class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('meta_description', $bootcamp->meta_description) }}</textarea>
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
                            <option value="{{ $inst->id }}" {{ old('institution_id', $bootcamp->institution_id) == $inst->id ? 'selected' : '' }}>{{ $inst->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="instructor_id" class="block text-sm font-bold text-primary-700 mb-1">Instruktur Pengajar</label>
                    <select id="instructor_id" name="instructor_id"
                            class="w-full rounded-xl border-primary-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        @foreach($instructors as $inst)
                            <option value="{{ $inst->id }}" {{ old('instructor_id', $bootcamp->instructor_id) == $inst->id ? 'selected' : '' }}>{{ $inst->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-5">
                <h2 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3">Pengaturan</h2>

                <div>
                    <label for="status" class="block text-sm font-bold text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select id="status" name="status" required
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="upcoming" {{ old('status', $bootcamp->status) === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                        <option value="ongoing" {{ old('status', $bootcamp->status) === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                        <option value="completed" {{ old('status', $bootcamp->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>

                <div>
                    <label for="max_participants" class="block text-sm font-bold text-gray-700 mb-1">Maks Peserta</label>
                    <input type="number" id="max_participants" name="max_participants" min="0"
                           value="{{ old('max_participants', $bootcamp->max_participants) }}" placeholder="0 = unlimited"
                           class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-5">
                <h2 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3">Harga</h2>
                <div>
                    <label for="price" class="block text-sm font-bold text-gray-700 mb-1">Harga (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" id="price" name="price" value="{{ old('price', $bootcamp->price) }}" min="0" required
                           class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label for="discount_price" class="block text-sm font-bold text-gray-700 mb-1">Harga Diskon (Rp)</label>
                    <input type="number" id="discount_price" name="discount_price" value="{{ old('discount_price', $bootcamp->discount_price) }}" min="0"
                           class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
            </div>

            <div class="flex flex-col gap-3">
                <button type="submit" class="w-full px-5 py-3 rounded-xl bg-primary-600 text-white text-sm font-bold hover:bg-primary-700 transition-colors shadow-md">
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.bootcamps.index') }}" class="w-full px-5 py-3 rounded-xl bg-white border border-gray-200 text-gray-600 text-sm font-bold hover:bg-gray-50 transition-colors text-center">
                    Batal
                </a>
            </div>
        </div>
    </div>
</form>
@endsection

@include('partials.tinymce')
