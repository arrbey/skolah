@extends('layouts.instructor')

@section('title', 'Kelola Lesson — ' . $course->title)

@push('head')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
@endpush

@section('page-header')
    <div class="flex items-center gap-3">
        <a href="{{ route('instructor.courses.edit', $course->id) }}" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-lg font-bold text-gray-900">Kelola Lesson</h1>
            <p class="text-sm text-gray-500 truncate max-w-xs">{{ $course->title }}</p>
        </div>
    </div>
@endsection

@section('content')
<div x-data="lessonManager()" class="space-y-6">

    {{-- Navigation tabs --}}
    <div class="flex gap-2 border-b border-gray-200 pb-0">
        <a href="{{ route('instructor.courses.edit', $course->id) }}"
           class="px-4 py-2.5 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:border-gray-300">
            Detail Kursus
        </a>
        <span class="px-4 py-2.5 text-sm font-semibold text-primary-600 border-b-2 border-primary-600">Kelola Lesson</span>
    </div>

    {{-- Stats bar --}}
    @php
        $totalLessonCount = 0;
        foreach ($course->sections as $_s) { $totalLessonCount += $_s->lessons->count(); }
    @endphp
    <div class="flex items-center gap-6 text-sm text-gray-500">
        <span>{{ $course->sections->count() }} Section</span>
        <span>{{ $totalLessonCount }} Lesson</span>
        <span>{{ $course->enrollments_count }} Siswa</span>
    </div>

    {{-- Add Section --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-4">
        <form method="POST" action="{{ route('instructor.courses.sections.store', $course->id) }}" class="flex gap-3">
            @csrf
            <input type="text" name="title" required placeholder="Nama section baru..."
                   class="flex-1 rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            <button type="submit"
                    class="px-5 py-2.5 rounded-xl bg-primary-600 text-white text-sm font-semibold hover:bg-primary-700 transition-colors whitespace-nowrap">
                + Tambah Section
            </button>
        </form>
    </div>

    {{-- Sections List --}}
    <div id="sections-container" class="space-y-4">
        @forelse($course->sections as $section)
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden section-item" data-section-id="{{ $section->id }}">
                {{-- Section Header --}}
                <div class="flex items-center gap-3 px-5 py-4 bg-gray-50 border-b border-gray-200">
                    <div class="cursor-grab section-handle text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/></svg>
                    </div>

                    <div class="flex-1 min-w-0" x-data="{ editing: false, title: '{{ addslashes($section->title) }}' }">
                        <span x-show="!editing" @dblclick="editing = true" class="font-semibold text-gray-900 cursor-pointer" title="Klik ganda untuk edit">
                            {{ $section->title }}
                        </span>
                        <form x-show="editing" x-cloak method="POST"
                              action="{{ route('instructor.courses.sections.update', [$course->id, $section->id]) }}"
                              class="flex gap-2" @submit="editing = false">
                            @csrf
                            @method('PUT')
                            <input type="text" name="title" x-model="title" required
                                   class="flex-1 rounded-lg border border-gray-300 px-3 py-1 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                   @keydown.escape="editing = false">
                            <button type="submit" class="px-3 py-1 rounded-lg bg-primary-600 text-white text-xs font-medium">Simpan</button>
                            <button type="button" @click="editing = false" class="px-3 py-1 rounded-lg bg-gray-100 text-gray-600 text-xs font-medium">Batal</button>
                        </form>
                    </div>

                    <span class="text-xs text-gray-500 shrink-0">{{ $section->lessons->count() }} lesson</span>

                    <form method="POST" action="{{ route('instructor.courses.sections.destroy', [$course->id, $section->id]) }}"
                          onsubmit="return confirm('Hapus section ini dan semua lesson di dalamnya?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-1.5 rounded-lg text-red-400 hover:text-red-600 hover:bg-red-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>

                {{-- Lessons --}}
                <div class="lessons-container divide-y divide-gray-100" data-section-id="{{ $section->id }}">
                    @foreach($section->lessons as $lesson)
                        <div class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50 transition-colors lesson-item" data-lesson-id="{{ $lesson->id }}">
                            <div class="cursor-grab lesson-handle text-gray-300 hover:text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/></svg>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    @if($lesson->video_url)
                                        <svg class="w-4 h-4 text-red-500 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                    @else
                                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    @endif
                                    <span class="text-sm text-gray-900 truncate">{{ $lesson->title }}</span>
                                </div>
                                <div class="flex items-center gap-3 mt-0.5">
                                    @if($lesson->is_free_preview)
                                        <span class="text-xs font-medium text-green-600">Free Preview</span>
                                    @endif
                                    @if($lesson->video_duration)
                                        <span class="text-xs text-gray-400">{{ $lesson->duration_formatted }}</span>
                                    @endif
                                    @if($lesson->processing_status === 'processing')
                                        <span class="text-xs font-semibold text-blue-500 flex items-center gap-1 bg-blue-50 px-2 py-0.5 rounded-md">
                                            <svg class="animate-spin w-3 h-3 text-blue-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8h-8z"></path></svg>
                                            Memproses Video...
                                        </span>
                                    @elseif($lesson->processing_status === 'failed')
                                        <span class="text-xs font-semibold text-red-500 bg-red-50 px-2 py-0.5 rounded-md">
                                            ⚠️ Kompresi Gagal
                                        </span>
                                    @endif

                                    <span class="text-xs font-medium {{ $lesson->is_published ? 'text-green-500' : 'text-amber-500' }}">
                                        {{ $lesson->is_published ? 'Published' : 'Draft' }}
                                    </span>
                                </div>
                            </div>

                            {{-- Edit button --}}
                            <button type="button"
                                    @click="openEditLesson({{ json_encode([
                                        'id' => $lesson->id,
                                        'title' => $lesson->title,
                                        'video_type' => $lesson->video_type ?? 'youtube',
                                        'video_url' => $lesson->video_url,
                                        'video_duration_seconds' => $lesson->video_duration_seconds ?? 0,
                                        'content' => $lesson->content,
                                        'is_free_preview' => $lesson->is_free_preview,
                                        'is_published' => $lesson->is_published,
                                        'course_id' => $course->id,
                                    ]) }})"
                                    class="p-1.5 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>

                            {{-- Delete --}}
                            <form method="POST" action="{{ route('instructor.courses.lessons.destroy', [$course->id, $lesson->id]) }}"
                                  onsubmit="return confirm('Hapus lesson ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>

                {{-- Add Lesson Button --}}
                <div class="px-5 py-3 bg-gray-50 border-t border-gray-100">
                    <button type="button"
                            @click="openAddLesson({{ $section->id }})"
                            class="flex items-center gap-2 text-sm text-primary-600 hover:text-primary-700 font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Tambah Lesson
                    </button>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-1">Belum ada section</h3>
                <p class="text-sm text-gray-500">Buat section pertama menggunakan form di atas.</p>
            </div>
        @endforelse
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{--  ADD LESSON MODAL                                                     --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    <div x-show="showAddModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="fixed inset-0 bg-gray-900/60" @click="showAddModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto"
             @click.outside="showAddModal = false">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900">Tambah Lesson Baru</h3>
                <button @click="showAddModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form :action="`{{ url('instructor/courses/' . $course->id . '/sections') }}/${addSectionId}/lessons`"
                  method="POST" enctype="multipart/form-data" class="p-6 space-y-4"
                  x-data="{ addVideoType: 'youtube' }">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul Lesson <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required placeholder="Contoh: Pengenalan Laravel"
                           class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                {{-- Toggle Video Type --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sumber Video</label>
                    <div class="flex rounded-xl overflow-hidden border border-gray-300">
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="video_type" value="youtube" x-model="addVideoType" class="sr-only">
                            <div :class="addVideoType === 'youtube' ? 'bg-red-500 text-white' : 'bg-white text-gray-600'"
                                 class="text-center py-2.5 text-sm font-medium transition-colors flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                YouTube
                            </div>
                        </label>
                        <label class="flex-1 cursor-pointer border-l border-gray-300">
                            <input type="radio" name="video_type" value="minio" x-model="addVideoType" class="sr-only">
                            <div :class="addVideoType === 'minio' ? 'bg-purple-600 text-white' : 'bg-white text-gray-600'"
                                 class="text-center py-2.5 text-sm font-medium transition-colors flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                Upload Video
                            </div>
                        </label>
                    </div>
                </div>

                {{-- YouTube URL --}}
                <div x-show="addVideoType === 'youtube'" x-transition>
                    <label class="block text-sm font-medium text-gray-700 mb-1">URL Video YouTube</label>
                    <input type="url" name="video_url" placeholder="https://youtube.com/watch?v=..."
                           class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                {{-- Upload Video ke MinIO --}}
                <div x-show="addVideoType === 'minio'" x-transition>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Upload File Video <span class="text-red-500">*</span></label>
                    <input type="file" name="video_file" accept="video/mp4,video/quicktime,video/x-msvideo"
                           class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-purple-500">
                    <p class="text-xs text-gray-500 mt-1">Format: MP4, MOV, AVI. Maks: 2GB. Video tersimpan privat di cloud.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Durasi Video (detik)</label>
                    <input type="number" name="video_duration_seconds" min="0" placeholder="Contoh: 600 (= 10 menit)"
                           class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Konten / Catatan</label>
                    <textarea id="lesson-content-add" name="content" rows="3" placeholder="Catatan atau materi teks (opsional)"
                              class="tinymce w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500"></textarea>
                </div>
                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_free_preview" value="1" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-gray-700">Free Preview</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_published" value="1" checked class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-gray-700">Published</span>
                    </label>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="flex-1 px-5 py-2.5 rounded-xl bg-primary-600 text-white text-sm font-semibold hover:bg-primary-700 transition-colors">
                        Tambah Lesson
                    </button>
                    <button type="button" @click="showAddModal = false" class="px-5 py-2.5 rounded-xl bg-gray-100 text-gray-700 text-sm font-medium hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{--  EDIT LESSON MODAL                                                    --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    <div x-show="showEditModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="fixed inset-0 bg-gray-900/60" @click="showEditModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto"
             @click.outside="showEditModal = false">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900">Edit Lesson</h3>
                <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form :action="`{{ url('instructor/courses/' . $course->id . '/lessons') }}/${editLesson.id}`"
                  method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul Lesson <span class="text-red-500">*</span></label>
                    <input type="text" name="title" x-model="editLesson.title" required
                           class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                {{-- Toggle Video Type (edit) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sumber Video</label>
                    <div class="flex rounded-xl overflow-hidden border border-gray-300">
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="video_type" value="youtube" x-model="editLesson.video_type" class="sr-only">
                            <div :class="editLesson.video_type === 'youtube' ? 'bg-red-500 text-white' : 'bg-white text-gray-600'"
                                 class="text-center py-2.5 text-sm font-medium transition-colors flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                YouTube
                            </div>
                        </label>
                        <label class="flex-1 cursor-pointer border-l border-gray-300">
                            <input type="radio" name="video_type" value="minio" x-model="editLesson.video_type" class="sr-only">
                            <div :class="editLesson.video_type === 'minio' ? 'bg-purple-600 text-white' : 'bg-white text-gray-600'"
                                 class="text-center py-2.5 text-sm font-medium transition-colors flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                Upload Video
                            </div>
                        </label>
                    </div>
                </div>

                {{-- YouTube URL (edit) --}}
                <div x-show="editLesson.video_type === 'youtube'" x-transition>
                    <label class="block text-sm font-medium text-gray-700 mb-1">URL Video YouTube</label>
                    <input type="url" name="video_url" x-model="editLesson.video_url"
                           class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                {{-- Upload Video MinIO (edit) --}}
                <div x-show="editLesson.video_type === 'minio'" x-transition>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ganti File Video</label>
                    <template x-if="editLesson.video_url && editLesson.video_type === 'minio'">
                        <p class="text-xs text-green-600 mb-2">✅ Video sudah diupload. Upload file baru untuk mengganti.</p>
                    </template>
                    <input type="file" name="video_file" accept="video/mp4,video/quicktime,video/x-msvideo"
                           class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-purple-500">
                    <p class="text-xs text-gray-500 mt-1">Biarkan kosong jika tidak ingin mengganti video.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Durasi Video (detik)</label>
                    <input type="number" name="video_duration_seconds" x-model="editLesson.video_duration_seconds" min="0"
                           class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Konten / Catatan</label>
                    <textarea id="lesson-content-edit" name="content" rows="3"
                              class="tinymce w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500"></textarea>
                </div>
                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_free_preview" value="1" :checked="editLesson.is_free_preview"
                               class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-gray-700">Free Preview</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_published" value="1" :checked="editLesson.is_published"
                               class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-gray-700">Published</span>
                    </label>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="flex-1 px-5 py-2.5 rounded-xl bg-primary-600 text-white text-sm font-semibold hover:bg-primary-700 transition-colors">
                        Simpan Perubahan
                    </button>
                    <button type="button" @click="showEditModal = false" class="px-5 py-2.5 rounded-xl bg-gray-100 text-gray-700 text-sm font-medium hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@include('partials.tinymce')

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
function lessonManager() {
    return {
        showAddModal: false,
        showEditModal: false,
        addSectionId: null,
        editLesson: { id: null, title: '', video_type: 'youtube', video_url: '', video_duration_seconds: 0, content: '', is_free_preview: false, is_published: true },

        openAddLesson(sectionId) {
            this.addSectionId = sectionId;
            this.showAddModal = true;
            // Clear TinyMCE for add
            setTimeout(() => {
                if (tinymce.get('lesson-content-add')) {
                    tinymce.get('lesson-content-add').setContent('');
                }
            }, 100);
        },

        openEditLesson(lesson) {
            this.editLesson = { ...lesson };
            this.showEditModal = true;
            // Set TinyMCE for edit
            setTimeout(() => {
                if (tinymce.get('lesson-content-edit')) {
                    tinymce.get('lesson-content-edit').setContent(lesson.content || '');
                }
            }, 100);
        },

        init() {
            // Section drag-drop
            const sectionsContainer = document.getElementById('sections-container');
            if (sectionsContainer) {
                new Sortable(sectionsContainer, {
                    handle: '.section-handle',
                    animation: 200,
                    ghostClass: 'opacity-50',
                    onEnd: (evt) => {
                        const order = [...sectionsContainer.querySelectorAll('.section-item')]
                            .map(el => parseInt(el.dataset.sectionId));
                        fetch(`{{ route('instructor.courses.sections.reorder', $course->id) }}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ order }),
                        });
                    }
                });
            }

            // Lesson drag-drop per section
            document.querySelectorAll('.lessons-container').forEach(container => {
                const sectionId = container.dataset.sectionId;
                new Sortable(container, {
                    handle: '.lesson-handle',
                    animation: 200,
                    ghostClass: 'opacity-50',
                    onEnd: (evt) => {
                        const order = [...container.querySelectorAll('.lesson-item')]
                            .map(el => parseInt(el.dataset.lessonId));
                        fetch(`/instructor/courses/{{ $course->id }}/sections/${sectionId}/lessons/reorder`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ order }),
                        });
                    }
                });
            });
        }
    };
}
</script>
@endpush
