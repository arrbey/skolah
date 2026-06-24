<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Bootcamp;
use App\Models\Course;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EarningController extends Controller
{
    /**
     * Halaman laporan pendapatan instructor.
     */
    public function index(Request $request)
    {
        $instructorId = auth()->id();

        $year         = $request->input('year', now()->year);
        $selectedYear = $year;
        $availableYears = $this->getAvailableYears($instructorId);

        if (empty($availableYears)) {
            $availableYears = [(int) now()->year];
        }

        $courseIds   = Course::where('instructor_id', $instructorId)->pluck('id');
        $bootcampIds = Bootcamp::where('instructor_id', $instructorId)->pluck('id');
        $bookIds    = Book::where('instructor_id', $instructorId)->pluck('id');

        // ── Monthly Earnings ─────────────────────────────────────────────────
        $monthlyData = [];
        $chartLabels = [];
        $chartData = [];

        for ($month = 1; $month <= 12; $month++) {
            $earning = $this->getEarningForMonth($year, $month, $courseIds, $bootcampIds, $bookIds);
            $transactions = $this->getTransactionCountForMonth($year, $month, $courseIds, $bootcampIds, $bookIds);

            $label = \Carbon\Carbon::create($year, $month)->translatedFormat('F');
            $chartLabels[] = $label;
            $chartData[] = $earning;

            $monthlyData[] = [
                'month'        => $label,
                'month_num'    => $month,
                'earning'      => $earning,
                'transactions' => $transactions,
            ];
        }

        // ── Summary ──────────────────────────────────────────────────────────
        $totalYearEarning = array_sum($chartData);
        $totalAllTime     = $this->calculateTotalEarnings($courseIds, $bootcampIds, $bookIds);
        $thisMonthEarning = $this->getEarningForMonth(now()->year, now()->month, $courseIds, $bootcampIds, $bookIds);

        // ── By Product Type ──────────────────────────────────────────────────
        $earningByCourse = $this->getEarningByType(Course::class, $courseIds, $year);
        $earningByBootcamp = $this->getEarningByType(Bootcamp::class, $bootcampIds, $year);
        $earningByBook = $this->getEarningByType(Book::class, $bookIds, $year);

        return view('instructor.earnings', compact(
            'year',
            'selectedYear',
            'availableYears',
            'monthlyData',
            'chartLabels',
            'chartData',
            'totalYearEarning',
            'totalAllTime',
            'thisMonthEarning',
            'earningByCourse',
            'earningByBootcamp',
            'earningByBook',
        ));
    }

    private function getEarningForMonth(int $year, int $month, $courseIds, $bootcampIds, $bookIds): int
    {
        return (int) OrderItem::whereHas('order', function ($q) use ($year, $month) {
            $q->paid()
              ->whereYear('paid_at', $year)
              ->whereMonth('paid_at', $month);
        })->where(function ($q) use ($courseIds, $bootcampIds, $bookIds) {
            $q->where(function ($q2) use ($courseIds) {
                $q2->where('itemable_type', Course::class)->whereIn('itemable_id', $courseIds);
            })->orWhere(function ($q2) use ($bootcampIds) {
                $q2->where('itemable_type', Bootcamp::class)->whereIn('itemable_id', $bootcampIds);
            })->orWhere(function ($q2) use ($bookIds) {
                $q2->where('itemable_type', Book::class)->whereIn('itemable_id', $bookIds);
            });
        })->sum(DB::raw('price * quantity'));
    }

    private function getTransactionCountForMonth(int $year, int $month, $courseIds, $bootcampIds, $bookIds): int
    {
        return OrderItem::whereHas('order', function ($q) use ($year, $month) {
            $q->paid()
              ->whereYear('paid_at', $year)
              ->whereMonth('paid_at', $month);
        })->where(function ($q) use ($courseIds, $bootcampIds, $bookIds) {
            $q->where(function ($q2) use ($courseIds) {
                $q2->where('itemable_type', Course::class)->whereIn('itemable_id', $courseIds);
            })->orWhere(function ($q2) use ($bootcampIds) {
                $q2->where('itemable_type', Bootcamp::class)->whereIn('itemable_id', $bootcampIds);
            })->orWhere(function ($q2) use ($bookIds) {
                $q2->where('itemable_type', Book::class)->whereIn('itemable_id', $bookIds);
            });
        })->count();
    }

    private function calculateTotalEarnings($courseIds, $bootcampIds, $bookIds): int
    {
        return (int) OrderItem::whereHas('order', fn ($q) => $q->paid())
            ->where(function ($q) use ($courseIds, $bootcampIds, $bookIds) {
                $q->where(function ($q2) use ($courseIds) {
                    $q2->where('itemable_type', Course::class)->whereIn('itemable_id', $courseIds);
                })->orWhere(function ($q2) use ($bootcampIds) {
                    $q2->where('itemable_type', Bootcamp::class)->whereIn('itemable_id', $bootcampIds);
                })->orWhere(function ($q2) use ($bookIds) {
                    $q2->where('itemable_type', Book::class)->whereIn('itemable_id', $bookIds);
                });
            })->sum(DB::raw('price * quantity'));
    }

    private function getEarningByType(string $type, $ids, int $year): int
    {
        return (int) OrderItem::whereHas('order', function ($q) use ($year) {
            $q->paid()->whereYear('paid_at', $year);
        })
            ->where('itemable_type', $type)
            ->whereIn('itemable_id', $ids)
            ->sum(DB::raw('price * quantity'));
    }

    private function getAvailableYears(int $instructorId): array
    {
        $courseIds   = Course::where('instructor_id', $instructorId)->pluck('id');
        $bootcampIds = Bootcamp::where('instructor_id', $instructorId)->pluck('id');
        $bookIds    = Book::where('instructor_id', $instructorId)->pluck('id');

        return OrderItem::whereHas('order', fn ($q) => $q->paid())
            ->where(function ($q) use ($courseIds, $bootcampIds, $bookIds) {
                $q->where(function ($q2) use ($courseIds) {
                    $q2->where('itemable_type', Course::class)->whereIn('itemable_id', $courseIds);
                })->orWhere(function ($q2) use ($bootcampIds) {
                    $q2->where('itemable_type', Bootcamp::class)->whereIn('itemable_id', $bootcampIds);
                })->orWhere(function ($q2) use ($bookIds) {
                    $q2->where('itemable_type', Book::class)->whereIn('itemable_id', $bookIds);
                });
            })
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->selectRaw('DISTINCT YEAR(orders.paid_at) as year')
            ->whereNotNull('orders.paid_at')
            ->orderByDesc('year')
            ->pluck('year')
            ->toArray();
    }
}
