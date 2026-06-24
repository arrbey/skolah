@extends('layouts.app')

@section('title', 'Mengerjakan ' . ucfirst($quiz->type) . ' — ' . $course->title)

@section('content')
<div x-data="quizAttempt({{ $timeLeftSeconds ?? 'null' }})" class="min-h-screen bg-gray-50" @beforeunload.window="confirmLeave($event)">

    {{-- Sticky Header --}}
    <div class="sticky top-0 z-40 bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-3xl mx-auto px-4 py-3 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">{{ ucfirst($quiz->type) }}</p>
                <p class="text-sm font-bold text-gray-900 truncate max-w-xs">{{ $quiz->title }}</p>
            </div>

            <div class="flex items-center gap-4">
                {{-- Timer --}}
                @if($quiz->time_limit)
                <div class="flex items-center gap-2 px-4 py-2 rounded-xl font-mono font-bold text-sm"
                     :class="timeLeft <= 60 ? 'bg-red-100 text-red-700' : (timeLeft <= 300 ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-700')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span x-text="formatTime(timeLeft)">00:00</span>
                </div>
                @endif

                {{-- Progress --}}
                <div class="text-sm text-gray-600">
                    <span class="font-bold text-gray-900" x-text="answeredCount">0</span>
                    / {{ $questions->count() }} dijawab
                </div>
            </div>
        </div>

        {{-- Progress bar --}}
        <div class="h-1 bg-gray-100">
            <div class="{{ $quiz->type === 'pretest' ? 'bg-blue-500' : 'bg-purple-500' }} h-1 transition-all duration-300"
                 :style="`width: ${(answeredCount / {{ $questions->count() }}) * 100}%`"></div>
        </div>
    </div>

    {{-- Soal-soal --}}
    <div class="max-w-3xl mx-auto px-4 py-8">
        <form id="quiz-form"
              action="{{ route('quiz.submit', [$course, $quiz, $attempt]) }}"
              method="POST"
              @submit="submitted = true">
            @csrf

            <div class="space-y-6">
                @foreach($questions as $idx => $question)
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6"
                     x-data="{ answered: false }"
                     :class="answered ? 'ring-2 {{ $quiz->type === 'pretest' ? 'ring-blue-200' : 'ring-purple-200' }}' : ''">

                    {{-- Nomor & Tipe --}}
                    <div class="flex items-start justify-between gap-3 mb-4">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg {{ $quiz->type === 'pretest' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }} flex items-center justify-center text-sm font-bold flex-shrink-0">
                                {{ $idx + 1 }}
                            </span>
                            <p class="font-medium text-gray-900 leading-relaxed">{{ $question->question }}</p>
                        </div>
                        <span class="px-2 py-0.5 text-xs rounded-full bg-gray-100 text-gray-500 flex-shrink-0">
                            {{ $question->points }} poin
                        </span>
                    </div>

                    {{-- Pilihan Ganda --}}
                    @if($question->isMultipleChoice())
                    <div class="space-y-2.5">
                        @foreach($question->options->sortBy('order') as $option)
                        <label class="flex items-center gap-3 p-3.5 rounded-xl border-2 border-gray-100 hover:border-gray-200 cursor-pointer transition-colors has-[:checked]:border-blue-400 has-[:checked]:bg-blue-50">
                            <input type="radio"
                                   name="answers[{{ $question->id }}]"
                                   value="{{ $option->id }}"
                                   @change="answered = true; updateAnsweredCount()"
                                   class="w-4 h-4 border-gray-300 {{ $quiz->type === 'pretest' ? 'text-blue-600 focus:ring-blue-500' : 'text-purple-600 focus:ring-purple-500' }}">
                            <span class="text-sm text-gray-700">{{ $option->option_text }}</span>
                        </label>
                        @endforeach
                    </div>

                    {{-- Benar / Salah --}}
                    @elseif($question->isTrueFalse())
                    <div class="flex gap-3">
                        @foreach($question->options->sortBy('order') as $option)
                        <label class="flex-1 flex items-center justify-center gap-2 p-3.5 rounded-xl border-2 border-gray-100 hover:border-gray-200 cursor-pointer transition-colors has-[:checked]:border-green-400 has-[:checked]:bg-green-50">
                            <input type="radio"
                                   name="answers[{{ $question->id }}]"
                                   value="{{ $option->id }}"
                                   @change="answered = true; updateAnsweredCount()"
                                   class="w-4 h-4 border-gray-300 text-green-600 focus:ring-green-500">
                            <span class="text-sm font-medium text-gray-700">{{ $option->option_text }}</span>
                        </label>
                        @endforeach
                    </div>

                    {{-- Essay --}}
                    @elseif($question->isEssay())
                    <textarea name="answers[{{ $question->id }}]" rows="5"
                              @input="answered = $event.target.value.trim().length > 0; updateAnsweredCount()"
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-transparent resize-none"
                              placeholder="Tuliskan jawaban Anda di sini..."></textarea>
                    <p class="text-xs text-gray-400 mt-1">Essay tidak dinilai otomatis.</p>
                    @endif

                </div>
                @endforeach
            </div>

            {{-- Submit --}}
            <div class="mt-8 flex items-center justify-between bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
                <div class="text-sm text-gray-500">
                    <span class="font-semibold text-gray-900" x-text="answeredCount">0</span>
                    dari {{ $questions->count() }} soal terjawab
                    <span x-show="{{ $questions->count() }} - answeredCount > 0" class="text-amber-600">
                        — (<span x-text="{{ $questions->count() }} - answeredCount"></span> belum dijawab)
                    </span>
                </div>
                <button type="submit"
                        @click="if (!submitted) { return confirm('Kumpulkan jawaban? Anda tidak bisa mengubah setelah ini.'); }"
                        class="px-8 py-3 {{ $quiz->type === 'pretest' ? 'bg-blue-600 hover:bg-blue-700' : 'bg-purple-600 hover:bg-purple-700' }} text-white font-semibold rounded-xl transition-colors">
                    Kumpulkan Jawaban
                </button>
            </div>

        </form>
    </div>
</div>

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
function quizAttempt(timeLeftSeconds) {
    return {
        timeLeft: timeLeftSeconds,
        answeredCount: 0,
        submitted: false,
        timer: null,

        init() {
            this.updateAnsweredCount();
            if (this.timeLeft !== null) {
                this.timer = setInterval(() => {
                    this.timeLeft--;
                    if (this.timeLeft <= 0) {
                        clearInterval(this.timer);
                        this.autoSubmit();
                    }
                }, 1000);
            }
        },

        updateAnsweredCount() {
            const form = document.getElementById('quiz-form');
            const radios = {};
            const textareas = [];

            form.querySelectorAll('input[type=radio]:checked').forEach(r => {
                radios[r.name] = true;
            });
            form.querySelectorAll('textarea').forEach(t => {
                if (t.value.trim()) textareas.push(t.name);
            });

            this.answeredCount = Object.keys(radios).length + textareas.length;
        },

        formatTime(sec) {
            if (sec === null || sec < 0) return '00:00';
            const m = Math.floor(sec / 60).toString().padStart(2, '0');
            const s = (sec % 60).toString().padStart(2, '0');
            return `${m}:${s}`;
        },

        autoSubmit() {
            this.submitted = true;
            document.getElementById('quiz-form').submit();
        },

        confirmLeave(e) {
            if (!this.submitted) {
                e.preventDefault();
                e.returnValue = '';
            }
        }
    }
}
</script>
@endpush
@endsection
