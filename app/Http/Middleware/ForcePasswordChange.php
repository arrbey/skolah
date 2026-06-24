<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class ForcePasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->must_change_password) {
            // Jangan redirect jika sudah di halaman ganti password, atau saat logout,
            // atau saat sedang memproses verifikasi email/signature.
            if (! $request->is('auth/force-password-change*') && 
                ! $request->is('logout') && 
                ! $request->hasValidSignature()) {
                return redirect()->route('auth.force-password-change');
            }
        }

        return $next($request);
    }
}
