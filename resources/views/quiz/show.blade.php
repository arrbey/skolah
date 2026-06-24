@extends('layouts.app')

@section('title', ucfirst($quiz->type) . ' — ' . $course->title)

@section('content')
<div class="min-h-screen bg-gray-50 py-10">
<div class="max-w-2xl mx-auto px-4">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
        <a href="{{ route('learn', $course->slug) }}" class="hover:text-gray-700">{{ $course->title }}</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-900 font-medium">{{ ucfirst($quiz->type) }}</span>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

        {{-- Header --}}
        <div class="{{ $quiz->type === 'pretest' ? 'bg-blue-600' : 'bg-purple-600' }} px-8 py-8">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <span class="text-white/70 text-sm font-medium uppercase tracking-wide">{{ ucfirst($quiz->type) }}</span>
                    <h1 class="text-2xl font-bold text-white mt-0.5">{{ $quiz->title }}</h1>
                </div>
            </div>
        </div>

        <div class="p-8">

            {{-- Deskripsi --}}
            @if($quiz->description)
            <p class="text-gray-600 mb-6 leading-relaxed">{{ $quiz->description }}</p>
            @endif

            {{-- Info --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-gray-50 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $quiz->total_questions }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">Soal</div>
                </div>
                <div class="bg-gray-50 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $quiz->passing_score }}%</div>
                    <div class="text-xs text-gray-500 mt-0.5">Nilai Lulus</div>
                </div>
                <div class="bg-gray-50 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $quiz->time_limit ? $quiz->time_limit . 'm' : '∞' }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">Batas Waktu</div>
                </div>
                <div class="bg-gray-50 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $quiz->total_points }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">Total Poin</div>
                </div>
            </div>

            {{-- Hasil sebelumnya --}}
            @if($lastAttempt && $lastAttempt->completed_at)
            <div class="mb-6 p-4 {{ $lastAttempt->passed ? 'bg-green-50 border-green-200' : 'bg-amber-50 border-amber-200' }} border rounded-xl">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold {{ $lastAttempt->passed ? 'text-green-800' : 'text-amber-800' }}">
                            Pengerjaan Terakhir:
                            <span class="text-lg ml-1">{{ $lastAttempt->score }}%</span>
                            — {{ $lastAttempt->passed ? '✓ Lulus' : '✗ Belum Lulus' }}
                        </p>
                        <p class="text-xs {{ $lastAttempt->passed ? 'text-green-600' : 'text-amber-600' }} mt-0.5">
                            {{ $lastAttempt->completed_at->translatedFormat('d F Y, H:i') }}
                        </p>
                    </div>
                    @if($quiz->show_result)
                    <a href="{{ route('quiz.result', [$course, $quiz, $lastAttempt]) }}"
                       class="text-sm font-medium {{ $lastAttempt->passed ? 'text-green-700 hover:text-green-800' : 'text-amber-700 hover:text-amber-800' }} underline">
                        Lihat Hasil
                    </a>
                    @endif
                </div>
            </div>
            @endif

            {{-- Ketentuan --}}
            <div class="mb-8 space-y-2">
                <p class="text-sm font-semibold text-gray-700 mb-3">Ketentuan:</p>
                <div class="flex items-start gap-2 text-sm text-gray-600">
                    <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Pastikan koneksi internet stabil selama mengerjakan.
                </div>
                @if($quiz->time_limit)
                <div class="flex items-start gap-2 text-sm text-gray-600">
                    <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Quiz otomatis dikumpulkan setelah batas waktu {{ $quiz->time_limit }} menit habis.
                </div>
                @endif
                <div class="flex items-start gap-2 text-sm text-gray-600">
                    <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Nilai minimum lulus adalah {{ $quiz->passing_score }}%.
                </div>
                @if($quiz->randomize_questions)
                <div class="flex items-start gap-2 text-sm text-gray-600">
                    <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Urutan soal diacak.
                </div>
                @endif
            </div>

            {{-- Tombol Mulai --}}
            <form action="{{ route('quiz.start', [$course, $quiz]) }}" method="POST">
                @csrf
                <button type="submit"
                        class="w-full py-3.5 {{ $quiz->type === 'pretest' ? 'bg-blue-600 hover:bg-blue-700' : 'bg-purple-600 hover:bg-purple-700' }} text-white text-lg font-semibold rounded-xl transition-colors">
                    {{ $alreadyDone ? 'Kerjakan Ulang' : 'Mulai ' . ucfirst($quiz->type) }}
                </button>
            </form>

            <div class="text-center mt-4">
                <a href="{{ route('learn', $course->slug) }}" class="text-sm text-gray-500 hover:text-gray-700">
                    Kembali ke Kursus
                </a>
            </div>

        </div>
    </div>
</div>
</div>
@endsection
