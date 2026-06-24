<?php

namespace App\Http\Middleware;

use App\Models\UserMembership;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckMembership
{
    /**
     * Cek apakah user memiliki membership aktif.
     * Gunakan di route yang memerlukan akses premium.
     *
     * Penggunaan di route:
     *   ->middleware('membership')
     *   ->middleware('membership:pro')       // hanya plan pro ke atas
     *   ->middleware('membership:pro,tim')   // pro atau tim
     */
    public function handle(Request $request, Closure $next, string ...$plans): Response
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login')
                ->with('info', 'Silakan login terlebih dahulu untuk mengakses konten ini.');
        }

        // Admin & instructor selalu punya akses penuh
        if ($user->hasAnyRole(['admin', 'instructor'])) {
            return $next($request);
        }

        // Cek membership aktif
        $membership = UserMembership::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->with('plan')
            ->first();

        // Jika tidak ada membership sama sekali → tolak
        if (! $membership) {
            return $this->denyAccess($request, 'Konten ini memerlukan membership aktif.');
        }

        // Jika tidak ada filter plan → cukup punya membership aktif apapun
        if (empty($plans)) {
            return $next($request);
        }

        // Cek apakah plan user termasuk dalam plan yang diizinkan
        if (in_array($membership->plan->slug, $plans)) {
            return $next($request);
        }

        return $this->denyAccess($request, 'Paket membership Anda tidak mencukupi untuk mengakses konten ini.');
    }

    private function denyAccess(Request $request, string $message): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'upgrade' => route('membership'),
            ], 403);
        }

        return redirect()->route('membership')
            ->with('warning', $message . ' Upgrade membership Anda untuk melanjutkan.');
    }
}
