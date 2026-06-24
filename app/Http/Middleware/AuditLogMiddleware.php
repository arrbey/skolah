<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Fase 9 — Audit Log Middleware
 *
 * Mencatat aksi penting (POST, PUT, PATCH, DELETE) ke tabel audit_logs.
 * Bisa dipakai sebagai route middleware ('audit') atau group middleware.
 *
 * Payload disanitasi — field sensitif (password, token, secret, dll) dihapus.
 */
class AuditLogMiddleware
{
    /**
     * HTTP methods yang dicatat.
     */
    protected array $methods = ['POST', 'PUT', 'PATCH', 'DELETE'];

    /**
     * Field yang TIDAK boleh masuk ke audit log payload.
     */
    protected array $sensitiveFields = [
        'password',
        'password_confirmation',
        'current_password',
        'new_password',
        'new_password_confirmation',
        'token',
        'secret',
        '_token',
        'credit_card',
        'card_number',
        'cvv',
        'server_key',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        try {
            if ($this->shouldLog($request)) {
                AuditLog::create([
                    'user_id'     => auth()->id(),
                    'ip_address'  => $request->ip(),
                    'method'      => $request->method(),
                    'url'         => substr($request->fullUrl(), 0, 1000),
                    'route_name'  => $request->route()?->getName(),
                    'payload'     => $this->sanitizePayload($request),
                    'status_code' => $response->getStatusCode(),
                    'user_agent'  => substr($request->userAgent() ?? '', 0, 500),
                    'created_at'  => now(),
                ]);
            }
        } catch (\Throwable $e) {
            // Jangan sampai audit log gagal → break user flow
            Log::error('AuditLog middleware error: ' . $e->getMessage(), [
                'url'    => $request->fullUrl(),
                'method' => $request->method(),
            ]);
        }

        return $response;
    }

    /**
     * Apakah request ini perlu dicatat.
     */
    protected function shouldLog(Request $request): bool
    {
        return in_array($request->method(), $this->methods);
    }

    /**
     * Sanitize request payload — hapus field sensitif, batasi ukuran.
     */
    protected function sanitizePayload(Request $request): ?array
    {
        $data = $request->except($this->sensitiveFields);

        // Hapus file dari payload (sudah dicatat di URL)
        $data = array_filter($data, fn($value) => !($value instanceof \Illuminate\Http\UploadedFile));

        if (empty($data)) {
            return null;
        }

        // Truncate string values yang terlalu panjang (max 500 chars per value)
        array_walk_recursive($data, function (&$value) {
            if (is_string($value) && strlen($value) > 500) {
                $value = substr($value, 0, 500) . '...[truncated]';
            }
        });

        // Batasi total payload agar tidak terlalu besar di DB
        $encoded = json_encode($data, JSON_UNESCAPED_UNICODE);
        if ($encoded && strlen($encoded) > 10000) {
            return ['_notice' => 'Payload too large, truncated', '_size' => strlen($encoded)];
        }

        return $data;
    }
}
