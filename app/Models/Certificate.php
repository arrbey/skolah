<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'certificate_number',
        'issued_at',
        'file_path',
        'template_id',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
        ];
    }

    // ── Relasi ────────────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(CertificateTemplate::class, 'template_id');
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getIssuedAtFormattedAttribute(): string
    {
        return tanggal_indo($this->issued_at);
    }

    public function getDownloadUrlAttribute(): string
    {
        return route('certificates.download', [
            'courseSlug' => $this->course->slug ?? $this->course_id,
        ]);
    }

    public function getFileExistsAttribute(): bool
    {
        return $this->file_path && Storage::exists($this->file_path);
    }

    // ── Business Logic ────────────────────────────────────────────────────────

    /**
     * Apakah PDF sertifikat perlu di-generate ulang?
     * True jika: file belum ada, atau template aktif sudah berubah/diupdate.
     */
    public function needsRegeneration(?CertificateTemplate $activeTemplate = null): bool
    {
        // Belum pernah generate
        if (! $this->file_path || ! Storage::exists($this->file_path)) {
            return true;
        }

        // Template belum pernah di-track (sertifikat lama)
        if (! $this->template_id) {
            return true;
        }

        // Tidak ada template aktif (fallback default) — regenerate
        if (! $activeTemplate || ! $activeTemplate->id) {
            return true;
        }

        // Template aktif berubah (misalnya admin ganti ke template lain)
        if ($this->template_id !== $activeTemplate->id) {
            return true;
        }

        // Template sama tapi sudah diupdate (edit posisi, background, dll)
        if ($activeTemplate->updated_at->gt($this->updated_at)) {
            return true;
        }

        return false;
    }

    // ── Static Helpers ────────────────────────────────────────────────────────

    /**
     * Generate nomor sertifikat: SKOL-2025-000001
     */
    public static function generateNumber(int $id): string
    {
        return 'SKOL-' . now()->year . '-' . str_pad($id, 6, '0', STR_PAD_LEFT);
    }
}
