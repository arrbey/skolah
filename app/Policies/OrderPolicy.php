<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
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
     * Hanya pemilik order yang bisa melihat detailnya.
     */
    public function view(User $user, Order $order): bool
    {
        return $user->id === $order->user_id;
    }
}
