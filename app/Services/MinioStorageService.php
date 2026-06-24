<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;

class MinioStorageService
{
    protected string $disk = 's3';

    // ══════════════════════════════════════════════════════════════════════════
    // PUBLIC FILES — GAMBAR (URL lengkap disimpan ke DB)
    // ══════════════════════════════════════════════════════════════════════════

    public function upload(UploadedFile $file, string $folder, int $maxWidth = 1280): string
    {
        return $this->uploadImage($file, $folder, $maxWidth);
    }

    public function uploadCourseThumbnail(UploadedFile $file, string $courseSlug): string
    {
        return $this->uploadImage(
            $file,
            config('minio.paths.course_thumbnail') . "/{$courseSlug}",
            1280,
            85
        );
    }

    public function uploadUserAvatar(UploadedFile $file, int $userId): string
    {
        $this->deleteFolder(config('minio.paths.user_avatar') . "/{$userId}");
        return $this->uploadImage(
            $file,
            config('minio.paths.user_avatar') . "/{$userId}",
            400,
            90
        );
    }

    public function uploadBanner(UploadedFile $file): string
    {
        return $this->uploadImage($file, config('minio.paths.banner'), 1920, 85);
    }

    public function uploadBookCover(UploadedFile $file, string $bookSlug): string
    {
        return $this->uploadImage(
            $file,
            config('minio.paths.book_cover') . "/{$bookSlug}",
            600,
            90
        );
    }

    public function uploadBootcampThumbnail(UploadedFile $file, string $bootcampSlug): string
    {
        return $this->uploadImage(
            $file,
            config('minio.paths.bootcamp_thumbnail') . "/{$bootcampSlug}",
            1280,
            85
        );
    }

    public function uploadPromoImage(UploadedFile $file): string
    {
        return $this->uploadImage($file, 'promos', 1280, 85);
    }

    public function uploadCategoryIcon(UploadedFile $file): string
    {
        return $this->uploadImage($file, config('minio.paths.category_icon'), 256, 90);
    }

    public function uploadTestimonialPhoto(UploadedFile $file): string
    {
        return $this->uploadImage($file, config('minio.paths.testimonial_photo'), 300, 85);
    }

    /**
     * Upload foto bukti pengiriman buku (delivery proof)
     * Return: URL publik lengkap
     */
    public function uploadDeliveryPhoto(UploadedFile $file, int $bookOrderId): string
    {
        return $this->uploadImage(
            $file,
            config('minio.paths.delivery_photo') . "/{$bookOrderId}",
            1200,
            85
        );
    }

    // ══════════════════════════════════════════════════════════════════════════
    // PRIVATE FILES — VIDEO LMS (PATH disimpan ke DB, URL via signed)
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Upload video lesson ke MinIO (PRIVATE).
     * Return: PATH relatif — disimpan ke kolom video_url di DB.
     */
    public function uploadLmsVideo(
        UploadedFile $file,
        int $courseId,
        int $lessonId
    ): string {
        $this->validateVideo($file);

        $filename = Str::random(40) . '.mp4';
        $dir      = config('minio.paths.lms_video') . "/{$courseId}/{$lessonId}";
        $path     = "{$dir}/{$filename}";

        // Stream upload — aman untuk file besar hingga 2GB
        Storage::disk($this->disk)->putFileAs(
            $dir,
            $file,
            $filename,
            ['ContentType' => 'video/mp4', 'visibility' => 'private']
        );

        return $path; // PATH saja — bukan URL lengkap
    }

    /**
     * Upload video yang sudah dikompres dari file lokal ke MinIO (PRIVATE).
     */
    public function uploadLmsVideoFromLocal(string $localPath, int $courseId, int $lessonId): string
    {
        $filename = Str::random(40) . '.mp4';
        $dir      = config('minio.paths.lms_video') . "/{$courseId}/{$lessonId}";
        $path     = "{$dir}/{$filename}";

        Storage::disk($this->disk)->putFileAs(
            $dir,
            new \Illuminate\Http\File($localPath),
            $filename,
            ['ContentType' => 'video/mp4', 'visibility' => 'private']
        );

        return $path;
    }

    /**
     * Generate Signed URL untuk streaming video.
     * Expired sesuai MINIO_VIDEO_EXPIRY menit (default 120).
     */
    public function getLmsVideoUrl(string $videoPath): string
    {
        return Storage::disk($this->disk)->temporaryUrl(
            $videoPath,
            now()->addMinutes(config('minio.expiry.video', 120))
        );
    }

    public function deleteLmsVideo(string $videoPath): void
    {
        if ($videoPath && Storage::disk($this->disk)->exists($videoPath)) {
            Storage::disk($this->disk)->delete($videoPath);
        }
    }

    // ══════════════════════════════════════════════════════════════════════════
    // PRIVATE FILES — BUKU DIGITAL
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Upload PDF buku digital ke MinIO (PRIVATE).
     * Return: PATH relatif.
     */
    public function uploadBookFile(UploadedFile $file, int $bookId): string
    {
        $this->validatePdf($file);

        $path = config('minio.paths.book_file') . "/{$bookId}/" . Str::random(40) . '.pdf';

        Storage::disk($this->disk)->put($path, file_get_contents($file->getRealPath()), [
            'ContentType' => 'application/pdf',
            'visibility'  => 'private',
        ]);

        return $path;
    }

