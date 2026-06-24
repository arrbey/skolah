<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAnswer;
use Carbon\Carbon;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    // ══════════════════════════════════════════════════════════════════════════
    // SHOW — Halaman intro + mulai quiz
    // ══════════════════════════════════════════════════════════════════════════

    public function show(Course $course, Quiz $quiz)
    {
        $this->checkEnrolled($course);
        abort_if($quiz->course_id !== $course->id, 404);
        abort_if(!$quiz->is_active, 403, 'Quiz ini belum aktif.');

        $user       = auth()->user();
        $lastAttempt = $quiz->latestAttemptByUser($user->id);
        $alreadyDone = $lastAttempt && $lastAttempt->completed_at;

        return view('quiz.show', compact('course', 'quiz', 'lastAttempt', 'alreadyDone'));
    }

    // ══════════════════════════════════════════════════════════════════════════
    // START — Buat attempt baru & redirect ke halaman soal
    // ══════════════════════════════════════════════════════════════════════════

    public function start(Course $course, Quiz $quiz)
    {
        $this->checkEnrolled($course);
        abort_if($quiz->course_id !== $course->id, 404);
        abort_if(!$quiz->is_active, 403);

        $user = auth()->user();

        // Buat attempt baru
        $attempt = QuizAttempt::create([
            'user_id'      => $user->id,
            'quiz_id'      => $quiz->id,
            'score'        => 0,
            'total_points' => $quiz->total_points,
            'earned_points'=> 0,
            'passed'       => false,
            'started_at'   => now(),
        ]);

        return redirect()->route('quiz.attempt', [$course, $quiz, $attempt]);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // ATTEMPT — Halaman pengerjaan quiz
    // ══════════════════════════════════════════════════════════════════════════

    public function attempt(Course $course, Quiz $quiz, QuizAttempt $attempt)
    {
        $this->checkEnrolled($course);
        abort_if($quiz->course_id !== $course->id, 404);
        abort_if($attempt->quiz_id !== $quiz->id, 404);
        abort_if($attempt->user_id !== auth()->id(), 403);
        abort_if($attempt->completed_at !== null, 403, 'Attempt ini sudah selesai.');

        // Cek timeout
        if ($quiz->time_limit && $attempt->started_at->addMinutes($quiz->time_limit)->isPast()) {
            $this->autoSubmit($attempt, $quiz);
            return redirect()->route('quiz.result', [$course, $quiz, $attempt]);
        }

        $questions = $quiz->randomize_questions
            ? $quiz->questions()->with('options')->get()->shuffle()
            : $quiz->questions()->with('options')->orderBy('order')->get();

        $timeLeftSeconds = null;
        if ($quiz->time_limit) {
            $deadline        = $attempt->started_at->addMinutes($quiz->time_limit);
            $timeLeftSeconds = max(0, now()->diffInSeconds($deadline, false));
        }

        return view('quiz.attempt', compact('course', 'quiz', 'attempt', 'questions', 'timeLeftSeconds'));
    }

    // ══════════════════════════════════════════════════════════════════════════
    // SUBMIT — Simpan jawaban & hitung skor
    // ══════════════════════════════════════════════════════════════════════════

    public function submit(Course $course, Quiz $quiz, QuizAttempt $attempt, Request $request)
    {
        $this->checkEnrolled($course);
        abort_if($quiz->course_id !== $course->id, 404);
        abort_if($attempt->quiz_id !== $quiz->id, 404);
        abort_if($attempt->user_id !== auth()->id(), 403);
        abort_if($attempt->completed_at !== null, 403);

        $questions = $quiz->questions()->with('options')->get();
        $answers   = $request->input('answers', []);

        $earnedPoints = 0;
        $totalPoints  = 0;

        foreach ($questions as $question) {
            $totalPoints += $question->points;
            $userAnswer  = $answers[$question->id] ?? null;

            if ($question->isEssay()) {
                // Essay tidak auto-grade
                QuizAnswer::create([
                    'attempt_id'        => $attempt->id,
                    'question_id'       => $question->id,
                    'selected_option_id'=> null,
                    'answer_text'       => $userAnswer,
                    'is_correct'        => null,
                ]);
                continue;
            }

            // Pilihan ganda & benar/salah
            $selectedOption = null;
            $isCorrect      = false;

            if ($userAnswer) {
                $selectedOption = $question->options->firstWhere('id', (int) $userAnswer);
                if ($selectedOption && $selectedOption->is_correct) {
                    $isCorrect    = true;
                    $earnedPoints += $question->points;
                }
            }

            QuizAnswer::create([
                'attempt_id'        => $attempt->id,
                'question_id'       => $question->id,
                'selected_option_id'=> $selectedOption?->id,
                'answer_text'       => null,
                'is_correct'        => $isCorrect,
            ]);
        }

        // Hitung skor (hindari division by zero)
        $score = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100) : 0;
        $passed = $score >= $quiz->passing_score;

        $attempt->update([
            'score'         => $score,
            'total_points'  => $totalPoints,
            'earned_points' => $earnedPoints,
            'passed'        => $passed,
            'completed_at'  => now(),
        ]);

        return redirect()->route('quiz.result', [$course, $quiz, $attempt]);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // RESULT — Tampilkan hasil quiz
    // ══════════════════════════════════════════════════════════════════════════

    public function result(Course $course, Quiz $quiz, QuizAttempt $attempt)
    {
        $this->checkEnrolled($course);
        abort_if($quiz->course_id !== $course->id, 404);
        abort_if($attempt->quiz_id !== $quiz->id, 404);
        abort_if($attempt->user_id !== auth()->id(), 403);
        abort_if($attempt->completed_at === null, 403, 'Quiz belum selesai.');

        $answers = $attempt->answers()
            ->with(['question.options', 'selectedOption'])
            ->get();

        return view('quiz.result', compact('course', 'quiz', 'attempt', 'answers'));
    }

    // ══════════════════════════════════════════════════════════════════════════
    // PRIVATE
    // ══════════════════════════════════════════════════════════════════════════

    private function checkEnrolled(Course $course): void
    {
        $enrolled = CourseEnrollment::where('user_id', auth()->id())
            ->where('course_id', $course->id)
            ->exists();
        abort_if(!$enrolled, 403, 'Anda belum terdaftar di kursus ini.');
    }

    private function autoSubmit(QuizAttempt $attempt, Quiz $quiz): void
    {
        if ($attempt->completed_at) return;

        $existingAnswerQuestionIds = $attempt->answers()->pluck('question_id');
        $questions = $quiz->questions()
            ->whereNotIn('id', $existingAnswerQuestionIds)
            ->get();

        // Simpan jawaban kosong untuk soal yang belum dijawab
        foreach ($questions as $question) {
            QuizAnswer::create([
                'attempt_id'        => $attempt->id,
                'question_id'       => $question->id,
                'selected_option_id'=> null,
                'answer_text'       => null,
                'is_correct'        => $question->isEssay() ? null : false,
            ]);
        }

        // Hitung dari jawaban yang sudah ada
        $earnedPoints = $attempt->answers()
            ->where('is_correct', true)
            ->join('quiz_questions', 'quiz_answers.question_id', '=', 'quiz_questions.id')
            ->sum('quiz_questions.points');

        $totalPoints = $quiz->total_points;
        $score = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100) : 0;

        $attempt->update([
            'score'         => $score,
            'total_points'  => $totalPoints,
            'earned_points' => $earnedPoints,
            'passed'        => $score >= $quiz->passing_score,
            'completed_at'  => now(),
        ]);
    }
}
