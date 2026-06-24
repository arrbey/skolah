<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\CertificateTemplate;
use App\Models\Course;
use App\Models\CourseEnrollment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    /**
     * Generate (atau re-download) sertifikat penyelesaian kursus.
     *
     * Route: GET /certificates/{courseSlug}/download
     */
    public function download(Request $request, string $courseSlug)
    {
        /** @var \App\Models\User $user */
        $user   = $request->user();
        $course = Course::where('slug', $courseSlug)->firstOrFail();

        // ── 1. Cek enrollment ──────────────────────────────────────────────
        $enrollment = CourseEnrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if (! $enrollment) {
            return redirect()->route('courses.show', $course->slug)
                ->with('error', 'Kamu belum terdaftar di kursus ini.');
        }

        // ── 2. Cek completion (progress 100%) ──────────────────────────────
        if ($enrollment->progress_percentage < 100) {
            return redirect()->route('learn', $course->slug)
                ->with('warning', "Selesaikan semua pelajaran terlebih dahulu untuk mendapatkan sertifikat. Progress kamu: {$enrollment->progress_percentage}%");
        }

        // ── 3. Cek apakah sertifikat sudah ada ───────────────────────────
        $certificate = Certificate::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if (! $certificate) {
            // ── 4. Buat sertifikat baru ──────────────────────────────────
            $certificate = Certificate::create([
                'user_id'            => $user->id,
                'course_id'          => $course->id,
                'certificate_number' => 'TEMP', // akan diupdate setelah insert
                'issued_at'          => now(),
                'file_path'          => null,
            ]);

            // Generate nomor berdasarkan ID yang baru di-insert
            $certNumber = Certificate::generateNumber($certificate->id);
            $certificate->update(['certificate_number' => $certNumber]);
        }

        $certNumber = $certificate->certificate_number;
        $filePath   = "certificates/{$user->id}/{$certNumber}.pdf";

        // ── 5. Ambil template aktif & cek apakah perlu regenerate ─────────
        $template = CertificateTemplate::getActive();

        if ($certificate->needsRegeneration($template)) {

            // Hapus PDF lama jika ada
            if ($certificate->file_path && Storage::exists($certificate->file_path)) {
                Storage::delete($certificate->file_path);
            }

            $pdf = Pdf::loadView('pdf.certificate', [
                'certificate' => $certificate,
                'user'        => $user,
                'course'      => $course,
                'issuedAt'    => $certificate->issued_at,
                'template'    => $template,
            ])
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'defaultFont'          => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
                'dpi'                  => 96,
            ]);

            // Simpan ke storage
            Storage::put($filePath, $pdf->output());

            // Update file_path & template_id di DB
            $certificate->update([
                'file_path'   => $filePath,
                'template_id' => $template->id ?? null,
            ]);
        }

        // ── 6. Stream PDF sebagai download ────────────────────────────────
        $filename = "Sertifikat-{$certNumber}.pdf";

        if (Storage::exists($filePath)) {
            return response()->streamDownload(function () use ($filePath) {
                echo Storage::get($filePath);
            }, $filename, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ]);
        }

        // Fallback: re-render langsung download tanpa cache
        $pdf = Pdf::loadView('pdf.certificate', [
            'certificate' => $certificate,
            'user'        => $user,
            'course'      => $course,
            'issuedAt'    => $certificate->issued_at,
            'template'    => $template,
        ])
        ->setPaper('a4', 'landscape')
        ->setOptions([
            'defaultFont'          => 'DejaVu Sans',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => true,
            'dpi'                  => 96,
        ]);

        return $pdf->download($filename);
    }

    /**
     * Download sertifikat via certificate number (SKOL-2026-000001).
     * Redirect ke route utama menggunakan course slug.
     *
     * Route: GET /certificates/{certNumber}/download
     */
    public function downloadByCertNumber(Request $request, string $certNumber)
    {
        $certificate = Certificate::where('certificate_number', $certNumber)
            ->with('course:id,slug')
            ->firstOrFail();

        // Policy: hanya pemilik sertifikat atau admin
        $this->authorize('download', $certificate);

        if (! $certificate->course) {
            abort(404, 'Sertifikat tidak ditemukan.');
        }

        // Redirect ke route utama dengan course slug
        return redirect()->route('certificates.download', [
            'courseSlug' => $certificate->course->slug,
        ]);
    }

    /**
     * Halaman verifikasi sertifikat publik.
     * Siapapun (tanpa login) bisa verifikasi keaslian sertifikat.
     *
     * Route: GET /verify/{certificateNumber}
     */
    public function verify(string $certificateNumber)
    {
        $certificate = Certificate::where('certificate_number', $certificateNumber)
            ->with(['user:id,name,avatar', 'course:id,slug,title,thumbnail,instructor_id'])
            ->first();

        if (! $certificate) {
            return view('pages.certificates.verify', [
                'certificate' => null,
                'valid'       => false,
            ]);
        }

        return view('pages.certificates.verify', [
            'certificate' => $certificate,
            'valid'       => true,
        ]);
    }
}