    /**
     * Generate Signed URL untuk download buku digital.
     * Expired sesuai MINIO_BOOK_EXPIRY menit (default 15).
     */
    public function getBookDownloadUrl(string $filePath): string
    {
        return Storage::disk($this->disk)->temporaryUrl(
            $filePath,
            now()->addMinutes(config('minio.expiry.book', 15))
        );
    }

    // ══════════════════════════════════════════════════════════════════════════
    // PRIVATE FILES — SERTIFIKAT
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Upload PDF sertifikat ke MinIO (PRIVATE).
     * Return: PATH relatif.
     */
    public function uploadCertificate(string $pdfContent, int $userId, string $certNumber): string
    {
        $path = config('minio.paths.certificate') . "/{$userId}/{$certNumber}.pdf";

        Storage::disk($this->disk)->put($path, $pdfContent, [
            'ContentType' => 'application/pdf',
            'visibility'  => 'private',
        ]);

        return $path;
    }

    /**
     * Generate Signed URL untuk download sertifikat.
     * Expired sesuai MINIO_CERT_EXPIRY menit (default 30).
     */
    public function getCertificateDownloadUrl(string $filePath): string
    {
        return Storage::disk($this->disk)->temporaryUrl(
            $filePath,
            now()->addMinutes(config('minio.expiry.certificate', 30))
        );
    }

    // ══════════════════════════════════════════════════════════════════════════
    // DELETE
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Hapus file dari MinIO (bisa URL lengkap atau PATH relatif).
     *
     * Juga hapus sibling `.webp` secara otomatis agar tidak ada orphan file
     * saat user delete original. Silent kalau .webp tidak ada (file lama).
     */
    public function delete(?string $pathOrUrl): void
    {
        if (! $pathOrUrl) return;

        $path = $this->extractPath($pathOrUrl);

        if (! $path) return;

        if (Storage::disk($this->disk)->exists($path)) {
            Storage::disk($this->disk)->delete($path);
        }

        // Hapus WebP sibling kalau ada (jpg/png → webp)
        if (preg_match('/\.(jpe?g|png)$/i', $path)) {
            $webpPath = preg_replace('/\.(jpe?g|png)$/i', '.webp', $path);
            if ($webpPath && Storage::disk($this->disk)->exists($webpPath)) {
                Storage::disk($this->disk)->delete($webpPath);
            }
        }
    }

    /**
     * Hapus semua file dalam sebuah folder di MinIO.
     */
    public function deleteFolder(string $folder): void
    {
        try {
            $files = Storage::disk($this->disk)->files($folder);
            foreach ($files as $file) {
                Storage::disk($this->disk)->delete($file);
            }
        } catch (\Throwable) {
            // Folder tidak ada atau kosong — abaikan
        }
    }

