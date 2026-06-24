<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Halaman daftar audit log dengan filter & search.
     */
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest('created_at');

        // Filter: search keyword (url, route_name, ip_address)
        if ($search = $request->input('search')) {
            $query->search($search);
        }

        // Filter: user_id
        if ($userId = $request->input('user_id')) {
            $query->byUser((int) $userId);
        }

        // Filter: HTTP method
        if ($method = $request->input('method')) {
            $query->byMethod($method);
        }

        // Filter: route prefix (admin, instructor, checkout, webhook)
        if ($routePrefix = $request->input('route')) {
            $query->byRoute($routePrefix);
        }

        // Filter: status code range
        if ($status = $request->input('status')) {
            match ($status) {
                '2xx' => $query->whereBetween('status_code', [200, 299]),
                '3xx' => $query->whereBetween('status_code', [300, 399]),
                '4xx' => $query->whereBetween('status_code', [400, 499]),
                '5xx' => $query->whereBetween('status_code', [500, 599]),
                default => null,
            };
        }

        // Filter: date range
        if ($from = $request->input('from')) {
            $query->where('created_at', '>=', $from);
        }
        if ($to = $request->input('to')) {
            $query->where('created_at', '<=', $to . ' 23:59:59');
        }

        $logs = $query->paginate(30)->withQueryString();

        // Data untuk dropdown filter
        $methods = ['POST', 'PUT', 'PATCH', 'DELETE'];
        $routePrefixes = [
            'admin.'      => 'Admin',
            'instructor.' => 'Instructor',
            'checkout'    => 'Checkout',
            'midtrans'    => 'Webhook',
        ];

        // Statistik ringkas
        $stats = [
            'total_today'   => AuditLog::whereDate('created_at', today())->count(),
            'total_week'    => AuditLog::where('created_at', '>=', now()->subWeek())->count(),
            'unique_users'  => AuditLog::whereDate('created_at', today())->distinct('user_id')->count('user_id'),
            'error_count'   => AuditLog::where('created_at', '>=', now()->subDay())
                                        ->where('status_code', '>=', 400)->count(),
        ];

        return view('admin.audit-logs.index', compact(
            'logs', 'methods', 'routePrefixes', 'stats'
        ));
    }

    /**
     * Detail payload audit log (modal/JSON).
     */
    public function show(AuditLog $auditLog)
    {
        $auditLog->load('user');

        return response()->json([
            'id'         => $auditLog->id,
            'user'       => $auditLog->user->name,
            'method'     => $auditLog->method,
            'url'        => $auditLog->url,
            'route_name' => $auditLog->route_name,
            'payload'    => $auditLog->payload,
            'status'     => $auditLog->status_code,
            'ip'         => $auditLog->ip_address,
            'user_agent' => $auditLog->user_agent,
            'created_at' => $auditLog->created_at->format('d M Y H:i:s'),
        ]);
    }
}
