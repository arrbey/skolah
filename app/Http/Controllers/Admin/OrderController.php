<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items']);

        // Search by order number or user
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
            });
        }

        // Filter status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Filter date range
        if ($from = $request->input('from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->input('to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $stats = [
            'total'    => Order::count(),
            'paid'     => Order::paid()->count(),
            'pending'  => Order::pending()->count(),
            'failed'   => Order::failed()->count(),
            'revenue'  => Order::paid()->sum('total'),
        ];

        $orders = $query->latest()->paginate(20);

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items']);

        return view('admin.orders.show', compact('order'));
    }

    public function export(Request $request)
    {
        $format = $request->input('format', 'pdf');

        $query = Order::with('user');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($from = $request->input('from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->input('to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $orders = $query->latest()->get();

        if ($format === 'excel') {
            return $this->exportExcel($orders);
        }

        return $this->exportPdf($orders);
    }

    private function exportPdf($orders)
    {
        $pdf = Pdf::loadView('admin.orders.export-pdf', compact('orders'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('orders-' . date('Y-m-d') . '.pdf');
    }

    private function exportExcel($orders)
    {
        $filename = 'orders-' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($orders) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, ['No. Order', 'User', 'Email', 'Total', 'Status', 'Metode Bayar', 'Tanggal']);

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_number,
                    $order->user?->name ?? '-',
                    $order->user?->email ?? '-',
                    $order->total,
                    $order->status,
                    $order->payment_method ?? '-',
                    $order->created_at->format('d/m/Y H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