    // ══════════════════════════════════════════════════════════════════════════
    // INTERNAL HELPERS
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Upload, resize original + generate WebP sibling, simpan ke MinIO.
     *
     * Strategy 2 (auto-convert + fallback):
     * - Original format (JPG/PNG) TETAP disimpan → backward compat untuk DomPDF,
     *   email, OG images, browser lama.
     * - WebP variant dibuat di path yang sama dengan extension `.webp` →
     *   dipakai via <picture> tag untuk browser modern (~30% lebih kecil).
     * - GIF di-skip untuk WebP (animasi spotty support, lebih baik pakai asli).
     *
     * Return: URL LENGKAP publik file ORIGINAL (kompatibel dengan kode existing).
     */
    protected function uploadImage(
        UploadedFile $file,
        string $folder,
        int $maxWidth = 1280,
        int $quality  = 85
    ): string {
        $this->validateImage($file);

        // Pertahankan format asli: jpg/jpeg → jpg, png → png, gif → gif
        $mime      = $file->getMimeType();
        $extension = match (true) {
            in_array($mime, ['image/jpeg', 'image/jpg']) => 'jpg',
            $mime === 'image/png'                        => 'png',
            $mime === 'image/gif'                        => 'gif',
            default                                      => 'jpg',
        };
        $contentType = match ($extension) {
            'png'   => 'image/png',
            'gif'   => 'image/gif',
            default => 'image/jpeg',
        };

        // Nama file RANDOM — base tanpa extension (pakai lagi untuk WebP sibling)
        $basename = Str::random(40);
        $filename = $basename . '.' . $extension;
        $path     = "{$folder}/{$filename}";

        // Intervention Image v3 — GD driver
        $manager = new ImageManager(new GdDriver());
        $image   = $manager->read($file->getRealPath());

        // Resize hanya jika lebar melebihi maxWidth (jangan upscale)
        if ($image->width() > $maxWidth) {
            $image->scaleDown(width: $maxWidth);
        }

        // ── 1) Upload ORIGINAL (JPG/PNG/GIF) ─────────────────────────────────
        $encoded = match ($extension) {
            'png'   => $image->toPng(),
            'gif'   => $image->toGif(),
            default => $image->toJpeg($quality),
        };

        Storage::disk($this->disk)->put($path, (string) $encoded, [
            'visibility'   => 'public',
            'ContentType'  => $contentType,
            'CacheControl' => 'public, max-age=31536000',
        ]);

        // ── 2) Upload WebP sibling (kecuali GIF) ─────────────────────────────
        // Kalau gagal → jangan break upload. Log saja, UI tetap fallback ke original.
        if ($extension !== 'gif') {
            try {
                $webpPath    = "{$folder}/{$basename}.webp";
                $webpEncoded = $image->toWebp($quality);

                Storage::disk($this->disk)->put($webpPath, (string) $webpEncoded, [
                    'visibility'   => 'public',
                    'ContentType'  => 'image/webp',
                    'CacheControl' => 'public, max-age=31536000',
                ]);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('WebP sibling generation failed', [
                    'path'  => $path,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $this->buildPublicUrl($path);
    }

    /**
     * Bangun URL publik dari path relatif.
     */
    protected function buildPublicUrl(string $path): string
    {
        return rtrim(config('minio.public_url'), '/') . '/' . ltrim($path, '/');
    }

    /**
     * Ekstrak path relatif dari URL publik MinIO.
     */
    protected function extractPath(string $url): ?string
    {
        $base = rtrim(config('minio.public_url'), '/');

        if (str_starts_with($url, $base)) {
            return ltrim(str_replace($base, '', $url), '/');
        }

        // Sudah berupa path relatif
        return $url;
    }

    /**
     * Upload background sertifikat ke MinIO (PUBLIC).
     * Return: PATH relatif (disimpan ke DB, URL dibangun saat render).
     */
    public function uploadCertificateBackground(UploadedFile $file): string
    {
        $this->validateImage($file, 10 * 1024 * 1024); // max 10MB untuk background

        $mime      = (new \finfo(FILEINFO_MIME_TYPE))->file($file->getRealPath());
        $extension = match (true) {
            in_array($mime, ['image/jpeg', 'image/jpg']) => 'jpg',
            $mime === 'image/png'                        => 'png',
            default                                      => 'jpg',
        };

        $filename = Str::random(40) . '.' . $extension;
        $path     = "certificate-backgrounds/{$filename}";

        Storage::disk($this->disk)->put($path, file_get_contents($file->getRealPath()), [
            'visibility'  => 'public',
            'ContentType' => $mime,
        ]);

        return $path;
    }

    /**
     * Validasi file gambar (MIME server-side + ukuran + path traversal).
     */
    protected function validateImage(UploadedFile $file, ?int $maxBytes = null): void
    {
        $this->checkPathTraversal($file);

        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($file->getRealPath());

        if (! in_array($mime, ['image/jpeg', 'image/png', 'image/webp', 'image/gif'])) {
            throw new \InvalidArgumentException("Tipe file gambar tidak diizinkan: {$mime}.");
        }

        $maxSize = $maxBytes ?? (10 * 1024 * 1024); // default 10MB
        if ($file->getSize() > $maxSize) {
            $maxMB = round($maxSize / 1024 / 1024);
            throw new \InvalidArgumentException("Ukuran gambar maksimal {$maxMB}MB.");
        }

        if (@getimagesize($file->getRealPath()) === false) {
            throw new \InvalidArgumentException('File bukan gambar yang valid.');
        }
    }

    /**
     * Validasi file video (MIME + ukuran maksimal 2GB + path traversal).
     */
    protected function validateVideo(UploadedFile $file): void
    {
        $this->checkPathTraversal($file);

        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($file->getRealPath());

        if (! in_array($mime, ['video/mp4', 'video/quicktime', 'video/x-msvideo'])) {
            throw new \InvalidArgumentException('Format video tidak didukung. Gunakan MP4.');
        }

        if ($file->getSize() > 2 * 1024 * 1024 * 1024) {
            throw new \InvalidArgumentException('Ukuran video maksimal 2GB.');
        }
    }

    /**
     * Validasi file PDF (MIME + ukuran maksimal 50MB + path traversal).
     */
    protected function validatePdf(UploadedFile $file): void
    {
        $this->checkPathTraversal($file);

        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($file->getRealPath());

        if ($mime !== 'application/pdf') {
            throw new \InvalidArgumentException('File harus berformat PDF.');
        }

        if ($file->getSize() > 50 * 1024 * 1024) {
            throw new \InvalidArgumentException('Ukuran PDF maksimal 50MB.');
        }
    }

    /**
     * Cegah path traversal — tolak file dengan karakter berbahaya di nama asli.
     */
    protected function checkPathTraversal(UploadedFile $file): void
    {
        $name = $file->getClientOriginalName();

        if (preg_match('/[\/\\\\:*?"<>|]/', $name) || str_contains($name, '..')) {
            throw new \InvalidArgumentException('Nama file mengandung karakter tidak valid.');
        }
    }
}
