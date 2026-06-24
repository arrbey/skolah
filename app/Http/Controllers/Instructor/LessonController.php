<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Instructor\StoreLessonRequest;
use App\Http\Requests\Instructor\StoreSectionRequest;
use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\CourseSection;
use App\Services\MinioStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LessonController extends Controller
{
    /**
     * Halaman kelola sections & lessons untuk sebuah course.
     */
    public function index(Course $course)
    {
        $this->authorize('manageLessons', $course);

        $course->load(['sections.lessons' => fn ($q) => $q->orderBy('order')]);
        $course->loadCount(['enrollments']);

        return view('instructor.courses.lessons', compact('course'));
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  SECTIONS
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Tambah section baru.
     */
    public function storeSection(StoreSectionRequest $request, Course $course)
    {
        $this->authorize('manageLessons', $course);

        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $maxOrder = $course->sections()->max('order') ?? 0;

        $course->sections()->create([
            'title' => $request->title,
            'order' => $maxOrder + 1,
        ]);

        return back()->with('success', 'Section berhasil ditambahkan.');
    }

    /**
     * Update section.
     */
    public function updateSection(StoreSectionRequest $request, Course $course, CourseSection $section)
    {
        $this->authorize('manageLessons', $course);
        $this->ensureSectionBelongsToCourse($section, $course);

        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $section->update(['title' => $request->title]);

        return back()->with('success', 'Section berhasil diperbarui.');
    }

    /**
     * Hapus section beserta semua lessons di dalamnya.
     */
    public function destroySection(Course $course, CourseSection $section)
    {
        $this->authorize('manageLessons', $course);
        $this->ensureSectionBelongsToCourse($section, $course);

        $section->lessons()->delete();
        $section->delete();

        return back()->with('success', 'Section dan semua lesson di dalamnya berhasil dihapus.');
    }

    /**
     * Reorder sections via AJAX.
     */
    public function reorderSections(Request $request, Course $course)
    {
        $this->authorize('manageLessons', $course);

        $request->validate([
            'order'   => 'required|array',
            'order.*' => 'integer|exists:course_sections,id',
        ]);

        foreach ($request->order as $index => $sectionId) {
            CourseSection::where('id', $sectionId)
                ->where('course_id', $course->id)
                ->update(['order' => $index + 1]);
        }

        return response()->json(['message' => 'Urutan section berhasil diperbarui.']);
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  LESSONS
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Tambah lesson baru ke section.
     */
    public function storeLesson(StoreLessonRequest $request, Course $course, CourseSection $section)
    {
        $this->authorize('manageLessons', $course);
        $this->ensureSectionBelongsToCourse($section, $course);

        $request->validate([
            'title'                  => 'required|string|max:255',
            'video_type'             => 'required|in:youtube,minio',
            'video_url'              => 'nullable|url|required_if:video_type,youtube',
            'video_file'             => 'nullable|file|mimes:mp4,mov,avi|max:2097152|required_if:video_type,minio',
            'video_duration'         => 'nullable|integer|min:0',
            'video_duration_seconds' => 'nullable|integer|min:0',
            'content'                => 'nullable|string',
            'is_free_preview'        => 'nullable|boolean',
            'is_published'           => 'nullable|boolean',
        ]);

        $maxOrder  = $section->lessons()->max('order') ?? 0;
        $videoType = $request->video_type ?? 'youtube';
        $videoUrl  = null;
        $fileSize  = null;

        // Simpan ID lesson sementara dengan dummy data untuk mendapat ID
        $lesson = $section->lessons()->create([
            'title'                  => $request->title,
            'video_type'             => $videoType,
            'video_url'              => null,
            'video_duration'         => $request->video_duration ?? 0,
            'video_duration_seconds' => $request->video_duration_seconds ?? 0,
            'content'                => $request->input('content'),
            'is_free_preview'        => $request->boolean('is_free_preview'),
            'is_published'           => $request->boolean('is_published', true),
            'order'                  => $maxOrder + 1,
        ]);

        if ($videoType === 'minio' && $request->hasFile('video_file')) {
            // Simpan lokal sementara dengan nama hash-only; jangan pakai original filename
            $file = $request->file('video_file');
            $extension = strtolower($file->guessExtension() ?: $file->extension() ?: 'mp4');
            $localPath = $file->storeAs('temp', Str::uuid()->toString() . '.' . $extension, 'local');
            $absoluteLocalPath = storage_path('app/' . $localPath);
            
            // Dispatch FFMPEG compress job ke belakang layar
            \App\Jobs\CompressVideoJob::dispatch($lesson->id, $course->id, $absoluteLocalPath);
            
            // Tandai diproses
            $lesson->update(['processing_status' => 'processing']);
        } elseif ($videoType === 'youtube') {
            $lesson->update(['video_url' => $request->video_url, 'processing_status' => 'ready']);
        }

        return back()->with('success', 'Lesson berhasil ditambahkan. Khusus video akan diproses di latar belakang beberapa menit.');
    }

    /**
     * Update lesson.
     */
    public function updateLesson(StoreLessonRequest $request, Course $course, CourseLesson $lesson)
    {
        $this->authorize('manageLessons', $course);
        $this->ensureLessonBelongsToCourse($lesson, $course);

        $request->validate([
            'title'                  => 'required|string|max:255',
            'video_type'             => 'required|in:youtube,minio',
            'video_url'              => 'nullable|url|required_if:video_type,youtube',
            'video_file'             => 'nullable|file|mimes:mp4,mov,avi|max:2097152',
            'video_duration'         => 'nullable|integer|min:0',
            'video_duration_seconds' => 'nullable|integer|min:0',
            'content'                => 'nullable|string',
            'is_free_preview'        => 'nullable|boolean',
            'is_published'           => 'nullable|boolean',
        ]);

        $videoType = $request->video_type ?? $lesson->video_type ?? 'youtube';
        $updateData = [
            'title'                  => $request->title,
            'video_type'             => $videoType,
            'video_duration'         => $request->video_duration ?? 0,
            'video_duration_seconds' => $request->video_duration_seconds ?? 0,
            'content'                => $request->input('content'),
            'is_free_preview'        => $request->boolean('is_free_preview'),
            'is_published'           => $request->boolean('is_published', true),
        ];

        if ($videoType === 'minio' && $request->hasFile('video_file')) {
            // Hapus video lama dari MinIO jika ada
            if ($lesson->isMinioVideo() && $lesson->video_url) {
                app(MinioStorageService::class)->deleteLmsVideo($lesson->video_url);
                $updateData['video_url'] = null;
                $updateData['video_file_size'] = null;
            }
            
            // Simpan lokal sementara dengan nama hash-only; jangan pakai original filename
            $file = $request->file('video_file');
            $extension = strtolower($file->guessExtension() ?: $file->extension() ?: 'mp4');
            $localPath = $file->storeAs('temp', Str::uuid()->toString() . '.' . $extension, 'local');
            $absoluteLocalPath = storage_path('app/' . $localPath);
            
            // Ubah status
            $updateData['processing_status'] = 'processing';
            $lesson->update($updateData);

            \App\Jobs\CompressVideoJob::dispatch($lesson->id, $course->id, $absoluteLocalPath);

        } elseif ($videoType === 'youtube') {
            // Hapus video MinIO lama jika sebelumnya minio
            if ($lesson->isMinioVideo() && $lesson->video_url) {
                app(MinioStorageService::class)->deleteLmsVideo($lesson->video_url);
            }
            $updateData['video_url']       = $request->video_url;
            $updateData['video_file_size'] = null;
            $updateData['processing_status'] = 'ready';
            $lesson->update($updateData);
        } else {
             // Jika pengguna hanya update teks saja tapi jenisnya tetap minio
             $lesson->update($updateData);
        }

        return back()->with('success', 'Lesson berhasil diperbarui. Video spesifik (jika ada) sedang diproses.');
    }

    /**
     * Hapus lesson.
     */
    public function destroyLesson(Course $course, CourseLesson $lesson)
    {
        $this->authorize('manageLessons', $course);
        $this->ensureLessonBelongsToCourse($lesson, $course);

        // Hapus video MinIO jika ada
        if ($lesson->isMinioVideo() && $lesson->video_url) {
            app(MinioStorageService::class)->deleteLmsVideo($lesson->video_url);
        }

        $lesson->delete();

        return back()->with('success', 'Lesson berhasil dihapus.');
    }

    /**
     * Reorder lessons dalam section via AJAX.
     */
    public function reorderLessons(Request $request, Course $course, CourseSection $section)
    {
        $this->authorize('manageLessons', $course);
        $this->ensureSectionBelongsToCourse($section, $course);

        $request->validate([
            'order'   => 'required|array',
            'order.*' => 'integer|exists:course_lessons,id',
        ]);

        foreach ($request->order as $index => $lessonId) {
            CourseLesson::where('id', $lessonId)
                ->where('section_id', $section->id)
                ->update(['order' => $index + 1]);
        }

        return response()->json(['message' => 'Urutan lesson berhasil diperbarui.']);
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  HELPERS
    // ══════════════════════════════════════════════════════════════════════════

    private function ensureSectionBelongsToCourse(CourseSection $section, Course $course): void
    {
        if ($section->course_id !== $course->id) {
            abort(404);
        }
    }

    private function ensureLessonBelongsToCourse(CourseLesson $lesson, Course $course): void
    {
        $lesson->loadMissing('section:id,course_id');

        if (! $lesson->section || $lesson->section->course_id !== $course->id) {
            abort(404);
        }
    }
}
