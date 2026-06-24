<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    /**
     * Admin bisa melakukan semua aksi.
     * Return null agar lanjut ke method spesifik jika bukan admin.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return null;
    }

    /**
     * Hanya instructor pemilik course yang bisa melihat di panel instructor.
     */
    public function view(User $user, Course $course): bool
    {
        return $user->id === $course->instructor_id;
    }

    /**
     * Hanya instructor yang bisa membuat course.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('instructor');
    }

    /**
     * Hanya instructor pemilik course yang bisa edit.
     */
    public function update(User $user, Course $course): bool
    {
        return $user->id === $course->instructor_id;
    }

    /**
     * Hanya instructor pemilik course yang bisa hapus.
     */
    public function delete(User $user, Course $course): bool
    {
        return $user->id === $course->instructor_id;
    }

    /**
     * Hanya user yang sudah enroll yang bisa akses learning room.
     */
    public function learn(User $user, Course $course): bool
    {
        return $course->enrollments()
            ->where('user_id', $user->id)
            ->exists();
    }

    /**
     * Hanya instructor pemilik course yang bisa kelola sections & lessons.
     */
    public function manageLessons(User $user, Course $course): bool
    {
        return $user->id === $course->instructor_id;
    }

    /**
     * Hanya instructor pemilik course yang bisa kelola quiz.
     */
    public function manageQuizzes(User $user, Course $course): bool
    {
        return $user->id === $course->instructor_id;
    }
}
