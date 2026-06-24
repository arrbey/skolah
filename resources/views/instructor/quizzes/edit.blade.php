@extends('layouts.instructor')

@section('title', 'Edit ' . ucfirst($quiz->type) . ' — ' . $course->title)

@section('page-header')
    <div class="flex items-center gap-3">
        <a href="{{ route('instructor.courses.quizzes.index', $course) }}" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-lg font-bold text-gray-900">Edit {{ ucfirst($quiz->type) }}</h1>
            <p class="text-sm text-gray-500 truncate max-w-xs">{{ $course->title }}</p>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">

        <div class="flex items-center gap-3 mb-6 pb-6 border-b border-gray-100">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $quiz->type === 'pretest' ? 'bg-blue-100' : 'bg-purple-100' }}">
                <svg class="w-5 h-5 {{ $quiz->type === 'pretest' ? 'text-blue-600' : 'text-purple-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <div>
                <h2 class="font-semibold text-gray-900">Edit Pengaturan {{ ucfirst($quiz->type) }}</h2>
                <p class="text-sm text-gray-500">{{ $quiz->questions_count }} soal tersedia</p>
            </div>
        </div>

        <form action="{{ route('instructor.courses.quizzes.update', [$course, $quiz]) }}" method="POST" class="space-y-5">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Judul Quiz <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $quiz->title) }}" required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('title') border-red-400 @enderror">
                @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi</label>
                <textarea name="description" rows="3"
                          class="tinymce w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-transparent resize-none">{{ old('description', $quiz->description) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nilai Lulus (%) <span class="text-red-500">*</span></label>
                    <input type="number" name="passing_score" value="{{ old('passing_score', $quiz->passing_score) }}" min="0" max="100" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    @error('passing_score') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Batas Waktu (menit)</label>
                    <input type="number" name="time_limit" value="{{ old('time_limit', $quiz->time_limit) }}" min="1" max="300"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                           placeholder="Kosongkan = tidak terbatas">
                </div>
            </div>

            <div class="bg-gray-50 rounded-xl p-4 space-y-3">
                <p class="text-sm font-medium text-gray-700">Pengaturan Lanjutan</p>

                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $quiz->is_active) ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <div>
                        <span class="text-sm font-medium text-gray-700">Aktifkan Quiz</span>
                        <p class="text-xs text-gray-400">Siswa dapat mengakses quiz ini</p>
                    </div>
                </label>

                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="hidden" name="show_result" value="0">
                    <input type="checkbox" name="show_result" value="1" {{ old('show_result', $quiz->show_result) ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <div>
                        <span class="text-sm font-medium text-gray-700">Tampilkan Hasil ke Siswa</span>
                        <p class="text-xs text-gray-400">Siswa dapat melihat skor dan jawaban benar setelah selesai</p>
                    </div>
                </label>

                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="hidden" name="randomize_questions" value="0">
                    <input type="checkbox" name="randomize_questions" value="1" {{ old('randomize_questions', $quiz->randomize_questions) ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <div>
                        <span class="text-sm font-medium text-gray-700">Acak Urutan Soal</span>
                        <p class="text-xs text-gray-400">Urutan soal diacak setiap siswa mengerjakan</p>
                    </div>
                </label>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="flex-1 py-2.5 {{ $quiz->type === 'pretest' ? 'bg-blue-600 hover:bg-blue-700' : 'bg-purple-600 hover:bg-purple-700' }} text-white font-semibold rounded-xl transition-colors">
                    Simpan Perubahan
                </button>
                <a href="{{ route('instructor.courses.quizzes.index', $course) }}"
                   class="px-5 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@include('partials.tinymce')
