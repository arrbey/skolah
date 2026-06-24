<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenance
{
    /**
     * Route prefixes yang tetap bisa diakses saat maintenance.
     * Login/logout harus diizinkan agar admin bisa masuk ke panel.
     */
    protected array $exceptPrefixes = [
        'admin',            // Admin panel tetap bisa diakses
        'midtrans/webhook', // Payment webhook harus tetap jalan
        'livewire',         // Livewire internal requests
        'login',            // Halaman login agar admin bisa masuk
        'logout',           // Proses logout
    ];

    /**
     * Route names yang tetap bisa diakses saat maintenance.
     */
    protected array $exceptRoutes = [
        'login',
        'login.post',
        'logout',
        'maintenance',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah maintenance mode aktif
        if (!$this->isMaintenanceMode()) {
            return $next($request);
        }

        // Admin selalu bypass maintenance
        if ($this->isAdmin($request)) {
            return $next($request);
        }

        // Cek apakah route dikecualikan
        if ($this->isExcluded($request)) {
            // Khusus halaman login: biarkan request masuk, tapi jika user BUKAN admin
            // setelah login, mereka tetap akan kena blokir di redirect berikutnya.
            return $next($request);
        }

        // Tampilkan halaman maintenance
        $message = Setting::get('maintenance_message', 'Sedang dalam pemeliharaan. Kami akan kembali segera!');

        return response()->view('errors.503', [
            'message' => $message,
        ], 503);
    }

    /**
     * Cek apakah maintenance mode aktif dari settings DB.
     */
    protected function isMaintenanceMode(): bool
    {
        try {
            return Setting::get('maintenance_mode', '0') === '1';
        } catch (\Throwable $e) {
            // Jika database belum tersedia, jangan blokir
            return false;
        }
    }

    /**
     * Cek apakah user yang login adalah admin.
     */
    protected function isAdmin(Request $request): bool
    {
        $user = $request->user();

        if (!$user) {
            return false;
        }

        // Cek via Spatie role
        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
            return true;
        }

        // Fallback: cek kolom role
        if (isset($user->role) && $user->role === 'admin') {
            return true;
        }

        return false;
    }

    /**
     * Cek apakah request ini dikecualikan dari maintenance block.
     */
    protected function isExcluded(Request $request): bool
    {
        $path = $request->path();

        // Cek prefix URL
        foreach ($this->exceptPrefixes as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return true;
            }
        }

        // Cek route name
        $routeName = $request->route()?->getName();
        if ($routeName && in_array($routeName, $this->exceptRoutes)) {
            return true;
        }

        return false;
    }
}
