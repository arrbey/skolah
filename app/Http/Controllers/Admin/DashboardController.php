<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookOrder;
use App\Models\Bootcamp;
use App\Models\BootcampRegistration;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\InstructorApplication;
use App\Models\Order;
use App\Models\User;
use App\Models\UserMembership;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ── Stat cards ────────────────────────────────────────────────────────
        $totalRevenue      = Order::paid()->sum('total');
        $totalUsers        = User::count();
        $totalOrdersToday  = Order::whereDate('created_at', today())->count();
        $totalCourses      = Course::count();
        $totalBootcamps    = Bootcamp::count();
        $totalBooks        = Book::count();
        $pendingOrders     = Order::pending()->count();
        $activeMembers     = UserMembership::active()->count();
        $monthRevenue      = Order::paid()->whereMonth('paid_at', now()->month)->whereYear('paid_at', now()->year)->sum('total');

        // ── Alert counts (action required) ───────────────────────────────────
        $alertBootcampPending    = BootcampRegistration::pending()->count();
        $alertBookUnprocessed    = BookOrder::pending()->count();
        $alertInstructorPending  = InstructorApplication::pending()->count();
        $alertOrderExpired       = Order::pending()
            ->where('created_at', '<=', now()->subHours(24))
            ->count();

        // ── Revenue breakdown per kategori produk ────────────────────────────
        $revenueBreakdown = $this->getRevenueBreakdown();

        // ── 30-day revenue chart ──────────────────────────────────────────────
        $chartData = $this->getRevenueChart30Days();

        // ── Recent orders ─────────────────────────────────────────────────────
        $recentOrders = Order::with('user')
            ->latest()
            ->take(10)
            ->get();

        // ── New users this week ───────────────────────────────────────────────
        $newUsersThisWeek = User::where('created_at', '>=', now()->subWeek())->count();

        // ── Top courses ───────────────────────────────────────────────────────
        $topCourses = Course::published()
            ->orderByDesc('total_students')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalRevenue',
            'totalUsers',
            'totalOrdersToday',
            'totalCourses',
            'totalBootcamps',
            'totalBooks',
            'pendingOrders',
            'activeMembers',
            'monthRevenue',
            'alertBootcampPending',
            'alertBookUnprocessed',
            'alertInstructorPending',
            'alertOrderExpired',
            'revenueBreakdown',
            'chartData',
            'recentOrders',
            'newUsersThisWeek',
            'topCourses',
        ));
    }

    private function getRevenueBreakdown(): array
    {
        $paid = Order::paid();

        // Revenue per tipe item dari order_items (polymorphic)
        $courseRevenue     = (clone $paid)->whereHas('items', fn($q) => $q->where('itemable_type', 'App\\Models\\Course'))->sum('total');
        $bootcampRevenue   = (clone $paid)->whereHas('items', fn($q) => $q->where('itemable_type', 'App\\Models\\Bootcamp'))->sum('total');
        $bookRevenue       = (clone $paid)->whereHas('items', fn($q) => $q->where('itemable_type', 'App\\Models\\Book'))->sum('total');
        $membershipRevenue = (clone $paid)->whereHas('items', fn($q) => $q->where('itemable_type', 'App\\Models\\MembershipPlan'))->sum('total');

        $total = $courseRevenue + $bootcampRevenue + $bookRevenue + $membershipRevenue ?: 1;

        return [
            ['label' => 'Kursus',       'value' => $courseRevenue,     'pct' => round($courseRevenue / $total * 100),     'color' => 'bg-primary-500'],
            ['label' => 'Bootcamp',     'value' => $bootcampRevenue,   'pct' => round($bootcampRevenue / $total * 100),   'color' => 'bg-purple-500'],
            ['label' => 'Buku',         'value' => $bookRevenue,       'pct' => round($bookRevenue / $total * 100),       'color' => 'bg-green-500'],
            ['label' => 'Membership',   'value' => $membershipRevenue, 'pct' => round($membershipRevenue / $total * 100), 'color' => 'bg-yellow-500'],
        ];
    }

    private function getRevenueChart30Days(): array
    {
        $days = collect();
        for ($i = 29; $i >= 0; $i--) {
            $days->push(now()->subDays($i)->format('Y-m-d'));
        }

        $revenues = Order::paid()
            ->where('paid_at', '>=', now()->subDays(30)->startOfDay())
            ->selectRaw('DATE(paid_at) as date, SUM(total) as revenue')
            ->groupBy('date')
            ->pluck('revenue', 'date');

        $labels = [];
        $data   = [];

        foreach ($days as $day) {
            $labels[] = Carbon::parse($day)->translatedFormat('d M');
            $data[]   = (int) ($revenues[$day] ?? 0);
        }

        return compact('labels', 'data');
    }
}
