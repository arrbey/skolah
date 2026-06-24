<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateAvatarRequest;
use App\Http\Requests\User\UpdatePasswordRequest;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Services\MinioStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // SHOW — /dashboard/settings
    // ─────────────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        return view('dashboard.settings', [
            'user' => $request->user(),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // UPDATE PROFILE — POST /dashboard/settings/profile
    // ─────────────────────────────────────────────────────────────────────────

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'bio'  => ['nullable', 'string', 'max:500'],
        ]);

        $user->update($data);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // UPDATE PASSWORD — POST /dashboard/settings/password
    // ─────────────────────────────────────────────────────────────────────────

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ], [
            'current_password.current_password' => 'Password saat ini tidak sesuai.',
            'password.confirmed'                => 'Konfirmasi password tidak cocok.',
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password berhasil diubah.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // UPDATE AVATAR — POST /dashboard/settings/avatar
    // ─────────────────────────────────────────────────────────────────────────

    public function updateAvatar(UpdateAvatarRequest $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'avatar.max' => 'Ukuran foto maksimal 2 MB.',
        ]);

        $user  = $request->user();
        $minio = app(MinioStorageService::class);

        // Hapus avatar lama jika ada
        if ($user->avatar) {
            $minio->delete($user->avatar);
        }

        // Simpan avatar baru ke S3
        $url = $minio->uploadUserAvatar($request->file('avatar'), $user->id);

        $user->update(['avatar' => $url]);

        return back()->with('success', 'Foto profil berhasil diperbarui.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // DELETE AVATAR — DELETE /dashboard/settings/avatar
    // ─────────────────────────────────────────────────────────────────────────

    public function deleteAvatar(Request $request)
    {
        $user = $request->user();

        if ($user->avatar) {
            app(MinioStorageService::class)->delete($user->avatar);
        }

        $user->update(['avatar' => null]);

        return back()->with('success', 'Foto profil berhasil dihapus.');
    }
}
