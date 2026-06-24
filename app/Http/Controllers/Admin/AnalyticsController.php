<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Order;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->input('period', '30');

        // Revenue chart
        $chartData = $this->getRevenueChart((int) $period);

        // User growth chart
        $userGrowth = $this->getUserGrowthChart((int) $period);

        // Top performing courses
        $topCourses = Course::published()
            ->orderByDesc('total_students')
            ->take(10)
            ->get();

        // Revenue by product type
        $revenueByType = $this->getRevenueByType();

        // Summary
        $summary = [
            'totalRevenue'    => Order::paid()->sum('total'),
            'periodRevenue'   => Order::paid()->where('paid_at', '>=', now()->subDays((int) $period))->sum('total'),
            'totalOrders'     => Order::paid()->count(),
            'periodOrders'    => Order::paid()->where('paid_at', '>=', now()->subDays((int) $period))->count(),
            'avgOrderValue'   => (int) Order::paid()->avg('total'),
            'newUsers'        => User::where('created_at', '>=', now()->subDays((int) $period))->count(),
        ];

        return view('admin.analytics', compact('chartData', 'userGrowth', 'topCourses', 'revenueByType', 'summary', 'period'));
    }

    public function export(Request $request)
    {
        $format = $request->input('format', 'pdf');
        $period = (int) $request->input('period', 30);

        $chartData     = $this->getRevenueChart($period);
        $revenueByType = $this->getRevenueByType();
        $topCourses    = Course::published()
            ->withCount('enrollments')
            ->orderByDesc('enrollments_count')
            ->limit(5)
            ->get();
        $summary       = [
            'totalRevenue'  => Order::paid()->sum('total'),
            'periodRevenue' => Order::paid()->where('paid_at', '>=', now()->subDays($period))->sum('total'),
            'totalOrders'   => Order::paid()->count(),
            'periodOrders'  => Order::paid()->where('paid_at', '>=', now()->subDays($period))->count(),
        ];

        if ($format === 'excel') {
            return $this->exportCsv($chartData, $summary, $period);
        }

        $pdf = Pdf::loadView('admin.analytics-export-pdf', compact('chartData', 'revenueByType', 'topCourses', 'summary', 'period'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('analytics-' . date('Y-m-d') . '.pdf');
    }

    private function getRevenueChart(int $days): array
    {
        $labels = [];
        $data   = [];

        $revenues = Order::paid()
            ->where('paid_at', '>=', now()->subDays($days)->startOfDay())
            ->selectRaw('DATE(paid_at) as date, SUM(total) as revenue')
            ->groupBy('date')
            ->pluck('revenue', 'date');

        for ($i = $days - 1; $i >= 0; $i--) {
            $day      = now()->subDays($i)->format('Y-m-d');
            $labels[] = Carbon::parse($day)->translatedFormat('d M');
            $data[]   = (int) ($revenues[$day] ?? 0);
        }

        return compact('labels', 'data');
    }

    private function getUserGrowthChart(int $days): array
    {
        $labels = [];
        $data   = [];

        $users = User::where('created_at', '>=', now()->subDays($days)->startOfDay())
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->pluck('count', 'date');

        for ($i = $days - 1; $i >= 0; $i--) {
            $day      = now()->subDays($i)->format('Y-m-d');
            $labels[] = Carbon::parse($day)->translatedFormat('d M');
            $data[]   = (int) ($users[$day] ?? 0);
        }

        return compact('labels', 'data');
    }

    private function getRevenueByType(): array
    {
        // Based on order_items itemable_type
        $result = \App\Models\OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'paid')
            ->selectRaw("
                CASE
                    WHEN itemable_type LIKE '%Course%' THEN 'Kursus'
                    WHEN itemable_type LIKE '%Bootcamp%' THEN 'Bootcamp'
                    WHEN itemable_type LIKE '%Book%' THEN 'Buku'
                    WHEN itemable_type LIKE '%Membership%' THEN 'Membership'
                    ELSE 'Lainnya'
                END as type,
                SUM(order_items.price * order_items.quantity) as total
            ")
            ->groupBy('type')
            ->pluck('total', 'type')
            ->toArray();

        return $result;
    }

    private function exportCsv(array $chartData, array $summary, int $period)
    {
        $filename = 'analytics-' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($chartData, $summary, $period) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, ["Laporan Analitik ' . \App\Models\Setting::get('site_name', 'Skolah.com') . ' — {$period} Hari Terakhir"]);
            fputcsv($file, []);
            fputcsv($file, ['Total Revenue', $summary['totalRevenue']]);
            fputcsv($file, ['Revenue Periode', $summary['periodRevenue']]);
            fputcsv($file, ['Total Orders', $summary['totalOrders']]);
            fputcsv($file, ['Orders Periode', $summary['periodOrders']]);
            fputcsv($file, []);
            fputcsv($file, ['Tanggal', 'Pendapatan (Rp)']);

            foreach ($chartData['labels'] as $i => $label) {
                fputcsv($file, [$label, $chartData['data'][$i]]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
