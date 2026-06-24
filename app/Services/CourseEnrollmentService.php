<?php

namespace App\Services;

use App\Mail\EnrollmentConfirmationMail;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseVariant;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
class CourseEnrollmentService
{
    /**
     * Handle pembayaran course berhasil.
     * Buat record CourseEnrollment + increment total_students di Course.
     *
     * Dipanggil dari MidtransWebhookController.
     */
    public function handlePaymentSuccess(Order $order): void
    {
        $order->loadMissing(['user', 'items.itemable']);

        DB::transaction(function () use ($order) {
            // Update status order
            if ($order->status !== 'paid') {
                $order->update([
                    'status'  => 'paid',
                    'paid_at' => now(),
                ]);
            }

            foreach ($order->items as $item) {
                if (! ($item->itemable instanceof Course)) {
                    continue;
                }

                /** @var Course $course */
                $course = $item->itemable;

                // Cek duplikasi enrollment
                $exists = CourseEnrollment::where('user_id', $order->user_id)
                    ->where('course_id', $course->id)
                    ->exists();

                if ($exists) {
                    Log::info('CourseEnrollment already exists', [
                        'user_id'   => $order->user_id,
                        'course_id' => $course->id,
                    ]);
                    continue;
                }

                // Buat enrollment (with variant if applicable)
                $enrollmentData = [
                    'user_id'             => $order->user_id,
                    'course_id'           => $course->id,
                    'enrolled_at'         => now(),
                    'progress_percentage' => 0,
                ];

                // Simpan variant_id jika ada
                if ($item->course_variant_id) {
                    $enrollmentData['course_variant_id'] = $item->course_variant_id;
                }

                CourseEnrollment::create($enrollmentData);

                // Increment total students di course
                $course->increment('total_students');

                // Increment total_enrolled di variant jika ada
                if ($item->course_variant_id) {
                    CourseVariant::where('id', $item->course_variant_id)
                        ->increment('total_enrolled');
                }

                // Kirim email konfirmasi enrollment
                Mail::to($order->user->email)->send(
                    new EnrollmentConfirmationMail($order->user, $course)
                );

                // Kirim notifikasi in-app enrollment
                try {
                    send_notification(
                        user: $order->user,
                        type: 'course',
                        title: '🎓 Kamu Terdaftar di Kursus Baru!',
                        message: "Selamat! Kamu berhasil terdaftar di kursus \"{$course->title}\". Mulai belajar sekarang!",
                        url: route('learn.index', ['courseSlug' => $course->slug]),
                    );
                } catch (\Throwable $e) {
                    Log::warning('Course enrollment in-app notification failed', [
                        'user_id'   => $order->user_id,
                        'course_id' => $course->id,
                        'error'     => $e->getMessage(),
                    ]);
                }

                Log::info('CourseEnrollment created after payment success', [
                    'user_id'   => $order->user_id,
                    'course_id' => $course->id,
                    'order'     => $order->order_number,
                ]);
            }
        });
    }

    /**
     * Handle pembayaran course gagal.
     * Tidak perlu rollback karena enrollment hanya dibuat saat sukses.
     */
    public function handlePaymentFailed(Order $order): void
    {
        Log::info('Course payment failed — no enrollment cleanup needed', [
            'order_id' => $order->id,
        ]);
    }

    /**
     * Enrollment manual oleh admin — untuk user yang membeli di luar website.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Course  $course
     * @param  int|null  $variantId
     * @param  bool  $sendNotification  Kirim email + in-app notification
     * @return array{enrollment: CourseEnrollment, created: bool}
     */
    public function manualEnroll(
        \App\Models\User $user,
        Course $course,
        ?int $variantId = null,
        bool $sendNotification = true
    ): array {
        return DB::transaction(function () use ($user, $course, $variantId, $sendNotification) {
            // Cek duplikasi
            $existing = CourseEnrollment::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->first();

            if ($existing) {
                return ['enrollment' => $existing, 'created' => false];
            }

            $data = [
                'user_id'             => $user->id,
                'course_id'           => $course->id,
                'enrolled_at'         => now(),
                'progress_percentage' => 0,
            ];

            if ($variantId) {
                $data['course_variant_id'] = $variantId;
            }

            $enrollment = CourseEnrollment::create($data);

            $course->increment('total_students');

            if ($variantId) {
                CourseVariant::where('id', $variantId)->increment('total_enrolled');
            }

            if ($sendNotification) {
                try {
                    Mail::to($user->email)->send(
                        new EnrollmentConfirmationMail($user, $course)
                    );
                } catch (\Throwable $e) {
                    Log::warning('Manual enrollment email failed', [
                        'user_id'   => $user->id,
                        'course_id' => $course->id,
                        'error'     => $e->getMessage(),
                    ]);
                }

                try {
                    send_notification(
                        user: $user,
                        type: 'course',
                        title: '🎓 Kamu Terdaftar di Kursus Baru!',
                        message: "Kamu berhasil terdaftar di kursus \"{$course->title}\". Mulai belajar sekarang!",
                        url: route('learn.index', ['courseSlug' => $course->slug]),
                    );
                } catch (\Throwable $e) {
                    Log::warning('Manual enrollment in-app notification failed', [
                        'user_id'   => $user->id,
                        'course_id' => $course->id,
                        'error'     => $e->getMessage(),
                    ]);
                }
            }

            Log::info('Manual course enrollment created by admin', [
                'user_id'   => $user->id,
                'course_id' => $course->id,
                'variant'   => $variantId,
            ]);

            return ['enrollment' => $enrollment, 'created' => true];
        });
    }

    /**
     * Hapus enrollment manual (unenroll) + decrement counter.
     */
    public function manualUnenroll(CourseEnrollment $enrollment): void
    {
        DB::transaction(function () use ($enrollment) {
            $course    = $enrollment->course;
            $variantId = $enrollment->course_variant_id;

            $enrollment->delete();

            if ($course && $course->total_students > 0) {
                $course->decrement('total_students');
            }

            if ($variantId) {
                $variant = CourseVariant::find($variantId);
                if ($variant && $variant->total_enrolled > 0) {
                    $variant->decrement('total_enrolled');
                }
            }

            Log::info('Manual course unenrollment by admin', [
                'enrollment_id' => $enrollment->id,
                'user_id'       => $enrollment->user_id,
                'course_id'     => $enrollment->course_id,
            ]);
        });
    }
}
