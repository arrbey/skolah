<?php

namespace App\Providers;

use App\Listeners\SendPasswordChangedEmail;
use App\Listeners\SendWelcomeEmail;
use App\Models\CourseEnrollment;
use App\Observers\CourseEnrollmentObserver;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Vite;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ── Paksa HTTPS jika request datang via HTTPS (production / ngrok) ───
        if (config('app.env') === 'production' || request()->isSecure()) {
            URL::forceScheme('https');
        }

        // ── CSP Nonce: satu nonce acak per request (untuk script-src) ────────
        // Nonce di-share ke semua Blade view sebagai $cspNonce, dan juga
        // di-pakai otomatis oleh @vite() directive.
        $cspNonce = Str::random(32);
        app()->instance('csp-nonce', $cspNonce);
        Vite::useCspNonce($cspNonce);
        View::share('cspNonce', $cspNonce);

        // ── Carbon: locale Indonesia ───────────────────────────────────────────
        Carbon::setLocale('id');

        // ── Password Defaults: Kebijakan password global ───────────────────────
        // Semua FormRequest cukup pakai Password::defaults() tanpa duplicate rules
        Password::defaults(function () {
            return Password::min(8)
                ->mixedCase()
                ->numbers()
                ->max(72);
        });

        // ── Event Listeners: Email Notifikasi ──────────────────────────────────
        // Event::listen(Registered::class, SendWelcomeEmail::class); // Dimatikan karena sudah terdeteksi otomatis oleh Laravel 11
        Event::listen(PasswordReset::class, SendPasswordChangedEmail::class);

        // ── Model Observers ────────────────────────────────────────────────────
        CourseEnrollment::observe(CourseEnrollmentObserver::class);

        // ── Homepage cache invalidation on content changes ────────────────
        $homepageModels = [
            \App\Models\Course::class,
            \App\Models\Bundle::class,
            \App\Models\Bootcamp::class,
            \App\Models\Book::class,
            \App\Models\Banner::class,
            \App\Models\Category::class,
            \App\Models\Testimonial::class,
            \App\Models\MembershipPlan::class,
            \App\Models\Benefit::class,
            \App\Models\LandingProgram::class,
            \App\Models\Gallery::class,
            \App\Models\Campus::class,
        ];
        $invalidateKeys = [
            'home.page_v1',
            'courses.index.categories_v1',
            'courses.index.total_count_v1',
            'sitemap.xml_v1',
        ];
        foreach ($homepageModels as $model) {
            $model::saved(function () use ($invalidateKeys) {
                foreach ($invalidateKeys as $k) {
                    \Illuminate\Support\Facades\Cache::forget($k);
                }
            });
            $model::deleted(function () use ($invalidateKeys) {
                foreach ($invalidateKeys as $k) {
                    \Illuminate\Support\Facades\Cache::forget($k);
                }
            });
        }

        // ── Storage symlink workaround untuk shared hosting ────────────────────
        // cPanel sering tidak mengizinkan artisan storage:link via SSH.
        // Ini membuat symlink otomatis saat aplikasi boot pertama kali.
        // Dibungkus try-catch agar aplikasi tidak crash jika symlink() di-disable.
        if (! file_exists(public_path('storage'))) {
            try {
                app('files')->link(
                    storage_path('app/public'),
                    public_path('storage')
                );
            } catch (\Throwable $e) {
                // Log sekali tapi jangan halt request — admin perlu fix manual.
                \Illuminate\Support\Facades\Log::warning('Storage symlink failed', [
                    'message' => $e->getMessage(),
                    'hint'    => 'Run: php artisan storage:link',
                ]);
            }
        }

        // ── Membership: Cek apakah ada plan aktif ──────────────────────────
        \Illuminate\Support\Facades\View::share('hasMembershipPlans', \App\Models\MembershipPlan::active()->exists());

        // ── Rate Limiting ──────────────────────────────────────────────────────
        $this->configureRateLimiting();
    }

    /**
     * Konfigurasi rate limiter untuk keamanan ' . \App\Models\Setting::get('site_name', 'Skolah.com') . '.
     */
    protected function configureRateLimiting(): void
    {
        // ── Login ──────────────────────────────────────────────────────────
        // Layer 1: max 5 percobaan per menit per IP
        // Layer 2: max 5 percobaan per menit per kombinasi email+IP
        // Layer 3: max 20 percobaan per 15 menit per email (mitigasi distributed brute-force)
        RateLimiter::for('login', function (Request $request) {
            $email = Str::lower((string) $request->input('email'));
            $emailKey = $email !== '' ? sha1($email) : 'no-email';

            return [
                Limit::perMinute(5)
                    ->by('ip:' . $request->ip())
                    ->response(function () {
                        return back()->withErrors([
                            'email' => 'Terlalu banyak percobaan login. Silakan coba lagi dalam 1 menit.',
                        ])->withInput();
                    }),
                Limit::perMinute(5)
                    ->by('email-ip:' . $emailKey . '|' . $request->ip())
                    ->response(function () {
                        return back()->withErrors([
                            'email' => 'Terlalu banyak percobaan login untuk akun ini. Silakan coba lagi dalam 1 menit.',
                        ])->withInput();
                    }),
                Limit::perMinutes(15, 20)
                    ->by('email:' . $emailKey)
                    ->response(function () {
                        return back()->withErrors([
                            'email' => 'Akun dikunci sementara karena terlalu banyak percobaan. Coba lagi dalam 15 menit.',
                        ])->withInput();
                    }),
            ];
        });

        // ── Register ───────────────────────────────────────────────────────
        // Multi-layer: cegah bot flood dari IP sama dan email target sama.
        RateLimiter::for('register', function (Request $request) {
            $email = Str::lower((string) $request->input('email'));
            $emailKey = $email !== '' ? sha1($email) : 'no-email';
            $ipKey = 'ip:' . $request->ip();

            return [
                Limit::perMinute(1)
                    ->by($ipKey)
                    ->response(fn () => back()->withErrors([
                        'email' => 'Registrasi terlalu sering. Tunggu 1 menit sebelum mencoba lagi.',
                    ])->withInput()),
                Limit::perMinutes(60, 5)
                    ->by($ipKey)
                    ->response(fn () => back()->withErrors([
                        'email' => 'Batas registrasi per jam tercapai. Coba lagi nanti.',
                    ])->withInput()),
                Limit::perMinutes(1440, 10)
                    ->by($ipKey)
                    ->response(fn () => back()->withErrors([
                        'email' => 'Batas registrasi harian tercapai. Coba lagi besok.',
                    ])->withInput()),
                Limit::perMinutes(60, 3)
                    ->by('email:' . $emailKey)
                    ->response(fn () => back()->withErrors([
                        'email' => 'Email ini terlalu sering digunakan untuk registrasi. Coba lagi nanti.',
                    ])->withInput()),
            ];
        });

        // ── Forgot / Reset Password ────────────────────────────────────────
        // Max 5 request per jam per IP (cegah email spam)
        RateLimiter::for('forgot-password', function (Request $request) {
            return Limit::perHour(5)
                ->by($request->ip())
                ->response(function () {
                    return back()->withErrors([
                        'email' => 'Terlalu banyak permintaan reset password. Coba lagi dalam 1 jam.',
                    ])->withInput();
                });
        });

        // ── Checkout ───────────────────────────────────────────────────────
        // Max 10 request per menit per user (cegah duplikat order)
        RateLimiter::for('checkout', function (Request $request) {
            return Limit::perMinute(10)
                ->by($request->user()?->id ?? $request->ip())
                ->response(function () {
                    return back()->with('error', 'Terlalu banyak permintaan checkout. Silakan tunggu sebentar.');
                });
        });

        // ── Upload ─────────────────────────────────────────────────────────
        // Max 20 upload per jam per user (cegah abuse storage)
        RateLimiter::for('upload', function (Request $request) {
            return Limit::perHour(20)
                ->by($request->user()?->id ?? $request->ip())
                ->response(function () {
                    return back()->with('error', 'Batas upload tercapai. Maksimal 20 file per jam.');
                });
        });

        // ── Webhook Midtrans ───────────────────────────────────────────────
        // Max 100 request per menit per IP
        RateLimiter::for('webhook', function (Request $request) {
            return Limit::perMinute(100)->by($request->ip());
        });

        // ── Chat (cegah spam pesan) ────────────────────────────────────────
        // Max 30 pesan per menit per user
        RateLimiter::for('chat', function (Request $request) {
            return Limit::perMinute(30)
                ->by($request->user()?->id ?? $request->ip())
                ->response(function () {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Terlalu banyak pesan. Tunggu beberapa saat.',
                    ], 429);
                });
        });

        // ── API umum ───────────────────────────────────────────────────────
        // Max 60 request per menit per user / IP
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)
                ->by($request->user()?->id ?? $request->ip());
        });
    }
}
