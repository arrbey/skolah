<?php

namespace App\Http\Middleware;

use App\Models\Course;
use App\Models\CourseEnrollment;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckEnrollment
{
    /**
     * Middleware untuk memastikan user sudah enrolled di course sebelum
     * mengakses learning room. Jika belum enrolled, redirect ke halaman
     * course (untuk purchase).
     *
     * Instruktur & admin dapat mengakses tanpa enrollment.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $slug   = $request->route('slug');
        $course = Course::where('slug', $slug)->firstOrFail();

        $user = $request->user();

        // Admin & instructor (course owner) bypass enrollment check
        if ($user->hasRole('admin')) {
            $request->merge(['course' => $course]);
            return $next($request);
        }

        if ($user->hasRole('instructor') && (int) $course->instructor_id === $user->id) {
            $request->merge(['course' => $course]);
            return $next($request);
        }

        // Cek apakah user sudah terdaftar di course ini
        $enrolled = CourseEnrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->exists();

        if (! $enrolled) {
            return redirect()
                ->route('courses.show', $course->slug)
                ->with('warning', 'Kamu belum terdaftar di kursus ini. Silakan daftar terlebih dahulu.');
        }

        // Tambahkan course ke request agar controller tidak perlu query ulang
        $request->merge(['course' => $course]);

        return $next($request);
    }
}
