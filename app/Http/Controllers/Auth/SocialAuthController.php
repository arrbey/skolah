<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Provider yang diizinkan.
     */
    private array $allowedProviders = ['google'];

    /**
     * Redirect ke halaman OAuth provider.
     */
    public function redirect(string $provider): RedirectResponse
    {
        if (! in_array($provider, $this->allowedProviders)) {
            abort(404);
        }

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle callback dari OAuth provider.
     */
    public function callback(string $provider): RedirectResponse
    {
        if (! in_array($provider, $this->allowedProviders)) {
            abort(404);
        }

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Throwable $e) {
            Log::warning('Social login callback failed', [
                'provider' => $provider,
                'error'    => $e->getMessage(),
            ]);

            return redirect()->route('login')
                ->with('error', 'Gagal login dengan Google. Silakan coba lagi.');
        }

        // Cari social account yang sudah terhubung
        $socialAccount = SocialAccount::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        // ─── CASE 1: Social account sudah ada → login langsung ─────────────
        if ($socialAccount) {
            $user = User::withTrashed()->find($socialAccount->user_id);

            // Jika user tidak ditemukan (data gantung), hapus social account dan redirect ke signup
            if (! $user) {
                $socialAccount->delete();
                return redirect()->route('login')
                    ->with('error', 'Akun tidak sinkron. Silakan coba login kembali untuk menghubungkan ulang.');
            }

            // Restore jika soft-deleted
            if ($user->trashed()) {
                $user->restore();
            }

            // ── Cek apakah akun di-suspend ────────────────────────────────
            if ($user->suspended_at) {
                return redirect()->route('login')
                    ->withErrors(['email' => 'Akun Anda telah ditangguhkan. Hubungi admin@' . \App\Models\Setting::get('site_name', 'Skolah.com') . ' untuk informasi lebih lanjut.']);
            }

            // Update data terbaru (Gunakan try-catch agar tidak 500 jika token terlalu panjang)
            try {
                $socialAccount->update([
                    'provider_token'         => $socialUser->token,
                    'provider_refresh_token' => $socialUser->refreshToken ?? $socialAccount->provider_refresh_token,
                    'avatar'                 => $socialUser->getAvatar(),
                ]);
            } catch (\Throwable $e) {
                Log::error('Failed to update social tokens: ' . $e->getMessage());
                // Lanjut login saja meskipun update token gagal
            }

            Auth::login($user, remember: true);
            
            try {
                session()->regenerate();
            } catch (\Throwable) {
                // Abaikan jika regenerate session gagal di shared hosting
            }

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Selamat datang kembali, ' . $user->name . '!');
        }

        // ─── CASE 2: Email sudah terdaftar → hubungkan akun ────────────────
        $existingUser = User::withTrashed()->where('email', $socialUser->getEmail())->first();

        if ($existingUser) {
            // Restore jika soft-deleted
            if ($existingUser->trashed()) {
                $existingUser->restore();
            }

            // ── Cek apakah akun di-suspend ────────────────────────────────
            if ($existingUser->suspended_at) {
                return redirect()->route('login')
                    ->withErrors(['email' => 'Akun Anda telah ditangguhkan. Hubungi admin@' . \App\Models\Setting::get('site_name', 'Skolah.com') . ' untuk informasi lebih lanjut.']);
            }

            $existingUser->socialAccounts()->create([
                'provider'               => $provider,
                'provider_id'            => $socialUser->getId(),
                'provider_token'         => $socialUser->token,
                'provider_refresh_token' => $socialUser->refreshToken,
                'avatar'                 => $socialUser->getAvatar(),
            ]);

            // Jika belum verified, otomatis verify (karena Google sudah verifikasi email)
            if (is_null($existingUser->email_verified_at)) {
                $existingUser->update(['email_verified_at' => now()]);
            }

            // Update avatar jika belum ada
            if (empty($existingUser->avatar) && $socialUser->getAvatar()) {
                $existingUser->update(['avatar' => $socialUser->getAvatar()]);
            }

            Auth::login($existingUser, remember: true);
            session()->regenerate();

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Akun Google berhasil dihubungkan! Selamat datang, ' . $existingUser->name . '.');
        }

        // ─── CASE 3: User baru → registrasi otomatis ───────────────────────
        $user = DB::transaction(function () use ($socialUser, $provider) {
            $user = User::create([
                'name'              => $socialUser->getName() ?? $socialUser->getNickname() ?? 'User',
                'email'             => $socialUser->getEmail(),
                'password'          => bcrypt(Str::random(32)), // random password, user bisa set nanti
                'avatar'            => $socialUser->getAvatar(),
                'role'              => 'user',
                'email_verified_at' => now(), // Google sudah verifikasi email
            ]);

            $user->assignRole('user');

            $user->socialAccounts()->create([
                'provider'               => $provider,
                'provider_id'            => $socialUser->getId(),
                'provider_token'         => $socialUser->token,
                'provider_refresh_token' => $socialUser->refreshToken,
                'avatar'                 => $socialUser->getAvatar(),
            ]);

            return $user;
        });

        event(new Registered($user));

        Auth::login($user, remember: true);
        session()->regenerate();

        return redirect()->route('dashboard')
            ->with('success', 'Akun berhasil dibuat via Google! Selamat datang di ' . \App\Models\Setting::get('site_name', 'Skolah.com') . ', ' . $user->name . '.');
    }
}
