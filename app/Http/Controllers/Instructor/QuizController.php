<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAnswer;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Services\AikenQuizParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    // ══════════════════════════════════════════════════════════════════════════
    // INDEX — Daftar Quiz (pretest & posttest) untuk 1 course
    // ══════════════════════════════════════════════════════════════════════════

    public function index(Course $course)
    {
        $this->authorize('manageQuizzes', $course);

        $quizzes = $course->quizzes()->withCount('questions')->with('attempts')->get();

        return view('instructor.quizzes.index', compact('course', 'quizzes'));
    }

    // ══════════════════════════════════════════════════════════════════════════
    // CREATE / STORE — Buat quiz baru
    // ══════════════════════════════════════════════════════════════════════════

    public function create(Course $course, Request $request)
    {
        $this->authorize('manageQuizzes', $course);

        // Cek tipe yang diminta (pretest/posttest)
        $type = $request->input('type', 'pretest');

        // Cek apakah tipe ini sudah ada
        $exists = $course->quizzes()->where('type', $type)->exists();
        if ($exists) {
            return redirect()->route('instructor.courses.quizzes.index', $course)
                ->with('error', ucfirst($type) . ' sudah ada untuk course ini.');
        }

        return view('instructor.quizzes.create', compact('course', 'type'));
    }

    public function store(Course $course, Request $request)
    {
        $this->authorize('manageQuizzes', $course);

        $validated = $request->validate([
            'type'                => 'required|in:pretest,posttest',
            'title'               => 'required|string|max:255',
            'description'         => 'nullable|string',
            'passing_score'       => 'required|integer|min:0|max:100',
            'time_limit'          => 'nullable|integer|min:1|max:300',
            'is_active'           => 'boolean',
            'show_result'         => 'boolean',
            'randomize_questions' => 'boolean',
        ]);

        // Cek duplikat
        if ($course->quizzes()->where('type', $validated['type'])->exists()) {
            return back()->withErrors(['type' => ucfirst($validated['type']) . ' sudah ada.']);
        }

        $validated['is_active']           = $request->boolean('is_active', true);
        $validated['show_result']         = $request->boolean('show_result', true);
        $validated['randomize_questions'] = $request->boolean('randomize_questions', false);

        $quiz = $course->quizzes()->create($validated);

        return redirect()->route('instructor.courses.quizzes.questions', [$course, $quiz])
            ->with('success', ucfirst($validated['type']) . ' berhasil dibuat! Sekarang tambahkan soal.');
    }

    // ══════════════════════════════════════════════════════════════════════════
    // EDIT / UPDATE — Edit pengaturan quiz
    // ══════════════════════════════════════════════════════════════════════════

    public function edit(Course $course, Quiz $quiz)
    {
        $this->authorize('manageQuizzes', $course);
        abort_if($quiz->course_id !== $course->id, 403);

        return view('instructor.quizzes.edit', compact('course', 'quiz'));
    }

    public function update(Course $course, Quiz $quiz, Request $request)
    {
        $this->authorize('manageQuizzes', $course);
        abort_if($quiz->course_id !== $course->id, 403);

        $validated = $request->validate([
            'title'               => 'required|string|max:255',
            'description'         => 'nullable|string',
            'passing_score'       => 'required|integer|min:0|max:100',
            'time_limit'          => 'nullable|integer|min:1|max:300',
            'is_active'           => 'boolean',
            'show_result'         => 'boolean',
            'randomize_questions' => 'boolean',
        ]);

        $validated['is_active']           = $request->boolean('is_active');
        $validated['show_result']         = $request->boolean('show_result');
        $validated['randomize_questions'] = $request->boolean('randomize_questions');

        $quiz->update($validated);

        return redirect()->route('instructor.courses.quizzes.index', $course)
            ->with('success', 'Pengaturan quiz berhasil diperbarui.');
    }

    // ══════════════════════════════════════════════════════════════════════════
    // DESTROY — Hapus quiz
    // ══════════════════════════════════════════════════════════════════════════

    public function destroy(Course $course, Quiz $quiz)
    {
        $this->authorize('manageQuizzes', $course);
        abort_if($quiz->course_id !== $course->id, 403);

        $quiz->delete();

        return redirect()->route('instructor.courses.quizzes.index', $course)
            ->with('success', ucfirst($quiz->type) . ' berhasil dihapus.');
    }

    // ══════════════════════════════════════════════════════════════════════════
    // QUESTIONS — Halaman kelola soal
    // ══════════════════════════════════════════════════════════════════════════

    public function questions(Course $course, Quiz $quiz)
    {
        $this->authorize('manageQuizzes', $course);
        abort_if($quiz->course_id !== $course->id, 403);

        $questions = $quiz->questions()->with('options')->get();

        return view('instructor.quizzes.questions', compact('course', 'quiz', 'questions'));
    }

    public function storeQuestion(Course $course, Quiz $quiz, Request $request)
    {
        $this->authorize('manageQuizzes', $course);
        abort_if($quiz->course_id !== $course->id, 403);

        $validated = $request->validate([
            'question'    => 'required|string',
            'type'        => 'required|in:multiple_choice,true_false,essay',
            'explanation' => 'nullable|string',
            'points'      => 'required|integer|min:1|max:100',
            'options'          => 'nullable|array',
            'options.*.text'   => 'nullable|string',
            'options.*.correct'=> 'nullable',
            'true_false_answer'=> 'nullable|in:true,false',
        ]);

        // Validasi manual per tipe
        if ($request->type === 'multiple_choice') {
            $opts = collect($request->input('options', []))->filter(fn($o) => !empty($o['text']));
            if ($opts->count() < 2) {
                return back()->withErrors(['options' => 'Minimal 2 pilihan jawaban.'])->withInput();
            }
        }

        if ($request->type === 'true_false' && !$request->filled('true_false_answer')) {
            return back()->withErrors(['true_false_answer' => 'Pilih jawaban yang benar.'])->withInput();
        }

        $order = $quiz->questions()->max('order') + 1;

        $question = $quiz->questions()->create([
            'question'    => $validated['question'],
            'type'        => $validated['type'],
            'explanation' => $validated['explanation'] ?? null,
            'points'      => $validated['points'],
            'order'       => $order,
        ]);

        // Simpan options
        if ($validated['type'] === 'multiple_choice' && !empty($validated['options'])) {
            $optIdx = 0;
            foreach ($validated['options'] as $i => $opt) {
                if (empty($opt['text'])) continue;
                $question->options()->create([
                    'option_text' => $opt['text'],
                    'is_correct'  => isset($opt['correct']) && $opt['correct'] == '1',
                    'order'       => ++$optIdx,
                ]);
            }
        } elseif ($validated['type'] === 'true_false') {
            $tfAnswer = $request->input('true_false_answer', 'true');
            $question->options()->createMany([
                ['option_text' => 'Benar', 'is_correct' => $tfAnswer === 'true',  'order' => 1],
                ['option_text' => 'Salah', 'is_correct' => $tfAnswer === 'false', 'order' => 2],
            ]);
        }

        return redirect()->route('instructor.courses.quizzes.questions', [$course, $quiz])
            ->with('success', 'Soal berhasil ditambahkan.');
    }

    public function updateQuestion(Course $course, Quiz $quiz, QuizQuestion $question, Request $request)
    {
        $this->authorize('manageQuizzes', $course);
        abort_if($question->quiz_id !== $quiz->id, 403);

        $validated = $request->validate([
            'question'         => 'required|string',
            'explanation'      => 'nullable|string',
            'points'           => 'required|integer|min:1|max:100',
            'options'          => 'nullable|array',
            'options.*.id'     => 'nullable|integer',
            'options.*.text'   => 'nullable|string',
            'options.*.correct'=> 'nullable',
            'true_false_answer'=> 'nullable|in:true,false',
        ]);

        $question->update([
            'question'    => $validated['question'],
            'explanation' => $validated['explanation'] ?? null,
            'points'      => $validated['points'],
        ]);

        // Update options
        if ($question->type === 'multiple_choice' && !empty($validated['options'])) {
            $question->options()->delete();
            $optIdx = 0;
            foreach ($validated['options'] as $i => $opt) {
                if (empty($opt['text'])) continue;
                $question->options()->create([
                    'option_text' => $opt['text'],
                    'is_correct'  => isset($opt['correct']) && $opt['correct'] == '1',
                    'order'       => ++$optIdx,
                ]);
            }
        } elseif ($question->type === 'true_false') {
            $tfAnswer = $request->input('true_false_answer', 'true');
            $question->options()->delete();
            $question->options()->createMany([
                ['option_text' => 'Benar', 'is_correct' => $tfAnswer === 'true',  'order' => 1],
                ['option_text' => 'Salah', 'is_correct' => $tfAnswer === 'false', 'order' => 2],
            ]);
        }

        return redirect()->route('instructor.courses.quizzes.questions', [$course, $quiz])
            ->with('success', 'Soal berhasil diperbarui.');
    }

    public function destroyQuestion(Course $course, Quiz $quiz, QuizQuestion $question)
    {
        $this->authorize('manageQuizzes', $course);
        abort_if($question->quiz_id !== $quiz->id, 403);

        $question->delete();

        // Re-order
        $quiz->questions()->orderBy('order')->each(function ($q, $i) {
            $q->update(['order' => $i + 1]);
        });

        return redirect()->route('instructor.courses.quizzes.questions', [$course, $quiz])
            ->with('success', 'Soal berhasil dihapus.');
    }

    // ══════════════════════════════════════════════════════════════════════════
    // RESULTS — Lihat hasil attempt siswa
    // ══════════════════════════════════════════════════════════════════════════

    public function results(Course $course, Quiz $quiz)
    {
        $this->authorize('manageQuizzes', $course);
        abort_if($quiz->course_id !== $course->id, 403);

        $attempts = $quiz->attempts()
            ->with('user')
            ->whereNotNull('completed_at')
            ->latest('completed_at')
            ->paginate(20);

        $stats = [
            'total_attempts' => $quiz->attempts()->whereNotNull('completed_at')->count(),
            'passed'         => $quiz->attempts()->whereNotNull('completed_at')->where('passed', true)->count(),
            'avg_score'      => (int) $quiz->attempts()->whereNotNull('completed_at')->avg('score'),
        ];

        return view('instructor.quizzes.results', compact('course', 'quiz', 'attempts', 'stats'));
    }

    // ══════════════════════════════════════════════════════════════════════════
    // IMPORT AIKEN — Import soal pilihan ganda dari format Aiken (text/file)
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Tampilkan form import Aiken.
     */
    public function showImport(Course $course, Quiz $quiz)
    {
        $this->authorize('manageQuizzes', $course);
        abort_if($quiz->course_id !== $course->id, 403);

        return view('instructor.quizzes.import', compact('course', 'quiz'));
    }

    /**
     * Proses import Aiken format.
     *
     * Input: 'content' (textarea) atau 'file' (.txt upload).
     * Semua soal di-import sebagai multiple_choice.
     */
    public function import(Course $course, Quiz $quiz, Request $request, AikenQuizParser $parser)
    {
        $this->authorize('manageQuizzes', $course);
        abort_if($quiz->course_id !== $course->id, 403);

        $validated = $request->validate([
            'content'        => 'nullable|string|max:100000',
            'file'           => 'nullable|file|mimes:txt|max:1024',
            'points_per_question' => 'required|integer|min:1|max:100',
            'replace_existing'    => 'nullable|boolean',
        ]);

        // Ambil konten dari file atau textarea
        $content = null;
        if ($request->hasFile('file')) {
            $content = file_get_contents($request->file('file')->getRealPath());
        } elseif (! empty($validated['content'])) {
            $content = $validated['content'];
        }

        if (! $content || trim($content) === '') {
            return back()->withErrors(['content' => 'Isi textarea atau upload file .txt berisi soal Aiken.'])->withInput();
        }

        $parsed = $parser->parse($content);

        if ($parser->hasErrors()) {
            return back()
                ->withErrors(['content' => 'Format Aiken tidak valid:'])
                ->with('parse_errors', $parser->getErrors())
                ->withInput();
        }

        if (empty($parsed)) {
            return back()->withErrors(['content' => 'Tidak ada soal yang ter-parse. Periksa format Aiken.'])->withInput();
        }

        $points           = (int) $validated['points_per_question'];
        $replaceExisting  = $request->boolean('replace_existing');

        DB::transaction(function () use ($quiz, $parsed, $points, $replaceExisting) {
            if ($replaceExisting) {
                // Hapus soal lama (cascade ke options & answers via FK)
                $quiz->questions()->delete();
            }

            $startOrder = $quiz->questions()->max('order') ?? 0;

            foreach ($parsed as $idx => $q) {
                $question = $quiz->questions()->create([
                    'question' => $q['question'],
                    'type'     => 'multiple_choice',
                    'points'   => $points,
                    'order'    => $startOrder + $idx + 1,
                ]);

                foreach ($q['options'] as $optIdx => $opt) {
                    $question->options()->create([
                        'option_text' => $opt['text'],
                        'is_correct'  => $opt['correct'],
                        'order'       => $optIdx + 1,
                    ]);
                }
            }
        });

        $count = count($parsed);
        $msg   = "Berhasil import {$count} soal dari format Aiken.";
        if ($replaceExisting) {
            $msg .= ' Soal lama telah diganti.';
        }

        return redirect()->route('instructor.courses.quizzes.questions', [$course, $quiz])
            ->with('success', $msg);
    }
}
