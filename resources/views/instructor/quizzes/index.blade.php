@extends('layouts.instructor')

@section('title', 'Pretest & Posttest — ' . $course->title)

@section('page-header')
    <div class="flex items-center gap-3">
        <a href="{{ route('instructor.courses.edit', $course->id) }}" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-lg font-bold text-gray-900">Pretest & Posttest</h1>
            <p class="text-sm text-gray-500 truncate max-w-xs">{{ $course->title }}</p>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Navigation tabs --}}
    <div class="flex gap-2 border-b border-gray-200">
        <a href="{{ route('instructor.courses.edit', $course->id) }}"
           class="px-4 py-2.5 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:border-gray-300">
            Detail Kursus
        </a>
        <a href="{{ route('instructor.courses.lessons', $course->id) }}"
           class="px-4 py-2.5 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:border-gray-300">
            Kelola Lesson
        </a>
        <span class="px-4 py-2.5 text-sm font-semibold text-primary-600 border-b-2 border-primary-600">
            Pretest & Posttest
        </span>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 bg-green-50 border border-green-200 rounded-xl p-4 text-green-800">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 rounded-xl p-4 text-red-800">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Pretest Card --}}
    @php
        $pretest  = $quizzes->firstWhere('type', 'pretest');
        $posttest = $quizzes->firstWhere('type', 'posttest');
    @endphp

    <div class="grid md:grid-cols-2 gap-6">

        {{-- ── PRETEST ── --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="bg-blue-600 p-5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-white text-lg">Pretest</h3>
                        <p class="text-blue-100 text-sm">Diberikan sebelum belajar</p>
                    </div>
                </div>
                @if($pretest)
                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $pretest->is_active ? 'bg-green-400 text-green-900' : 'bg-gray-300 text-gray-700' }}">
                        {{ $pretest->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                @endif
            </div>

            <div class="p-5">
                @if($pretest)
                    <h4 class="font-semibold text-gray-900 mb-1">{{ $pretest->title }}</h4>
                    <p class="text-sm text-gray-500 mb-4">{{ $pretest->description ?: 'Tidak ada deskripsi' }}</p>

                    <div class="grid grid-cols-3 gap-3 mb-5">
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <div class="text-xl font-bold text-gray-900">{{ $pretest->questions_count }}</div>
                            <div class="text-xs text-gray-500 mt-0.5">Soal</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <div class="text-xl font-bold text-gray-900">{{ $pretest->passing_score }}%</div>
                            <div class="text-xs text-gray-500 mt-0.5">Nilai Lulus</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <div class="text-xl font-bold text-gray-900">{{ $pretest->time_limit ? $pretest->time_limit . 'm' : '∞' }}</div>
                            <div class="text-xs text-gray-500 mt-0.5">Waktu</div>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('instructor.courses.quizzes.questions', [$course, $pretest]) }}"
                           class="flex-1 text-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                            Kelola Soal
                        </a>
                        <a href="{{ route('instructor.courses.quizzes.edit', [$course, $pretest]) }}"
                           class="px-3 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                            Edit
                        </a>
                        <a href="{{ route('instructor.courses.quizzes.results', [$course, $pretest]) }}"
                           class="px-3 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                            Hasil
                        </a>
                        <form action="{{ route('instructor.courses.quizzes.destroy', [$course, $pretest]) }}" method="POST"
                              onsubmit="return confirm('Hapus pretest dan semua data attempt?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="px-3 py-2 bg-red-50 text-red-600 text-sm font-medium rounded-lg hover:bg-red-100 transition-colors">
                                Hapus
                            </button>
                        </form>
                    </div>
                @else
                    <div class="text-center py-6">
                        <p class="text-gray-500 text-sm mb-4">Belum ada pretest untuk kursus ini.</p>
                        <a href="{{ route('instructor.courses.quizzes.create', [$course, 'type' => 'pretest']) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Buat Pretest
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- ── POSTTEST ── --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="bg-purple-600 p-5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-white text-lg">Posttest</h3>
                        <p class="text-purple-100 text-sm">Diberikan setelah selesai belajar</p>
                    </div>
                </div>
                @if($posttest)
                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $posttest->is_active ? 'bg-green-400 text-green-900' : 'bg-gray-300 text-gray-700' }}">
                        {{ $posttest->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                @endif
            </div>

            <div class="p-5">
                @if($posttest)
                    <h4 class="font-semibold text-gray-900 mb-1">{{ $posttest->title }}</h4>
                    <p class="text-sm text-gray-500 mb-4">{{ $posttest->description ?: 'Tidak ada deskripsi' }}</p>

                    <div class="grid grid-cols-3 gap-3 mb-5">
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <div class="text-xl font-bold text-gray-900">{{ $posttest->questions_count }}</div>
                            <div class="text-xs text-gray-500 mt-0.5">Soal</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <div class="text-xl font-bold text-gray-900">{{ $posttest->passing_score }}%</div>
                            <div class="text-xs text-gray-500 mt-0.5">Nilai Lulus</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <div class="text-xl font-bold text-gray-900">{{ $posttest->time_limit ? $posttest->time_limit . 'm' : '∞' }}</div>
                            <div class="text-xs text-gray-500 mt-0.5">Waktu</div>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('instructor.courses.quizzes.questions', [$course, $posttest]) }}"
                           class="flex-1 text-center px-3 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors">
                            Kelola Soal
                        </a>
                        <a href="{{ route('instructor.courses.quizzes.edit', [$course, $posttest]) }}"
                           class="px-3 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                            Edit
                        </a>
                        <a href="{{ route('instructor.courses.quizzes.results', [$course, $posttest]) }}"
                           class="px-3 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                            Hasil
                        </a>
                        <form action="{{ route('instructor.courses.quizzes.destroy', [$course, $posttest]) }}" method="POST"
                              onsubmit="return confirm('Hapus posttest dan semua data attempt?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="px-3 py-2 bg-red-50 text-red-600 text-sm font-medium rounded-lg hover:bg-red-100 transition-colors">
                                Hapus
                            </button>
                        </form>
                    </div>
                @else
                    <div class="text-center py-6">
                        <p class="text-gray-500 text-sm mb-4">Belum ada posttest untuk kursus ini.</p>
                        <a href="{{ route('instructor.courses.quizzes.create', [$course, 'type' => 'posttest']) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Buat Posttest
                        </a>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
