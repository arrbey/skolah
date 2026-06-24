<?php

namespace App\Policies;

use App\Models\BootcampRegistration;
use App\Models\User;

class BootcampRegistrationPolicy
{
    /**
     * Admin bisa melakukan semua aksi.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return null;
    }

    /**
     * Hanya pemilik tiket yang bisa download/view.
     */
    public function view(User $user, BootcampRegistration $registration): bool
    {
        return $user->id === $registration->user_id;
    }

    /**
     * Download tiket PDF/QR — hanya pemilik tiket.
     */
    public function download(User $user, BootcampRegistration $registration): bool
    {
        return $user->id === $registration->user_id;
    }
}
