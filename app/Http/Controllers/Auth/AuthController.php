<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use App\Models\Course;
use App\Models\Instructor;
use App\Models\Bootcamp;
use App\Events\StudentRegistered;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    // ── Login ──────────────────────────────────────────────────────────────────

    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            // Log percobaan login gagal ke DB untuk monitoring
            $this->logLoginAttempt($request, false);

            // Pesan generik — JANGAN beritahu field mana yang salah
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Email atau password tidak sesuai.']);
        }

        // Login berhasil — log & regenerate session (cegah session fixation)
        $this->logLoginAttempt($request, true);
        $request->session()->regenerate();

        $user = Auth::user();

        // ── Cek apakah akun di-suspend ────────────────────────────────────
        if ($user->suspended_at) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Akun Anda telah ditangguhkan. Hubungi admin@' . \App\Models\Setting::get('site_name', 'Skolah.com') . ' untuk informasi lebih lanjut.']);
        }

        // Jika belum verifikasi email — redirect ke halaman verifikasi (jangan logout)
        if (! $user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice')
                ->with('info', 'Silakan verifikasi email Anda terlebih dahulu. Cek inbox atau folder spam.');
        }

        return $this->redirectByRole($user)
            ->with('success', 'Selamat datang kembali, ' . $user->name . '!');
    }

    // ── Register ───────────────────────────────────────────────────────────────

    public function showRegister(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        $request->session()->put('register_form_loaded_at', now()->timestamp);
        $request->session()->put('register_form_token', Str::random(40));
        $request->session()->put('register_js_token', Str::random(64));

        return view('auth.register');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $request->session()->forget(['register_form_loaded_at', 'register_form_token', 'register_js_token']);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'user',
        ]);

        $user->assignRole('user');

        event(new Registered($user));

        // Trigger Realtime Stats Update
        event(new StudentRegistered(
            User::where('role', 'user')->count(),
            Course::count() ?? 0,
            User::where('role', 'instructor')->count(),
            Bootcamp::count() ?? 0
        ));

        Auth::login($user);
        $request->session()->regenerate(); // Cegah session fixation

        return redirect()->route('dashboard')
            ->with('success', 'Akun berhasil dibuat! Selamat datang di ' . \App\Models\Setting::get('site_name', 'Skolah.com') . ', ' . $user->name . '.');
    }

    // ── Logout ─────────────────────────────────────────────────────────────────

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda berhasil keluar dari ' . \App\Models\Setting::get('site_name', 'Skolah.com') . '.');
    }

    // ── Forgot Password ────────────────────────────────────────────────────────

    public function showForgotPassword(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return view('auth.forgot-password');
    }

    public function sendResetLink(ForgotPasswordRequest $request): RedirectResponse
    {
        // Kirim reset link — hasilnya TIDAK ditampilkan ke user
        // untuk mencegah email enumeration attack
        Password::sendResetLink(
            $request->only('email')
        );

        // Selalu tampilkan pesan sukses, terlepas email ada atau tidak
        return back()->with('success', 'Jika email terdaftar di sistem kami, link reset password telah dikirim. Cek folder inbox atau spam.');
    }

    // ── Reset Password ─────────────────────────────────────────────────────────

    public function showResetPassword(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request): RedirectResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')
                ->with('success', 'Password berhasil direset. Silakan login dengan password baru Anda.');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }

    public function showForcePasswordChange(): View
    {
        return view('auth.force-password-change');
    }

    public function updateForcePasswordChange(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::min(8)->mixedCase()->numbers()],
        ], [
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = Auth::user();
        $user->update([
            'password'             => Hash::make($request->password),
            'must_change_password' => false,
        ]);

        return $this->redirectByRole($user)
            ->with('success', 'Password Anda berhasil diperbarui. Selamat datang di portal.');
    }

    // ── Helper: Log percobaan login ke tabel login_attempts ───────────────────

    private function logLoginAttempt(Request $request, bool $successful): void
    {
        DB::table('login_attempts')->insert([
            'ip_address'   => $request->ip(),
            'email'        => $request->input('email', ''),
            'user_agent'   => substr((string) $request->userAgent(), 0, 500),
            'successful'   => $successful,
            'attempted_at' => now(),
        ]);
    }

    // ── Helper: Redirect berdasarkan role ──────────────────────────────────────

    private function redirectByRole(User $user): RedirectResponse
    {
        if ($user->hasRole('admin')) {
            return redirect()->intended(route('admin.dashboard'));
        }

        if ($user->hasRole('instructor')) {
            return redirect()->intended(route('instructor.dashboard'));
        }

        return redirect()->intended(route('dashboard'));
    }
}
