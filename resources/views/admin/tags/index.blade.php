@extends('layouts.admin')

@section('title', 'Manajemen Tag')

@section('page-header')
    <span class="text-base font-semibold text-gray-900">Manajemen Tag</span>
@endsection

@section('content')

@if(session('success'))
    <div class="mb-5 flex items-center gap-3 p-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl text-sm">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="mb-5 flex items-center gap-3 p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-sm">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        {{ session('error') }}
    </div>
@endif

<div class="grid lg:grid-cols-3 gap-6">

    {{-- Form Tambah Tag --}}
    <div class="lg:col-span-1 space-y-5">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <h3 class="font-semibold text-gray-900 text-sm mb-4">Tambah Tag Baru</h3>
            <form action="{{ route('admin.tags.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Nama Tag <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           placeholder="Contoh: Python, Web Design, UI/UX"
                           class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 @error('name') border-red-400 @enderror">
                    @error('name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-400 mt-1">Slug akan dibuat otomatis dari nama tag.</p>
                </div>
                <button type="submit" class="w-full py-2.5 rounded-xl bg-primary-600 text-white text-sm font-semibold hover:bg-primary-700 transition-colors">
                    Tambah Tag
                </button>
            </form>
        </div>

        {{-- Filter --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <h3 class="font-semibold text-gray-900 text-sm mb-3">Cari Tag</h3>
            <form method="GET">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama tag..."
                       class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 mb-3">
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 py-2 rounded-xl bg-gray-800 text-white text-sm font-medium hover:bg-gray-700 transition-colors">Cari</button>
                    @if(request('search'))
                        <a href="{{ route('admin.tags.index') }}" class="flex-1 py-2 rounded-xl bg-gray-100 text-gray-600 text-sm font-medium text-center hover:bg-gray-200 transition-colors">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Daftar Tag --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-900 text-sm">Semua Tag</h3>
                <span class="text-xs text-gray-400">{{ $tags->total() }} tag</span>
            </div>

            @if($tags->isEmpty())
                <div class="p-16 text-center">
                    <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    <p class="text-gray-400 text-sm">Belum ada tag.</p>
                </div>
            @else
                <div x-data="{ editing: null, editName: '' }">
                    <div class="divide-y divide-gray-100">
                        @foreach($tags as $tag)
                            <div class="flex items-center gap-3 px-5 py-3.5 hover:bg-gray-50 transition-colors">
                                {{-- Icon Tag --}}
                                <div class="w-8 h-8 rounded-lg bg-primary-50 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                </div>

                                {{-- Mode tampil --}}
                                <div class="flex-1 min-w-0" x-show="editing !== {{ $tag->id }}" x-cloak>
                                    <p class="text-sm font-medium text-gray-900">{{ $tag->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $tag->slug }} &bull; {{ $tag->courses_count }} kursus</p>
                                </div>

                                {{-- Mode edit inline --}}
                                <form action="{{ route('admin.tags.update', $tag) }}" method="POST" class="flex-1 flex gap-2" x-show="editing === {{ $tag->id }}" x-cloak>
                                    @csrf @method('PUT')
                                    <input type="text" name="name" :value="editName" required
                                           class="flex-1 rounded-xl border border-primary-300 px-3 py-1.5 text-sm focus:ring-2 focus:ring-primary-500">
                                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-primary-600 text-white text-xs font-medium hover:bg-primary-700">Simpan</button>
                                    <button type="button" @click="editing = null" class="px-3 py-1.5 rounded-xl bg-gray-100 text-gray-600 text-xs hover:bg-gray-200">Batal</button>
                                </form>

                                {{-- Tombol Aksi --}}
                                <div class="flex gap-1 flex-shrink-0" x-show="editing !== {{ $tag->id }}" x-cloak>
                                    <button type="button"
                                            @click="editing = {{ $tag->id }}; editName = '{{ addslashes($tag->name) }}'"
                                            class="p-1.5 rounded-xl text-gray-400 hover:text-primary-600 hover:bg-primary-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <form action="{{ route('admin.tags.destroy', $tag) }}" method="POST"
                                          onsubmit="return confirm('Hapus tag \'{{ addslashes($tag->name) }}\'? Tag akan dilepas dari {{ $tag->courses_count }} kursus.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-xl text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="px-5 py-4 border-t border-gray-100">
                    {{ $tags->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
