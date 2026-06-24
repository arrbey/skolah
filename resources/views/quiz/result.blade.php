@extends('layouts.app')

@section('title', 'Hasil ' . ucfirst($quiz->type) . ' — ' . $course->title)

@section('content')
<div class="min-h-screen bg-gray-50 py-10">
<div class="max-w-3xl mx-auto px-4 space-y-6">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('learn', $course->slug) }}" class="hover:text-gray-700">{{ $course->title }}</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('quiz.show', [$course, $quiz]) }}" class="hover:text-gray-700">{{ ucfirst($quiz->type) }}</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-900 font-medium">Hasil</span>
    </div>

    {{-- Kartu Hasil Utama --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="{{ $attempt->passed ? 'bg-green-600' : 'bg-red-500' }} px-8 py-8 text-center">
            <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                @if($attempt->passed)
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                @else
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                @endif
            </div>
            <div class="text-5xl font-bold text-white mb-1">{{ $attempt->score }}%</div>
            <p class="text-white/80 text-lg">{{ $attempt->passed ? 'Selamat! Anda Lulus' : 'Belum Lulus' }}</p>
            <p class="text-white/60 text-sm mt-1">Nilai minimum lulus: {{ $quiz->passing_score }}%</p>
        </div>

        <div class="px-8 py-6">
            <div class="grid grid-cols-3 gap-4 mb-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $attempt->earned_points }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">Poin Diperoleh</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $attempt->total_points }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">Total Poin</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $attempt->duration ?? '-' }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">Durasi</div>
                </div>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('learn', $course->slug) }}"
                   class="flex-1 text-center py-2.5 bg-gray-900 text-white font-semibold rounded-xl hover:bg-gray-800 transition-colors">
                    Kembali ke Kursus
                </a>
                <a href="{{ route('quiz.show', [$course, $quiz]) }}"
                   class="px-5 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition-colors">
                    Kerjakan Ulang
                </a>
            </div>
        </div>
    </div>

    {{-- Review Jawaban (hanya jika show_result = true) --}}
    @if($quiz->show_result)
    <div class="space-y-4">
        <h2 class="text-lg font-bold text-gray-900">Review Jawaban</h2>

        @foreach($answers as $idx => $answer)
        @php $question = $answer->question; @endphp
        <div class="bg-white rounded-2xl border shadow-sm p-6 {{ $answer->is_correct === true ? 'border-green-200' : ($answer->is_correct === false ? 'border-red-200' : 'border-gray-200') }}">
            <div class="flex items-start gap-3 mb-4">
                {{-- Status icon --}}
                <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5
                    {{ $answer->is_correct === true ? 'bg-green-100' : ($answer->is_correct === false ? 'bg-red-100' : 'bg-gray-100') }}">
                    @if($answer->is_correct === true)
                        <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    @elseif($answer->is_correct === false)
                        <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                    @else
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    @endif
                </div>

                <div class="flex-1">
                    <div class="flex items-start justify-between gap-2">
                        <p class="font-medium text-gray-900 leading-relaxed">
                            <span class="text-gray-400 text-sm mr-1">{{ $idx + 1 }}.</span>
                            {{ $question->question }}
                        </p>
                        <span class="text-xs px-2 py-0.5 rounded-full flex-shrink-0
                            {{ $answer->is_correct === true ? 'bg-green-100 text-green-700' : ($answer->is_correct === false ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-500') }}">
                            @if($answer->is_correct === true) +{{ $question->points }} poin
                            @elseif($answer->is_correct === false) 0 poin
                            @else Essay
                            @endif
                        </span>
                    </div>

                    {{-- Pilihan Ganda / Benar-Salah --}}
                    @if(!$question->isEssay())
                    <div class="mt-3 space-y-1.5">
                        @foreach($question->options->sortBy('order') as $option)
                        <div class="flex items-center gap-2 text-sm px-3 py-2 rounded-lg
                            {{ $option->is_correct ? 'bg-green-50 text-green-800 font-medium' : '' }}
                            {{ $answer->selected_option_id == $option->id && !$option->is_correct ? 'bg-red-50 text-red-700' : '' }}">
                            @if($option->is_correct)
                                <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            @elseif($answer->selected_option_id == $option->id)
                                <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            @else
                                <span class="w-4 h-4 flex-shrink-0"></span>
                            @endif
                            {{ $option->option_text }}
                            @if($answer->selected_option_id == $option->id && !$option->is_correct)
                                <span class="text-xs text-red-400">(jawaban Anda)</span>
                            @endif
                        </div>
                        @endforeach
                    </div>

                    {{-- Jika tidak menjawab --}}
                    @if(!$answer->selected_option_id && !$answer->is_correct)
                    <p class="mt-2 text-xs text-gray-400 italic">Tidak dijawab</p>
                    @endif

                    {{-- Essay --}}
                    @else
                    @if($answer->answer_text)
                    <div class="mt-3 bg-gray-50 rounded-xl px-4 py-3 text-sm text-gray-700">
                        <p class="text-xs text-gray-400 mb-1">Jawaban Anda:</p>
                        <p>{{ $answer->answer_text }}</p>
                    </div>
                    @else
                    <p class="mt-2 text-sm text-gray-400 italic">Tidak dijawab</p>
                    @endif
                    @endif

                    {{-- Penjelasan --}}
                    @if($question->explanation)
                    <div class="mt-3 text-sm text-gray-500 bg-blue-50 border border-blue-100 rounded-xl px-4 py-3">
                        <span class="font-medium text-blue-700">Penjelasan:</span>
                        {{ $question->explanation }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-gray-50 border border-gray-200 rounded-2xl p-6 text-center text-gray-500 text-sm">
        Review jawaban tidak tersedia untuk quiz ini.
    </div>
    @endif

</div>
</div>
@endsection
