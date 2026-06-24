<?php

namespace App\Policies;

use App\Models\Certificate;
use App\Models\User;

class CertificatePolicy
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
     * Hanya pemilik sertifikat yang bisa melihat/download.
     */
    public function download(User $user, Certificate $certificate): bool
    {
        return $user->id === $certificate->user_id;
    }
}
