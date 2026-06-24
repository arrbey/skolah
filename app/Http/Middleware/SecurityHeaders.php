<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Hapus header bawaan server agar tidak bentrok (double header = strictest wins)
        $response->headers->remove('Content-Security-Policy');

        // 1. Mencegah website dipasang di dalam iframe web lain (Anti-Clickjacking)
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // 2. Mencegah browser menebak-nebak tipe file (Anti-MIME Sniffing)
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // 3. Mengaktifkan filter XSS di browser lama
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // 4. Mengontrol informasi referrer yang dikirim ke web lain
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // 5. Membatasi akses ke fitur hardware browser yang tidak perlu
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=(self "https://*.midtrans.com")');

        // 6. Content Security Policy (CSP) — Hardened (no 'unsafe-inline' on script-src)
        // Inline scripts harus menggunakan nonce: <script nonce="{{ $cspNonce }}">
        // 'unsafe-eval' tetap dipertahankan karena dibutuhkan Alpine.js + Livewire + TinyMCE.
        $nonce = app()->bound('csp-nonce') ? app('csp-nonce') : '';
        $nonceSrc = $nonce ? "'nonce-{$nonce}'" : '';

        $midtransDomains = 'https://*.midtrans.com https://app.midtrans.com ' .
                             'https://app.sandbox.midtrans.com https://*.sandbox.midtrans.com ' .
                             'https://api.midtrans.com https://api.sandbox.midtrans.com';

        $csp = "default-src 'self'; " .
               "script-src 'self' {$nonceSrc} 'unsafe-eval' " .
                   $midtransDomains . ' ' .
                   "https://cdn.tiny.cloud https://*.tinymce.com " .
                   "https://cdn.jsdelivr.net https://code.jquery.com " .
                   "https://www.youtube.com https://s.ytimg.com " .
                   "https://www.googletagmanager.com https://www.google-analytics.com; " .
               "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdn.tiny.cloud https://*.tinymce.com; " .
               "img-src 'self' data: https: blob: " . $midtransDomains . ' https://*.tiny.cloud; ' .
               "font-src 'self' data: https://fonts.gstatic.com https://cdn.jsdelivr.net; " .
               "connect-src 'self' " .
                   $midtransDomains . ' ' .
                   "https://*.tiny.cloud " .
                   "https://cdn.jsdelivr.net " .
                   "wss://*.pusher.com wss://*.pusherapp.com " .
                   "https://*.pusher.com https://*.pusherapp.com " .
                   "https://www.google-analytics.com; " .
               "frame-src 'self' " . $midtransDomains . ' https://*.tiny.cloud https://www.youtube.com https://*.youtube.com https://*.youtube-nocookie.com; ' .
               "object-src 'none'; " .
               "base-uri 'self'; " .
               "form-action 'self' " . $midtransDomains . ';';

        // CSP diaktifkan kembali dengan izin 'unsafe-eval' dan 'unsafe-inline' untuk kompatibilitas maksimal
        $response->headers->set('Content-Security-Policy', $csp);

        // 7. Paksa browser menggunakan HTTPS (HSTS) - Hanya aktif jika bukan di localhost
        if (!app()->isLocal()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        return $response;
    }
}
