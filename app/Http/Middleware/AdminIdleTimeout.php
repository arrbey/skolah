<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin Idle Timeout — auto-logout admin setelah X menit idle.
 *
 * Berbeda dari SESSION_LIFETIME global (default 120 menit), middleware ini
 * memberlakukan timeout lebih ketat KHUSUS untuk admin (default 30 menit)
 * untuk mengurangi window serangan jika admin lupa logout di shared PC.
 *
 * Config via env: ADMIN_IDLE_TIMEOUT_MINUTES (default 30)
 *
 * Cara kerja:
 * - Saat request, cek session key `admin_last_activity` timestamp
 * - Kalau >timeout → flush session, redirect login, pesan "sesi kedaluwarsa"
 * - Kalau ≤timeout → update timestamp ke sekarang
 */
class AdminIdleTimeout
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Skip kalau belum login atau bukan admin (middleware ini idempotent)
        if (! $user || ! $user->hasRole('admin')) {
            return $next($request);
        }

        $timeoutMinutes = (int) env('ADMIN_IDLE_TIMEOUT_MINUTES', 30);
        $timeoutSeconds = $timeoutMinutes * 60;

        $lastActivity = $request->session()->get('admin_last_activity');

        if ($lastActivity && (time() - $lastActivity) > $timeoutSeconds) {
            // Log event sebelum flush
            Log::channel('daily')->warning('Admin session idle timeout', [
                'user_id'         => $user->id,
                'ip'              => $request->ip(),
                'idle_seconds'    => time() - $lastActivity,
                'timeout_minutes' => $timeoutMinutes,
            ]);

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('error', "Sesi admin kamu sudah kedaluwarsa ({$timeoutMinutes} menit idle). Silakan login ulang.");
        }

        // Update timestamp (sliding window)
        $request->session()->put('admin_last_activity', time());

        return $next($request);
    }
}
