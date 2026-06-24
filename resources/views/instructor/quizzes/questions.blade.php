@extends('layouts.instructor')

@section('title', 'Kelola Soal — ' . ucfirst($quiz->type))

@section('page-header')
    <div class="flex items-center gap-3">
        <a href="{{ route('instructor.courses.quizzes.index', $course) }}" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-lg font-bold text-gray-900">Kelola Soal — {{ ucfirst($quiz->type) }}</h1>
            <p class="text-sm text-gray-500 truncate max-w-xs">{{ $quiz->title }}</p>
        </div>
    </div>
@endsection

@section('content')
<div x-data="quizQuestionManager()" class="space-y-6">

    @if(session('success'))
        <div class="flex items-center gap-3 bg-green-50 border border-green-200 rounded-xl p-4 text-green-800">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Info bar --}}
    <div class="flex items-center justify-between bg-white border border-gray-200 rounded-xl px-5 py-3.5 shadow-sm">
        <div class="flex items-center gap-4 text-sm text-gray-600">
            <span class="font-semibold text-gray-900">{{ $questions->count() }} Soal</span>
            <span>·</span>
            <span>Nilai lulus: {{ $quiz->passing_score }}%</span>
            @if($quiz->time_limit)
                <span>·</span>
                <span>Batas waktu: {{ $quiz->time_limit }} menit</span>
            @endif
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('instructor.courses.quizzes.import', [$course, $quiz]) }}"
               class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors"
               title="Import soal format Aiken">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Import Aiken
            </a>
            <button @click="openAddModal()"
                    class="flex items-center gap-2 px-4 py-2 {{ $quiz->type === 'pretest' ? 'bg-blue-600 hover:bg-blue-700' : 'bg-purple-600 hover:bg-purple-700' }} text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Soal
            </button>
        </div>
    </div>

    {{-- Daftar Soal --}}
    <div class="space-y-4">
        @forelse($questions as $question)
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="flex items-start gap-4 p-5">
                {{-- Nomor --}}
                <div class="w-8 h-8 rounded-lg {{ $quiz->type === 'pretest' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }} flex items-center justify-center text-sm font-bold flex-shrink-0">
                    {{ $question->order }}
                </div>

                {{-- Konten Soal --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-3">
                        <p class="text-gray-900 font-medium leading-relaxed">{{ $question->question }}</p>
                        <div class="flex items-center gap-1.5 flex-shrink-0">
                            <span class="px-2 py-0.5 text-xs font-medium rounded-full
                                @if($question->type === 'multiple_choice') bg-blue-50 text-blue-700
                                @elseif($question->type === 'true_false') bg-amber-50 text-amber-700
                                @else bg-gray-100 text-gray-600
                                @endif">
                                {{ $question->type_label }}
                            </span>
                            <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-600">
                                {{ $question->points }} poin
                            </span>
                        </div>
                    </div>

                    {{-- Pilihan jawaban --}}
                    @if($question->options->isNotEmpty())
                    <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach($question->options as $opt)
                        <div class="flex items-center gap-2 text-sm {{ $opt->is_correct ? 'text-green-700 font-medium' : 'text-gray-500' }}">
                            <span class="w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0 {{ $opt->is_correct ? 'bg-green-100' : 'bg-gray-100' }}">
                                @if($opt->is_correct)
                                    <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                @else
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                @endif
                            </span>
                            {{ $opt->option_text }}
                        </div>
                        @endforeach
                    </div>
                    @elseif($question->type === 'essay')
                    <p class="mt-2 text-sm text-gray-400 italic">Essay — dinilai manual oleh instruktur</p>
                    @endif

                    @if($question->explanation)
                    <div class="mt-3 text-sm text-gray-500 bg-gray-50 rounded-lg px-3 py-2">
                        <span class="font-medium">Penjelasan:</span> {{ $question->explanation }}
                    </div>
                    @endif
                </div>

                {{-- Tombol aksi --}}
                <div class="flex flex-col gap-1.5 flex-shrink-0">
                    <button @click="openEditModal({{ $question->id }}, {{ json_encode([
                        'id'            => $question->id,
                        'question'      => $question->question,
                        'type'          => $question->type,
                        'explanation'   => $question->explanation,
                        'points'        => $question->points,
                        'options'       => $question->options->map(fn($o) => ['id' => $o->id, 'text' => $o->option_text, 'correct' => $o->is_correct])->values()->toArray(),
                        'true_false_answer' => $question->type === 'true_false'
                            ? ($question->options->firstWhere('option_text','Benar')?->is_correct ? 'true' : 'false')
                            : null,
                    ]) }})"
                    class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <form action="{{ route('instructor.courses.quizzes.questions.destroy', [$course, $quiz, $question]) }}" method="POST"
                          onsubmit="return confirm('Hapus soal ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-16 bg-white border border-gray-200 rounded-xl">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 mb-1">Belum ada soal</h3>
            <p class="text-gray-500 text-sm mb-5">Mulai tambahkan soal untuk quiz ini</p>
            <button @click="openAddModal()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 {{ $quiz->type === 'pretest' ? 'bg-blue-600 hover:bg-blue-700' : 'bg-purple-600 hover:bg-purple-700' }} text-white font-semibold rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Soal Pertama
            </button>
        </div>
        @endforelse
    </div>

    {{-- ══════════════════════════════════════════════════════ MODAL TAMBAH SOAL ══ --}}
    <div x-show="showAddModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         @keydown.escape.window="showAddModal = false">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showAddModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto" @click.stop>

            <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between rounded-t-2xl">
                <h3 class="text-lg font-bold text-gray-900">Tambah Soal Baru</h3>
                <button @click="showAddModal = false" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('instructor.courses.quizzes.questions.store', [$course, $quiz]) }}" method="POST" class="p-6 space-y-5">
                @csrf

                {{-- Tipe Soal --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Soal <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="flex flex-col items-center gap-2 p-3 border-2 rounded-xl cursor-pointer transition-colors"
                               :class="addType === 'multiple_choice' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300'">
                            <input type="radio" name="type" value="multiple_choice" x-model="addType" class="sr-only">
                            <svg class="w-6 h-6" :class="addType === 'multiple_choice' ? 'text-blue-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            <span class="text-xs font-medium" :class="addType === 'multiple_choice' ? 'text-blue-700' : 'text-gray-600'">Pilihan Ganda</span>
                        </label>
                        <label class="flex flex-col items-center gap-2 p-3 border-2 rounded-xl cursor-pointer transition-colors"
                               :class="addType === 'true_false' ? 'border-amber-500 bg-amber-50' : 'border-gray-200 hover:border-gray-300'">
                            <input type="radio" name="type" value="true_false" x-model="addType" class="sr-only">
                            <svg class="w-6 h-6" :class="addType === 'true_false' ? 'text-amber-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-xs font-medium" :class="addType === 'true_false' ? 'text-amber-700' : 'text-gray-600'">Benar / Salah</span>
                        </label>
                        <label class="flex flex-col items-center gap-2 p-3 border-2 rounded-xl cursor-pointer transition-colors"
                               :class="addType === 'essay' ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-gray-300'">
                            <input type="radio" name="type" value="essay" x-model="addType" class="sr-only">
                            <svg class="w-6 h-6" :class="addType === 'essay' ? 'text-green-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <span class="text-xs font-medium" :class="addType === 'essay' ? 'text-green-700' : 'text-gray-600'">Essay</span>
                        </label>
                    </div>
                </div>

                {{-- Pertanyaan --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Pertanyaan <span class="text-red-500">*</span></label>
                    <textarea id="quiz-question-add" name="question" rows="3" required
                              class="tinymce w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-transparent resize-none"
                              placeholder="Tulis pertanyaan di sini..."></textarea>
                </div>

                {{-- Poin --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Poin <span class="text-red-500">*</span></label>
                    <input type="number" name="points" value="1" min="1" max="100" required
                           class="w-32 px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                </div>

                {{-- Pilihan Ganda --}}
                <div x-show="addType === 'multiple_choice'" class="space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="text-sm font-medium text-gray-700">Pilihan Jawaban <span class="text-red-500">*</span></label>
                        <button type="button" @click="addOption()" class="text-xs text-blue-600 hover:text-blue-700 font-medium">
                            + Tambah Pilihan
                        </button>
                    </div>
                    <p class="text-xs text-gray-400">Centang pilihan yang merupakan jawaban benar</p>
                    <template x-for="(opt, idx) in addOptions" :key="idx">
                        <div class="flex items-center gap-3">
                            <input type="checkbox" :name="addType === 'multiple_choice' ? `options[${idx}][correct]` : ''" value="1" x-model="opt.correct"
                                   class="w-4 h-4 rounded border-gray-300 text-green-600 focus:ring-green-500 flex-shrink-0">
                            <input type="text" :name="addType === 'multiple_choice' ? `options[${idx}][text]` : ''" x-model="opt.text"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   :placeholder="`Pilihan ${idx + 1}`">
                            <button type="button" @click="removeOption(idx)" x-show="addOptions.length > 2"
                                    class="text-red-400 hover:text-red-600 flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </template>
                </div>

                {{-- Benar / Salah --}}
                <div x-show="addType === 'true_false'" class="space-y-2">
                    <label class="text-sm font-medium text-gray-700">Jawaban yang Benar <span class="text-red-500">*</span></label>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio"
                                   :name="addType === 'true_false' ? 'true_false_answer' : ''"
                                   value="true"
                                   :checked="addTrueFalseAnswer === 'true'"
                                   @change="addTrueFalseAnswer = 'true'"
                                   class="w-4 h-4 border-gray-300 text-green-600 focus:ring-green-500">
                            <span class="text-sm font-medium text-green-700">Benar</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio"
                                   :name="addType === 'true_false' ? 'true_false_answer' : ''"
                                   value="false"
                                   :checked="addTrueFalseAnswer === 'false'"
                                   @change="addTrueFalseAnswer = 'false'"
                                   class="w-4 h-4 border-gray-300 text-red-600 focus:ring-red-500">
                            <span class="text-sm font-medium text-red-700">Salah</span>
                        </label>
                    </div>
                </div>

                {{-- Essay info --}}
                <div x-show="addType === 'essay'" class="bg-green-50 border border-green-200 rounded-xl p-4 text-sm text-green-800">
                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Soal essay tidak otomatis dinilai. Siswa mengisi jawaban bebas dan tidak ada penilaian otomatis.
                </div>

                {{-- Penjelasan --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Penjelasan (opsional)</label>
                    <textarea id="quiz-explanation-add" name="explanation" rows="2"
                              class="tinymce w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-transparent resize-none"
                              placeholder="Penjelasan jawaban yang ditampilkan setelah siswa menjawab..."></textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                            class="flex-1 py-2.5 {{ $quiz->type === 'pretest' ? 'bg-blue-600 hover:bg-blue-700' : 'bg-purple-600 hover:bg-purple-700' }} text-white font-semibold rounded-xl transition-colors">
                        Simpan Soal
                    </button>
                    <button type="button" @click="showAddModal = false"
                            class="px-5 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════ MODAL EDIT SOAL ══ --}}
    <div x-show="showEditModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         @keydown.escape.window="showEditModal = false">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showEditModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto" @click.stop>

            <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between rounded-t-2xl">
                <h3 class="text-lg font-bold text-gray-900">Edit Soal</h3>
                <button @click="showEditModal = false" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <template x-if="editQuestion">
                <form :action="`{{ url('instructor/courses/' . $course->id . '/quizzes/' . $quiz->id . '/questions') }}/${editQuestion.id}`"
                      method="POST" class="p-6 space-y-5">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">

                    {{-- Tipe (read-only) --}}
                    <div class="bg-gray-50 rounded-xl px-4 py-3">
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Tipe Soal</span>
                        <p class="text-sm font-semibold text-gray-800 mt-0.5" x-text="getTypeLabel(editQuestion.type)"></p>
                    </div>

                    {{-- Pertanyaan --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Pertanyaan <span class="text-red-500">*</span></label>
                        <textarea id="quiz-question-edit" name="question" rows="3" required
                                  class="tinymce w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-transparent resize-none"></textarea>
                    </div>

                    {{-- Poin --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Poin <span class="text-red-500">*</span></label>
                        <input type="number" name="points" x-model="editQuestion.points" min="1" max="100" required
                               class="w-32 px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    </div>

                    {{-- Pilihan Ganda --}}
                    <template x-if="editQuestion.type === 'multiple_choice'">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <label class="text-sm font-medium text-gray-700">Pilihan Jawaban</label>
                                <button type="button" @click="addEditOption()" class="text-xs text-blue-600 hover:text-blue-700 font-medium">
                                    + Tambah Pilihan
                                </button>
                            </div>
                            <template x-for="(opt, idx) in editQuestion.options" :key="idx">
                                <div class="flex items-center gap-3">
                                    <input type="checkbox" :name="`options[${idx}][correct]`" value="1"
                                           :checked="opt.correct" @change="opt.correct = $event.target.checked"
                                           class="w-4 h-4 rounded border-gray-300 text-green-600 focus:ring-green-500 flex-shrink-0">
                                    <input type="text" :name="`options[${idx}][text]`" x-model="opt.text"
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                           :placeholder="`Pilihan ${idx + 1}`">
                                    <button type="button" @click="removeEditOption(idx)" x-show="editQuestion.options.length > 2"
                                            class="text-red-400 hover:text-red-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </template>

                    {{-- Benar / Salah --}}
                    <template x-if="editQuestion.type === 'true_false'">
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-700">Jawaban yang Benar</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="true_false_answer" value="true"
                                           :checked="editQuestion.true_false_answer === 'true'"
                                           @change="editQuestion.true_false_answer = 'true'"
                                           class="w-4 h-4 text-green-600 focus:ring-green-500">
                                    <span class="text-sm font-medium text-green-700">Benar</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="true_false_answer" value="false"
                                           :checked="editQuestion.true_false_answer === 'false'"
                                           @change="editQuestion.true_false_answer = 'false'"
                                           class="w-4 h-4 text-red-600 focus:ring-red-500">
                                    <span class="text-sm font-medium text-red-700">Salah</span>
                                </label>
                            </div>
                        </div>
                    </template>

                    {{-- Penjelasan --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Penjelasan (opsional)</label>
                        <textarea id="quiz-explanation-edit" name="explanation" rows="2"
                                  class="tinymce w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-transparent resize-none"
                                  placeholder="Penjelasan jawaban..."></textarea>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="submit"
                                class="flex-1 py-2.5 {{ $quiz->type === 'pretest' ? 'bg-blue-600 hover:bg-blue-700' : 'bg-purple-600 hover:bg-purple-700' }} text-white font-semibold rounded-xl transition-colors">
                            Simpan Perubahan
                        </button>
                        <button type="button" @click="showEditModal = false"
                                class="px-5 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition-colors">
                            Batal
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </div>

</div>

@include('partials.tinymce')

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
function quizQuestionManager() {
    return {
        showAddModal: false,
        showEditModal: false,
        addType: 'multiple_choice',
        addTrueFalseAnswer: 'true',
        addOptions: [
            { text: '', correct: false },
            { text: '', correct: false },
            { text: '', correct: false },
            { text: '', correct: false },
        ],
        editQuestion: null,

        openAddModal() {
            this.addType = 'multiple_choice';
            this.addTrueFalseAnswer = 'true';
            this.addOptions = [
                { text: '', correct: false },
                { text: '', correct: false },
                { text: '', correct: false },
                { text: '', correct: false },
            ];
            this.showAddModal = true;
            // Clear TinyMCE
            setTimeout(() => {
                if (tinymce.get('quiz-question-add')) tinymce.get('quiz-question-add').setContent('');
                if (tinymce.get('quiz-explanation-add')) tinymce.get('quiz-explanation-add').setContent('');
            }, 100);
        },

        openEditModal(id, data) {
            this.editQuestion = JSON.parse(JSON.stringify(data));
            this.showEditModal = true;
            // Set TinyMCE
            setTimeout(() => {
                if (tinymce.get('quiz-question-edit')) tinymce.get('quiz-question-edit').setContent(this.editQuestion.question || '');
                if (tinymce.get('quiz-explanation-edit')) tinymce.get('quiz-explanation-edit').setContent(this.editQuestion.explanation || '');
            }, 100);
        },

        addOption() {
            this.addOptions.push({ text: '', correct: false });
        },

        removeOption(idx) {
            if (this.addOptions.length > 2) this.addOptions.splice(idx, 1);
        },

        addEditOption() {
            this.editQuestion.options.push({ text: '', correct: false });
        },

        removeEditOption(idx) {
            if (this.editQuestion.options.length > 2) this.editQuestion.options.splice(idx, 1);
        },

        getTypeLabel(type) {
            const labels = { multiple_choice: 'Pilihan Ganda', true_false: 'Benar / Salah', essay: 'Essay' };
            return labels[type] || type;
        }
    }
}
</script>
@endpush
@endsection
