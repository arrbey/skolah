<?php

namespace App\Services;

use App\Mail\EnrollmentConfirmationMail;
use App\Models\Bundle;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BundleEnrollmentService
{
    public function handlePaymentSuccess(Order $order): void
    {
        $order->loadMissing(['user', 'items.itemable']);

        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                if (! ($item->itemable instanceof Bundle)) {
                    continue;
                }

                /** @var Bundle $bundle */
                $bundle = $item->itemable;

                foreach ($bundle->courses as $course) {
                    // Cek duplikasi enrollment
                    $exists = CourseEnrollment::where('user_id', $order->user_id)
                        ->where('course_id', $course->id)
                        ->exists();

                    if ($exists) {
                        continue;
                    }

                    // Buat enrollment
                    CourseEnrollment::create([
                        'user_id'             => $order->user_id,
                        'course_id'           => $course->id,
                        'enrolled_at'         => now(),
                        'progress_percentage' => 0,
                    ]);

                    // Increment total students di course
                    $course->increment('total_students');

                    // Kirim email konfirmasi enrollment (opsional: mungkin 1 email saja untuk bundle?)
                    // Untuk sekarang kita biarkan per kursus agar user dapat info lengkap
                    try {
                        Mail::to($order->user->email)->send(
                            new EnrollmentConfirmationMail($order->user, $course)
                        );
                    } catch (\Throwable $e) {
                        Log::warning('Bundle enrollment email failed', ['error' => $e->getMessage()]);
                    }
                }

                // Kirim notifikasi in-app untuk bundle
                try {
                    send_notification(
                        user: $order->user,
                        type: 'course',
                        title: '🎁 Paket Kursus Aktif!',
                        message: "Selamat! Paket \"{$bundle->title}\" kamu sudah aktif. Silakan mulai belajar semua kursusnya!",
                        url: route('dashboard.courses'),
                    );
                } catch (\Throwable $e) {
                    Log::warning('Bundle enrollment notification failed', ['error' => $e->getMessage()]);
                }

                Log::info('BundleEnrollment processed after payment success', [
                    'user_id'   => $order->user_id,
                    'bundle_id' => $bundle->id,
                    'order'     => $order->order_number,
                ]);
            }
        });
    }
}
