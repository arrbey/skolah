<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\CourseSection;
use App\Models\LessonProgress;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\TwitterCard;
use Illuminate\Http\Request;

class LearnController extends Controller
{
    /**
     * Learning room — tampilkan lesson pertama atau lesson yang dipilih.
     *
     * Route: GET /learn/{slug}
     * Route: GET /learn/{slug}/lessons/{lessonId}
     */
    public function show(Request $request, string $slug, ?int $lessonId = null)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        // Load course beserta sections & lessons (ordered)
        $course = Course::where('slug', $slug)
            ->with([
                'instructor:id,name,avatar,bio',
                'sections' => fn($q) => $q->orderBy('order'),
                'sections.lessons' => fn($q) => $q->where('is_published', true)->orderBy('order'),
                'pretest',
                'posttest',
            ])
            ->firstOrFail();

        // Semua lesson (flat list, ordered globally)
        $allLessons = $course->sections->flatMap(
            fn($section) => $section->lessons
        );

        // Tentukan lesson aktif
        if ($lessonId) {
            $currentLesson = CourseLesson::findOrFail($lessonId);

            // Pastikan lesson memang milik course ini
            abort_unless(
                $currentLesson->section->course_id === $course->id,
                404
            );
        } else {
            // Default: lesson pertama
            $currentLesson = $allLessons->first();

            if (! $currentLesson) {
                return redirect()->route('courses.show', $course->slug)
                    ->with('info', 'Kursus ini belum memiliki materi.');
            }

            // Redirect ke URL dengan lesson ID agar bisa di-bookmark
            return redirect()->route('learn.lesson', [
                'slug'     => $course->slug,
                'lessonId' => $currentLesson->id,
            ]);
        }

        // Progress data untuk user ini
        $allLessonIds = $allLessons->pluck('id')->toArray();

        $completedLessonIds = LessonProgress::where('user_id', $user->id)
            ->whereIn('lesson_id', $allLessonIds)
            ->where('is_completed', true)
            ->pluck('lesson_id')
            ->toArray();

        $totalLessons    = count($allLessonIds);
        $completedCount  = count($completedLessonIds);
        $progressPercent = $totalLessons > 0
            ? (int) round(($completedCount / $totalLessons) * 100)
            : 0;

        // Prev / Next navigation
        $allLessonsIndexed = $allLessons->values();
        $currentIndex      = $allLessonsIndexed->search(fn($l) => $l->id === $currentLesson->id);
        $prevLesson        = $currentIndex > 0 ? $allLessonsIndexed->get($currentIndex - 1) : null;
        $nextLesson        = $allLessonsIndexed->get($currentIndex + 1);

        // SEO — noindex karena halaman belajar memerlukan autentikasi
        $seoTitle = $currentLesson->title . ' — ' . $course->title . ' | ' . \App\Models\Setting::get('site_name', 'Skolah.com');
        $seoDesc  = 'Belajar ' . $course->title . ' di ' . \App\Models\Setting::get('site_name', 'Skolah.com') . '. Pelajaran: ' . $currentLesson->title;
        $seoImage = $course->thumbnail_url ?? asset('images/og-courses.jpg');

        SEOMeta::setTitle($seoTitle);
        SEOMeta::setDescription($seoDesc);
        SEOMeta::addMeta('robots', 'noindex, nofollow');

        OpenGraph::setTitle($course->title);
        OpenGraph::setDescription($seoDesc);
        OpenGraph::addImage($seoImage, ['width' => 1200, 'height' => 630]);
        OpenGraph::addProperty('type', 'website');
        OpenGraph::setSiteName(\App\Models\Setting::get('site_name', \App\Models\Setting::get('site_name', 'Skolah.com')));

        TwitterCard::setType('summary_large_image');
        TwitterCard::setTitle($seoTitle);
        TwitterCard::setDescription($seoDesc);
        TwitterCard::setImage($seoImage);

        return view('learn.show', compact(
            'course',
            'currentLesson',
            'completedLessonIds',
            'progressPercent',
            'totalLessons',
            'completedCount',
            'prevLesson',
            'nextLesson',
        ));
    }
}
