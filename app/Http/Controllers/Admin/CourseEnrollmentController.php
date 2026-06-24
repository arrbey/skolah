<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\User;
use App\Services\CourseEnrollmentService;
use Illuminate\Http\Request;

/**
 * Manajemen enrollment manual oleh admin.
 *
 * Use case utama:
 * - User membeli course via offline (transfer manual, event, corporate deal)
 *   dan admin perlu mendaftarkannya ke course secara manual.
 * - Unenroll user dari course jika terjadi kesalahan atau pembatalan.
 */
class CourseEnrollmentController extends Controller
{
    public function __construct(protected CourseEnrollmentService $enrollmentService)
    {
    }

    /**
     * Daftar user yang enrolled di sebuah course + form tambah.
     */
    public function index(Request $request, Course $course)
    {
        $course->load(['variants' => fn ($q) => $q->active()->ordered()]);

        $query = $course->enrollments()
            ->with(['user', 'variant']);

        if ($search = $request->input('search')) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $enrollments = $query->latest('enrolled_at')->paginate(20)->withQueryString();

        $stats = [
            'total'       => $course->enrollments()->count(),
            'completed'   => $course->enrollments()->completed()->count(),
            'in_progress' => $course->enrollments()->inProgress()->count(),
            'not_started' => $course->enrollments()->notStarted()->count(),
        ];

        return view('admin.courses.enrollments', compact('course', 'enrollments', 'stats'));
    }

    /**
     * Tambah enrollment manual.
     */
    public function store(Request $request, Course $course)
    {
        $data = $request->validate([
            'user_id'           => ['required', 'exists:users,id'],
            'course_variant_id' => ['nullable', 'exists:course_variants,id'],
            'send_notification' => ['nullable', 'boolean'],
        ]);

        // Validasi variant milik course ini
        if (!empty($data['course_variant_id'])) {
            $validVariant = $course->variants()
                ->where('id', $data['course_variant_id'])
                ->exists();

            if (! $validVariant) {
                return back()->with('error', 'Varian yang dipilih bukan milik kursus ini.');
            }
        }

        $user = User::findOrFail($data['user_id']);

        $result = $this->enrollmentService->manualEnroll(
            user: $user,
            course: $course,
            variantId: $data['course_variant_id'] ?? null,
            sendNotification: (bool) ($data['send_notification'] ?? true),
        );

        if (! $result['created']) {
            return back()->with('error', "User \"{$user->name}\" sudah terdaftar di kursus ini.");
        }

        return back()->with('success', "User \"{$user->name}\" berhasil didaftarkan ke kursus.");
    }

    /**
     * Hapus enrollment (unenroll user dari course).
     */
    public function destroy(Course $course, CourseEnrollment $enrollment)
    {
        if ($enrollment->course_id !== $course->id) {
            abort(404);
        }

        $userName = $enrollment->user?->name ?? 'User';

        $this->enrollmentService->manualUnenroll($enrollment);

        return back()->with('success', "Enrollment \"{$userName}\" berhasil dihapus.");
    }

    /**
     * Endpoint JSON untuk autocomplete user (dipakai di Select2 / Alpine).
     */
    public function searchUsers(Request $request, Course $course)
    {
        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        // Exclude user yang sudah terdaftar
        $enrolledIds = $course->enrollments()->pluck('user_id')->toArray();

        $users = User::where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->whereNotIn('id', $enrolledIds)
            ->whereNull('suspended_at')
            ->limit(15)
            ->get(['id', 'name', 'email', 'avatar']);

        return response()->json($users->map(fn ($u) => [
            'id'     => $u->id,
            'name'   => $u->name,
            'email'  => $u->email,
            'avatar' => $u->avatar_url,
        ]));
    }
}
