<?php

namespace App\Livewire;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseLesson;
use App\Models\LessonProgress;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class LessonProgressComponent extends Component
{
    // ── State Properties ──────────────────────────────────────────────────────

    public Course       $course;
    public CourseLesson $currentLesson;

    /** ID semua lesson yang sudah completed oleh user ini */
    public array $completedLessons = [];

    /** Persentase progress keseluruhan (0–100) */
    public int $progressPercentage = 0;

    /** Total lesson di course ini */
    public int $totalLessons = 0;

    /** Apakah lesson saat ini sudah completed */
    public bool $isCurrentCompleted = false;

    /** Apakah sertifikat sudah tersedia */
    public bool $hasCertificate = false;

    // ── Mount ────────────────────────────────────────────────────────────────

    public function mount(Course $course, CourseLesson $currentLesson): void
    {
        $this->course        = $course;
        $this->currentLesson = $currentLesson;

        $this->loadProgress();
    }

    // ── Load / Refresh Progress ───────────────────────────────────────────────

    protected function loadProgress(): void
    {
        $userId = Auth::id();

        // Semua lesson ID di course ini (across semua sections)
        $allLessonIds = CourseLesson::whereHas('section', function ($q) {
            $q->where('course_id', $this->course->id);
        })
        ->where('is_published', true)
        ->pluck('id')
        ->toArray();

        $this->totalLessons = count($allLessonIds);

        // Lesson yang sudah completed oleh user
        $this->completedLessons = LessonProgress::where('user_id', $userId)
            ->whereIn('lesson_id', $allLessonIds)
            ->where('is_completed', true)
            ->pluck('lesson_id')
            ->toArray();

        // Hitung persentase
        $this->progressPercentage = $this->totalLessons > 0
            ? (int) round((count($this->completedLessons) / $this->totalLessons) * 100)
            : 0;

        // Status lesson saat ini
        $this->isCurrentCompleted = in_array($this->currentLesson->id, $this->completedLessons);

        // Cek sertifikat
        $this->hasCertificate = Certificate::where('user_id', $userId)
            ->where('course_id', $this->course->id)
            ->exists();
    }

    // ── Actions ───────────────────────────────────────────────────────────────

    /**
     * Tandai lesson saat ini sebagai selesai / belum selesai (toggle).
     */
    public function toggleComplete(): void
    {
        $userId   = Auth::id();
        $lessonId = $this->currentLesson->id;

        $progress = LessonProgress::firstOrCreate(
            ['user_id' => $userId, 'lesson_id' => $lessonId],
            ['is_completed' => false, 'watched_at' => null]
        );

        // Toggle
        $progress->update([
            'is_completed' => ! $progress->is_completed,
            'watched_at'   => ! $progress->is_completed ? now() : null,
        ]);

        // Refresh state
        $this->loadProgress();

        $this->updateEnrollmentAndDispatch($userId, $lessonId);
    }

    /**
     * Tandai lesson saat ini sebagai selesai (dipanggil dari YouTube auto-tracking).
     * Hanya akan selesai, tidak bisa di-untoggle dari sini.
     */
    #[On('mark-complete')]
    public function markComplete(): void
    {
        $userId   = Auth::id();
        $lessonId = $this->currentLesson->id;

        // Jika sudah selesai, tidak perlu diproses lagi
        if ($this->isCurrentCompleted) {
            return;
        }

        LessonProgress::updateOrCreate(
            ['user_id' => $userId, 'lesson_id' => $lessonId],
            ['is_completed' => true, 'watched_at' => now()]
        );

        // Refresh state
        $this->loadProgress();

        $this->updateEnrollmentAndDispatch($userId, $lessonId);
    }

    /**
     * Update enrollment progress dan dispatch event Alpine/Livewire.
     */
    private function updateEnrollmentAndDispatch(int $userId, int $lessonId): void
    {
        // Update enrollment progress_percentage
        $enrollment = CourseEnrollment::where('user_id', $userId)
            ->where('course_id', $this->course->id)
            ->first();

        if ($enrollment) {
            $completed = $this->progressPercentage >= 100;
            $enrollment->update([
                'progress_percentage' => $this->progressPercentage,
                'completed_at'        => $completed ? ($enrollment->completed_at ?? now()) : null,
            ]);
        }

        // Emit event ke Alpine.js agar sidebar bisa update tanpa reload
        $this->dispatch('progress-updated', [
            'lessonId'   => $lessonId,
            'completed'  => $this->isCurrentCompleted,
            'percentage' => $this->progressPercentage,
        ]);

        // Notify jika baru saja 100%
        if ($this->progressPercentage >= 100 && ! $this->hasCertificate) {
            $this->dispatch('course-completed');
        }
    }

    /**
     * Pindah ke lesson berikutnya (urutan berdasarkan section order + lesson order).
     */
    public function goToNextLesson(): void
    {
        $nextLesson = $this->getAdjacentLesson('next');

        if ($nextLesson) {
            $this->redirect(route('learn.lesson', [
                'slug'     => $this->course->slug,
                'lessonId' => $nextLesson->id,
            ]));
        }
    }

    /**
     * Pindah ke lesson sebelumnya.
     */
    public function goToPrevLesson(): void
    {
        $prevLesson = $this->getAdjacentLesson('prev');

        if ($prevLesson) {
            $this->redirect(route('learn.lesson', [
                'slug'     => $this->course->slug,
                'lessonId' => $prevLesson->id,
            ]));
        }
    }

    /**
     * Dapatkan lesson sebelum/sesudah lesson saat ini berdasarkan urutan global.
     *
     * @param 'next'|'prev' $direction
     */
    protected function getAdjacentLesson(string $direction): ?CourseLesson
    {
        // Ambil semua lesson terurut (section.order, lesson.order)
        $allLessons = CourseLesson::whereHas('section', function ($q) {
            $q->where('course_id', $this->course->id)->orderBy('order');
        })
        ->where('is_published', true)
        ->with(['section' => fn($q) => $q->orderBy('order')])
        ->get()
        ->sortBy(fn($lesson) => [$lesson->section->order, $lesson->order])
        ->values();

        $currentIndex = $allLessons->search(fn($l) => $l->id === $this->currentLesson->id);

        if ($currentIndex === false) {
            return null;
        }

        $targetIndex = $direction === 'next' ? $currentIndex + 1 : $currentIndex - 1;

        return $allLessons->get($targetIndex);
    }

    // ── Computed Helpers ─────────────────────────────────────────────────────

    public function getNextLessonProperty(): ?CourseLesson
    {
        return $this->getAdjacentLesson('next');
    }

    public function getPrevLessonProperty(): ?CourseLesson
    {
        return $this->getAdjacentLesson('prev');
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.lesson-progress', [
            'nextLesson'  => $this->nextLesson,
            'prevLesson'  => $this->prevLesson,
            'isCompleted' => $this->hasCertificate,
        ]);
    }
}
