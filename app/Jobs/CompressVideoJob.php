<?php

namespace App\Jobs;

use App\Models\CourseLesson;
use App\Services\MinioStorageService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use FFMpeg\Format\Video\X264;

class CompressVideoJob implements ShouldQueue
{
    use Queueable;

    public $timeout = 3600; // 1 jam maksimal eksekusi

    protected $lessonId;
    protected $localPath;
    protected $courseId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $lessonId, int $courseId, string $localPath)
    {
        $this->lessonId = $lessonId;
        $this->courseId = $courseId;
        $this->localPath = $localPath;
    }

    /**
     * Execute the job.
     */
    public function handle(MinioStorageService $minioService): void
    {
        $lesson = CourseLesson::find($this->lessonId);
        if (!$lesson) {
            $this->cleanup();
            return;
        }

        try {
            $lesson->update(['processing_status' => 'processing']);

            // Lokasi hasil kompresi sementara
            $compressedFilename = 'compressed_' . basename($this->localPath);
            $compressedPath = storage_path('app/temp/' . $compressedFilename);

            Log::info("Memulai kompresi video: {$this->localPath} -> {$compressedPath}");

            // Setup format kompresi H.264
            $format = new X264();
            $format->setKiloBitrate(1000); // Batasi bitrate menjadi 1Mbps agar ringan

            FFMpeg::fromDisk('local') // assumes temp files are in storage/app/temp
                ->open('temp/' . basename($this->localPath))
                ->export()
                ->toDisk('local')
                ->inFormat($format)
                ->save('temp/' . $compressedFilename);

            Log::info("Kompresi selesai. Mengunggah ke MinIO...");

            // Hapus file lama di minio (jika ada update)
            if ($lesson->video_url) {
                $minioService->deleteLmsVideo($lesson->video_url);
            }

            // Upload hasil kompresi
            $videoUrl = $minioService->uploadLmsVideoFromLocal($compressedPath, $this->courseId, $this->lessonId);
            $fileSize = File::size($compressedPath);

            // Update database dengan video baru dan status ready
            $lesson->update([
                'video_url' => $videoUrl,
                'video_file_size' => $fileSize,
                'processing_status' => 'ready',
            ]);

            Log::info("Video lesson {$this->lessonId} berhasil diproses dan disimpan.");

        } catch (\Exception $e) {
            Log::error("Gagal mengompresi video lesson {$this->lessonId}: " . $e->getMessage());
            $lesson->update(['processing_status' => 'failed']);
            throw $e;
        } finally {
            $this->cleanup();
            // Juga hapus file compressed
            if (isset($compressedPath) && File::exists($compressedPath)) {
                File::delete($compressedPath);
            }
        }
    }

    protected function cleanup()
    {
        if (File::exists($this->localPath)) {
            File::delete($this->localPath);
        }
    }
}
