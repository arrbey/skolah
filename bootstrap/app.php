<?php

use App\Http\Middleware\CheckMaintenance;
use App\Http\Middleware\CheckMembership;
use App\Http\Middleware\SanitizeInput;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\ForcePasswordChange;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // ── Trust Proxies (Cloudflare/cPanel reverse proxy) ───────────────────
        // Jangan fallback ke wildcard. Set TRUSTED_PROXIES di .env hanya jika
        // benar-benar berada di belakang proxy tepercaya (comma-separated IP).
        $trustedProxies = env('TRUSTED_PROXIES');
        $middleware->trustProxies(
            at: $trustedProxies
                ? ($trustedProxies === '*' ? '*' : array_map('trim', explode(',', $trustedProxies)))
                : []
        );

        // ── Web-only Middleware ────────────────────────────────────────────────
        // Middleware ini HANYA berlaku untuk route di web.php.
        // Route API (api.php) seperti webhook Midtrans tidak terkena ini.
        $middleware->web(append: [
            SanitizeInput::class,
            SecurityHeaders::class,
            CheckMaintenance::class,
            ForcePasswordChange::class,
            \App\Http\Middleware\CheckAccountStatus::class,
        ]);

        // ── Middleware Aliases (Route Middleware) ──────────────────────────────
        // Digunakan di route: ->middleware('role:admin')
        $middleware->alias([
            'role'               => RoleMiddleware::class,
            'permission'         => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'membership'         => CheckMembership::class,
            'enrolled'           => \App\Http\Middleware\CheckEnrollment::class,
            'midtrans_ip'           => \App\Http\Middleware\VerifyMidtransIp::class,
            'audit'                 => \App\Http\Middleware\AuditLogMiddleware::class,
            'force_password_change' => ForcePasswordChange::class,
            'admin_idle'            => \App\Http\Middleware\AdminIdleTimeout::class,
        ]);

        // ── Kecualikan Midtrans Webhook dari CSRF Verification ─────────────────
        // Midtrans server POST ke endpoint ini — tidak bisa kirim CSRF token
        // Catatan: Route API secara default sudah dikecualikan dari CSRF.
        $middleware->validateCsrfTokens(except: [
            // 'api/*', // Opsional jika ingin eksplisit
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // ── Security: Log suspicious/critical exceptions ──────────────────
        $exceptions->reportable(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e) {
            // Log 404 hanya jika path mengandung pola mencurigakan (path traversal, sql injection probe)
            $path = request()->path();
            if (preg_match('/(\.\.|\/etc\/|wp-admin|phpmy|\.sql|\.bak|<script)/i', $path)) {
                \Illuminate\Support\Facades\Log::channel('daily')->warning('Suspicious 404 request', [
                    'path'       => $path,
                    'ip'         => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }
        })->stop(false); // stop(false) = tetap report ke default handler juga

        $exceptions->reportable(function (\Illuminate\Auth\Access\AuthorizationException $e) {
            \Illuminate\Support\Facades\Log::channel('daily')->warning('Authorization violation', [
                'user_id'    => auth()->id(),
                'path'       => request()->path(),
                'ip'         => request()->ip(),
                'message'    => $e->getMessage(),
            ]);
        })->stop(false);

        $exceptions->reportable(function (\Illuminate\Session\TokenMismatchException $e) {
            \Illuminate\Support\Facades\Log::channel('daily')->warning('CSRF token mismatch', [
                'user_id'    => auth()->id(),
                'path'       => request()->path(),
                'method'     => request()->method(),
                'ip'         => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        })->stop(false);

        $exceptions->reportable(function (\Illuminate\Http\Exceptions\ThrottleRequestsException $e) {
            \Illuminate\Support\Facades\Log::channel('daily')->warning('Rate limit exceeded', [
                'user_id'    => auth()->id(),
                'path'       => request()->path(),
                'ip'         => request()->ip(),
            ]);
        })->stop(false);

        // ── Jangan pernah expose stack trace di production ────────────────
        // Error pages akan otomatis tampil dari resources/views/errors/
        // Laravel sudah handle ini, tapi kita pastikan response tidak bocor
        $exceptions->respond(function (\Symfony\Component\HttpFoundation\Response $response) {
            // Pastikan header keamanan ada di response error juga
            if (! app()->isLocal()) {
                $response->headers->set('X-Content-Type-Options', 'nosniff');
                $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
            }

            return $response;
        });
    })->create();
