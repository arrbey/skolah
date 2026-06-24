<?php

namespace App\Models;

use App\Services\MinioStorageService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseLesson extends Model
{
    use HasFactory;

    protected $table = 'course_lessons';

    protected $fillable = [
        'section_id',
        'title',
        'video_url',
        'video_type',
        'video_duration',
        'video_duration_seconds',
        'video_file_size',
        'content',
        'order',
        'is_free_preview',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'video_duration'         => 'integer',
            'video_duration_seconds' => 'integer',
            'video_file_size'        => 'integer',
            'order'                  => 'integer',
            'is_free_preview'        => 'boolean',
            'is_published'           => 'boolean',
        ];
    }

    // ── Relasi ────────────────────────────────────────────────────────────────

    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'section_id');
    }

    public function progress(): HasMany
    {
        return $this->hasMany(LessonProgress::class, 'lesson_id');
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeFreePreview(Builder $query): Builder
    {
        return $query->where('is_free_preview', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order');
    }

    // ── Video Type Helpers ─────────────────────────────────────────────────────

    /** Apakah video bersumber dari YouTube? */
    public function isYoutube(): bool
    {
        return ($this->video_type ?? 'youtube') === 'youtube';
    }

    /** Apakah video diupload ke MinIO? */
    public function isMinioVideo(): bool
    {
        return $this->video_type === 'minio';
    }

    /** Ambil YouTube Video ID dari berbagai format URL */
    public function getYoutubeId(): ?string
    {
        if (! $this->isYoutube() || ! $this->video_url) return null;
        preg_match('/(?:v=|youtu\.be\/|embed\/)([a-zA-Z0-9_\-]{11})/', $this->video_url, $m);
        return $m[1] ?? null;
    }

    /** Embed URL YouTube — langsung pakai di <iframe src> */
    public function getYoutubeEmbedUrl(): ?string
    {
        $id = $this->getYoutubeId();
        return $id
            ? "https://www.youtube.com/embed/{$id}?rel=0&modestbranding=1"
            : null;
    }

    /**
     * Generate Signed URL MinIO untuk streaming video.
     * ⚠️ Panggil di controller/view satu kali — JANGAN di dalam loop.
     */
    public function getMinioVideoUrl(): ?string
    {
        if (! $this->isMinioVideo() || ! $this->video_url) return null;
        return app(MinioStorageService::class)->getLmsVideoUrl($this->video_url);
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    /** Format durasi detik → "mm:ss" atau "hh:mm:ss" */
    public function getFormattedDurationAttribute(): string
    {
        $s = $this->video_duration_seconds ?? 0;

        if ($s <= 0) {
            // Fallback ke video_duration (menit) jika ada
            return $this->video_duration ? formatDuration($this->video_duration) : '';
        }

        $h   = (int) floor($s / 3600);
        $m   = (int) floor(($s % 3600) / 60);
        $sec = $s % 60;

        return $h > 0
            ? sprintf('%d:%02d:%02d', $h, $m, $sec)
            : sprintf('%d:%02d', $m, $sec);
    }

    /** @deprecated — gunakan getFormattedDurationAttribute() */
    public function getDurationFormattedAttribute(): string
    {
        return $this->getFormattedDurationAttribute();
    }

    public function getYoutubeIdAttribute(): ?string
    {
        return $this->getYoutubeId();
    }

    public function getEmbedUrlAttribute(): ?string
    {
        return $this->getYoutubeEmbedUrl();
    }

    public function getVideoThumbnailAttribute(): ?string
    {
        $id = $this->getYoutubeId();
        return $id ? "https://img.youtube.com/vi/{$id}/maxresdefault.jpg" : null;
    }

    // ── Helper Methods ────────────────────────────────────────────────────────

    /** Cek apakah lesson sudah selesai oleh user tertentu. */
    public function isCompletedByUser(int $userId): bool
    {
        return $this->progress()
            ->where('user_id', $userId)
            ->where('is_completed', true)
            ->exists();
    }
}

