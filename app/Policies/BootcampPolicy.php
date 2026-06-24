<?php

namespace App\Policies;

use App\Models\Bootcamp;
use App\Models\User;

class BootcampPolicy
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
     * Hanya instructor pemilik bootcamp yang bisa melihat di panel instructor.
     */
    public function view(User $user, Bootcamp $bootcamp): bool
    {
        return $user->id === $bootcamp->instructor_id;
    }

    /**
     * Hanya instructor yang bisa membuat bootcamp.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('instructor');
    }

    /**
     * Hanya instructor pemilik bootcamp yang bisa edit.
     */
    public function update(User $user, Bootcamp $bootcamp): bool
    {
        return $user->id === $bootcamp->instructor_id;
    }

    /**
     * Hanya instructor pemilik bootcamp yang bisa hapus.
     */
    public function delete(User $user, Bootcamp $bootcamp): bool
    {
        return $user->id === $bootcamp->instructor_id;
    }
}
