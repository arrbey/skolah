<?php

namespace App\Observers;

use App\Models\Certificate;
use App\Models\CourseEnrollment;
use App\Notifications\AppNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class CourseEnrollmentObserver
{
    /**
     * Dipanggil setiap kali CourseEnrollment di-update.
     * Jika progress_percentage mencapai 100%, otomatis generate sertifikat
     * dan kirim notifikasi ke user.
     */
    public function updated(CourseEnrollment $enrollment): void
    {
        // Hanya proses jika progress baru saja mencapai 100%
        if (
            $enrollment->progress_percentage < 100 ||
            ! $enrollment->wasChanged('progress_percentage')
        ) {
            return;
        }

        // Update completed_at jika belum diset
        if (! $enrollment->completed_at) {
            $enrollment->updateQuietly(['completed_at' => now()]);
        }

        // Cek apakah sertifikat sudah ada
        $existing = Certificate::where('user_id', $enrollment->user_id)
            ->where('course_id', $enrollment->course_id)
            ->first();

        if ($existing) {
            return; // Sudah ada, skip
        }

        // ── Buat sertifikat baru ──────────────────────────────────────────
        $certificate = Certificate::create([
            'user_id'            => $enrollment->user_id,
            'course_id'          => $enrollment->course_id,
            'certificate_number' => 'TEMP',
            'issued_at'          => now(),
            'file_path'          => null,
        ]);

        $certNumber = Certificate::generateNumber($certificate->id);
        $certificate->update(['certificate_number' => $certNumber]);

        // ── Generate PDF ──────────────────────────────────────────────────
        try {
            $user   = $enrollment->user;
            $course = $enrollment->course;

            if ($user && $course) {
                $pdf = Pdf::loadView('pdf.certificate', [
                    'certificate' => $certificate,
                    'user'        => $user,
                    'course'      => $course,
                    'issuedAt'    => $certificate->issued_at,
                ])
                ->setPaper('a4', 'landscape')
                ->setOptions([
                    'defaultFont'          => 'DejaVu Sans',
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled'      => false,
                    'dpi'                  => 150,
                ]);

                $filePath = "certificates/{$user->id}/{$certNumber}.pdf";
                Storage::put($filePath, $pdf->output());
                $certificate->update(['file_path' => $filePath]);

                // ── Kirim notifikasi ke user ──────────────────────────────
                $user->notify(new AppNotification(
                    type: 'cert',
                    title: '🏆 Sertifikat Siap Diunduh!',
                    message: "Selamat! Kamu telah menyelesaikan kursus \"{$course->title}\". Sertifikat dengan nomor {$certNumber} sudah tersedia.",
                    url: route('certificates.download', ['courseSlug' => $course->slug]),
                ));
            }
        } catch (\Throwable $e) {
            // Jangan sampai crash proses belajar jika generate PDF gagal
            \Illuminate\Support\Facades\Log::error('Auto-certificate generation failed', [
                'enrollment_id' => $enrollment->id,
                'error'         => $e->getMessage(),
            ]);
        }
    }
}
