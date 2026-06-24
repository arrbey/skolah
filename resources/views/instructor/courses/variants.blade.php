@extends('layouts.instructor')

@section('title', 'Kelola Varian — ' . $course->title)

@section('page-header')
    <div class="flex items-center gap-3">
        <a href="{{ route('instructor.courses.edit', $course->id) }}" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-lg font-bold text-gray-900">Kelola Varian Kursus</h1>
            <p class="text-sm text-gray-500 truncate max-w-xs">{{ $course->title }}</p>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Navigation tabs --}}
    <div class="flex gap-2 border-b border-gray-200 pb-0">
        <a href="{{ route('instructor.courses.edit', $course->id) }}"
           class="px-4 py-2.5 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:border-gray-300">
            Detail Kursus
        </a>
        <a href="{{ route('instructor.courses.lessons', $course->id) }}"
           class="px-4 py-2.5 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:border-gray-300">
            Kelola Lesson
        </a>
        <a href="{{ route('instructor.courses.quizzes.index', $course->id) }}"
           class="px-4 py-2.5 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:border-gray-300">
            Pretest & Posttest
        </a>
        <span class="px-4 py-2.5 text-sm font-semibold text-primary-600 border-b-2 border-primary-600">
            Varian Delivery
        </span>
    </div>

    {{-- Info --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
        <div class="flex gap-3">
            <span class="text-blue-500 text-lg">💡</span>
            <div class="text-sm text-blue-800">
                <p class="font-semibold mb-1">Apa itu Varian?</p>
                <p>Varian memungkinkan 1 kursus memiliki beberapa opsi pengiriman: <strong>Online</strong>, <strong>Offline (tatap muka)</strong>, atau <strong>Hybrid</strong>, masing-masing dengan harga, jadwal, dan lokasi berbeda.</p>
                <p class="mt-1">Jika kursus tidak memiliki varian, harga default dari detail kursus yang digunakan.</p>
            </div>
        </div>
    </div>

    {{-- Existing Variants --}}
    @if($variants->isNotEmpty())
    <div class="space-y-4">
        <h2 class="text-base font-bold text-gray-900">Varian Aktif ({{ $variants->count() }})</h2>

        @foreach($variants as $variant)
        <div class="bg-white rounded-2xl border border-gray-200 p-5" x-data="{ editing: false }">
            {{-- Display mode --}}
            <div x-show="!editing">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-sm font-bold text-gray-900">{{ $variant->display_label }}</span>
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium
                                {{ $variant->delivery_type === 'online' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $variant->delivery_type === 'offline' ? 'bg-amber-100 text-amber-700' : '' }}
                                {{ $variant->delivery_type === 'hybrid' ? 'bg-purple-100 text-purple-700' : '' }}
                            ">{{ ucfirst($variant->delivery_type) }}</span>
                            @if(!$variant->is_active)
                                <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">Non-aktif</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-3 text-sm text-gray-600">
                            @if($variant->has_discount)
                                <span class="font-bold" style="color:#6C63FF">{{ $variant->effective_price_formatted }}</span>
                                <span class="line-through text-gray-400">{{ $variant->price_formatted }}</span>
                                <span class="text-xs text-red-500 font-semibold">-{{ $variant->discount_percent }}%</span>
                            @else
                                <span class="font-bold" style="color:#6C63FF">{{ $variant->price_formatted }}</span>
                            @endif
                        </div>
                        @if($variant->schedule_formatted)
                            <p class="text-xs text-gray-500 mt-1">📅 {{ $variant->schedule_formatted }}</p>
                        @endif
                        @if($variant->location)
                            <p class="text-xs text-gray-500">📍 {{ $variant->location }}</p>
                        @endif
                        @if($variant->platform)
                            <p class="text-xs text-gray-500">💻 {{ $variant->platform }}</p>
                        @endif
                        <p class="text-xs text-gray-400 mt-1">
                            👥 {{ $variant->total_enrolled }} terdaftar
                            @if($variant->max_participants > 0)
                                / {{ $variant->max_participants }} maks
                            @else
                                (unlimited)
                            @endif
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <button @click="editing = true" class="text-xs bg-gray-100 text-gray-600 hover:bg-gray-200 px-3 py-1.5 rounded-lg transition-colors">✏️ Edit</button>
                        <form action="{{ route('instructor.courses.variants.destroy', [$course->id, $variant->id]) }}" method="POST"
                              onsubmit="return confirm('Hapus varian ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs bg-red-50 text-red-600 hover:bg-red-100 px-3 py-1.5 rounded-lg transition-colors">🗑 Hapus</button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Edit mode --}}
            <div x-show="editing" x-cloak>
                <form action="{{ route('instructor.courses.variants.update', [$course->id, $variant->id]) }}" method="POST" class="space-y-4">
                    @csrf @method('PUT')
                    <h3 class="text-sm font-bold text-gray-700">Edit Varian</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Tipe Delivery *</label>
                            <select name="delivery_type" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                <option value="online" {{ $variant->delivery_type === 'online' ? 'selected' : '' }}>🎥 Online</option>
                                <option value="offline" {{ $variant->delivery_type === 'offline' ? 'selected' : '' }}>🏢 Offline</option>
                                <option value="hybrid" {{ $variant->delivery_type === 'hybrid' ? 'selected' : '' }}>🔀 Hybrid</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Label (opsional)</label>
                            <input type="text" name="label" value="{{ $variant->label }}" placeholder="Contoh: Kelas Jakarta Mei 2026" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Harga (Rp) *</label>
                            <input type="number" name="price" value="{{ $variant->price }}" min="0" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Harga Diskon (Rp)</label>
                            <input type="number" name="discount_price" value="{{ $variant->discount_price }}" min="0" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Jadwal Mulai</label>
                            <input type="datetime-local" name="schedule_start" value="{{ $variant->schedule_start?->format('Y-m-d\TH:i') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Jadwal Selesai</label>
                            <input type="datetime-local" name="schedule_end" value="{{ $variant->schedule_end?->format('Y-m-d\TH:i') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Lokasi</label>
                            <input type="text" name="location" value="{{ $variant->location }}" placeholder="Jakarta Selatan" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Platform</label>
                            <input type="text" name="platform" value="{{ $variant->platform }}" placeholder="Zoom / Google Meet" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Meeting Link</label>
                            <input type="url" name="meeting_link" value="{{ $variant->meeting_link }}" placeholder="https://zoom.us/..." class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Maks Peserta (0 = unlimited)</label>
                            <input type="number" name="max_participants" value="{{ $variant->max_participants }}" min="0" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Urutan</label>
                            <input type="number" name="sort_order" value="{{ $variant->sort_order }}" min="0" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" id="is_active_{{ $variant->id }}" value="1" {{ $variant->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <label for="is_active_{{ $variant->id }}" class="text-sm text-gray-700">Aktif</label>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-primary-600 text-white text-sm font-semibold rounded-lg hover:bg-primary-700 transition-colors">Simpan</button>
                        <button type="button" @click="editing = false" class="px-4 py-2 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">Batal</button>
                    </div>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-2xl p-8 text-center">
        <p class="text-gray-500 text-sm">Belum ada varian. Tambahkan varian pertama di bawah.</p>
    </div>
    @endif

    {{-- Add New Variant Form --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-6" x-data="{ open: {{ $variants->isEmpty() ? 'true' : 'false' }} }">
        <button @click="open = !open" class="flex items-center justify-between w-full text-left">
            <h2 class="text-base font-bold text-gray-900">➕ Tambah Varian Baru</h2>
            <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <form x-show="open" x-cloak x-transition
              action="{{ route('instructor.courses.variants.store', $course->id) }}" method="POST" class="mt-5 space-y-4">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tipe Delivery *</label>
                    <select name="delivery_type" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm @error('delivery_type') border-red-500 @enderror">
                        <option value="online" {{ old('delivery_type') === 'online' ? 'selected' : '' }}>🎥 Online</option>
                        <option value="offline" {{ old('delivery_type') === 'offline' ? 'selected' : '' }}>🏢 Offline</option>
                        <option value="hybrid" {{ old('delivery_type') === 'hybrid' ? 'selected' : '' }}>🔀 Hybrid</option>
                    </select>
                    @error('delivery_type') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Label (opsional)</label>
                    <input type="text" name="label" value="{{ old('label') }}" placeholder="Contoh: Kelas Jakarta Mei 2026" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm @error('label') border-red-500 @enderror">
                    @error('label') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Harga (Rp) *</label>
                    <input type="number" name="price" value="{{ old('price', $course->price) }}" min="0" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm @error('price') border-red-500 @enderror">
                    @error('price') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Harga Diskon (Rp)</label>
                    <input type="number" name="discount_price" value="{{ old('discount_price') }}" min="0" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm @error('discount_price') border-red-500 @enderror">
                    @error('discount_price') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Jadwal Mulai</label>
                    <input type="datetime-local" name="schedule_start" value="{{ old('schedule_start') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Jadwal Selesai</label>
                    <input type="datetime-local" name="schedule_end" value="{{ old('schedule_end') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Lokasi</label>
                    <input type="text" name="location" value="{{ old('location') }}" placeholder="Jakarta Selatan" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Platform</label>
                    <input type="text" name="platform" value="{{ old('platform') }}" placeholder="Zoom / Google Meet" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Meeting Link</label>
                    <input type="url" name="meeting_link" value="{{ old('meeting_link') }}" placeholder="https://zoom.us/..." class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Maks Peserta (0 = unlimited)</label>
                    <input type="number" name="max_participants" value="{{ old('max_participants', 0) }}" min="0" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Urutan</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="is_active_new" value="1" checked class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                <label for="is_active_new" class="text-sm text-gray-700">Aktif</label>
            </div>

            <button type="submit" class="px-5 py-2.5 bg-primary-600 text-white text-sm font-semibold rounded-xl hover:bg-primary-700 transition-colors">
                ➕ Tambah Varian
            </button>
        </form>
    </div>
</div>
@endsection
