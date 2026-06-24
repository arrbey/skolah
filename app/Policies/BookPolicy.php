<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\OrderItem;
use App\Models\User;

class BookPolicy
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
     * Hanya instructor pemilik buku yang bisa melihat di panel instructor.
     */
    public function view(User $user, Book $book): bool
    {
        return $user->id === $book->instructor_id;
    }

    /**
     * Hanya instructor yang bisa membuat buku.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('instructor');
    }

    /**
     * Hanya instructor pemilik buku yang bisa edit.
     */
    public function update(User $user, Book $book): bool
    {
        return $user->id === $book->instructor_id;
    }

    /**
     * Hanya instructor pemilik buku yang bisa hapus.
     */
    public function delete(User $user, Book $book): bool
    {
        return $user->id === $book->instructor_id;
    }

    /**
     * Download buku digital — instructor pemilik atau user yang sudah beli.
     */
    public function download(User $user, Book $book): bool
    {
        // Instructor pemilik buku bisa download
        if ($user->hasRole('instructor') && $user->id === $book->instructor_id) {
            return true;
        }

        // User yang sudah membeli buku (order status paid)
        return OrderItem::whereHas('order', function ($q) use ($user) {
                $q->where('user_id', $user->id)->where('status', 'paid');
            })
            ->where('itemable_type', Book::class)
            ->where('itemable_id', $book->id)
            ->exists();
    }
}
