<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Bootcamp;
use App\Models\Book;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Instructor dashboard — stats overview.
     */
    public function index(Request $request)
    {
        $instructorId = auth()->id();

        // ── Stats Cards ──────────────────────────────────────────────────────
        $totalCourses   = Course::where('instructor_id', $instructorId)->count();
        $publishedCourses = Course::where('instructor_id', $instructorId)->published()->count();
        $totalBootcamps = Bootcamp::where('instructor_id', $instructorId)->count();
        $totalBooks     = Book::where('instructor_id', $instructorId)->count();

        $totalStudents = CourseEnrollment::whereHas('course', function ($q) use ($instructorId) {
            $q->where('instructor_id', $instructorId);
        })->distinct('user_id')->count('user_id');

        // ── Earnings ─────────────────────────────────────────────────────────
        // Total earnings dari order items yang merujuk course/bootcamp/book milik instructor
        $totalEarnings = $this->calculateTotalEarnings($instructorId);
        $monthlyEarnings = $this->calculateMonthlyEarnings($instructorId);

        // ── Recent Enrollments ───────────────────────────────────────────────
        $recentEnrollments = CourseEnrollment::with(['user', 'course'])
            ->whereHas('course', fn ($q) => $q->where('instructor_id', $instructorId))
            ->latest('enrolled_at')
            ->take(8)
            ->get();

        // ── Monthly Earnings Chart (last 6 months) ──────────────────────────
        $earningsChart = $this->getEarningsChart($instructorId, 6);

        // ── Popular Courses ─────────────────────────────────────────────────
        $popularCourses = Course::where('instructor_id', $instructorId)
            ->published()
            ->orderByDesc('total_students')
            ->take(5)
            ->get();

        return view('instructor.dashboard', compact(
            'totalCourses',
            'publishedCourses',
            'totalBootcamps',
            'totalBooks',
            'totalStudents',
            'totalEarnings',
            'monthlyEarnings',
            'recentEnrollments',
            'earningsChart',
            'popularCourses',
        ));
    }

    /**
     * Hitung total earnings instructor dari paid orders.
     */
    private function calculateTotalEarnings(int $instructorId): int
    {
        return (int) OrderItem::whereHas('order', fn ($q) => $q->paid())
            ->where(function ($q) use ($instructorId) {
                $q->where(function ($q2) use ($instructorId) {
                    $q2->where('itemable_type', Course::class)
                        ->whereIn('itemable_id', Course::where('instructor_id', $instructorId)->pluck('id'));
                })->orWhere(function ($q2) use ($instructorId) {
                    $q2->where('itemable_type', Bootcamp::class)
                        ->whereIn('itemable_id', Bootcamp::where('instructor_id', $instructorId)->pluck('id'));
                })->orWhere(function ($q2) use ($instructorId) {
                    $q2->where('itemable_type', Book::class)
                        ->whereIn('itemable_id', Book::where('instructor_id', $instructorId)->pluck('id'));
                });
            })
            ->sum(DB::raw('price * quantity'));
    }

    /**
     * Hitung earnings bulan ini.
     */
    private function calculateMonthlyEarnings(int $instructorId): int
    {
        return (int) OrderItem::whereHas('order', fn ($q) => $q->paid()->where('paid_at', '>=', now()->startOfMonth()))
            ->where(function ($q) use ($instructorId) {
                $q->where(function ($q2) use ($instructorId) {
                    $q2->where('itemable_type', Course::class)
                        ->whereIn('itemable_id', Course::where('instructor_id', $instructorId)->pluck('id'));
                })->orWhere(function ($q2) use ($instructorId) {
                    $q2->where('itemable_type', Bootcamp::class)
                        ->whereIn('itemable_id', Bootcamp::where('instructor_id', $instructorId)->pluck('id'));
                })->orWhere(function ($q2) use ($instructorId) {
                    $q2->where('itemable_type', Book::class)
                        ->whereIn('itemable_id', Book::where('instructor_id', $instructorId)->pluck('id'));
                });
            })
            ->sum(DB::raw('price * quantity'));
    }

    /**
     * Data chart earnings per bulan.
     */
    private function getEarningsChart(int $instructorId, int $months = 6): array
    {
        $labels = [];
        $data   = [];

        $courseIds   = Course::where('instructor_id', $instructorId)->pluck('id');
        $bootcampIds = Bootcamp::where('instructor_id', $instructorId)->pluck('id');
        $bookIds    = Book::where('instructor_id', $instructorId)->pluck('id');

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = $date->translatedFormat('M Y');

            $earning = (int) OrderItem::whereHas('order', function ($q) use ($date) {
                $q->paid()
                  ->whereYear('paid_at', $date->year)
                  ->whereMonth('paid_at', $date->month);
            })->where(function ($q) use ($courseIds, $bootcampIds, $bookIds) {
                $q->where(function ($q2) use ($courseIds) {
                    $q2->where('itemable_type', Course::class)->whereIn('itemable_id', $courseIds);
                })->orWhere(function ($q2) use ($bootcampIds) {
                    $q2->where('itemable_type', Bootcamp::class)->whereIn('itemable_id', $bootcampIds);
                })->orWhere(function ($q2) use ($bookIds) {
                    $q2->where('itemable_type', Book::class)->whereIn('itemable_id', $bookIds);
                });
            })->sum(DB::raw('price * quantity'));

            $data[] = $earning;
        }

        return ['labels' => $labels, 'data' => $data];
    }
}
