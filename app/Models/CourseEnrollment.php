<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseEnrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'course_variant_id',
        'enrolled_at',
        'completed_at',
        'progress_percentage',
    ];

    protected function casts(): array
    {
        return [
            'enrolled_at'         => 'datetime',
            'completed_at'        => 'datetime',
            'progress_percentage' => 'integer',
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

    public function variant(): BelongsTo
    {
        return $this->belongsTo(CourseVariant::class, 'course_variant_id');
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->whereNotNull('completed_at')
                     ->where('progress_percentage', 100);
    }

    public function scopeInProgress(Builder $query): Builder
    {
        return $query->whereNull('completed_at')
                     ->where('progress_percentage', '>', 0);
    }

    public function scopeNotStarted(Builder $query): Builder
    {
        return $query->where('progress_percentage', 0);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForCourse(Builder $query, int $courseId): Builder
    {
        return $query->where('course_id', $courseId);
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getIsCompletedAttribute(): bool
    {
        return $this->progress_percentage >= 100 && ! is_null($this->completed_at);
    }

    public function getProgressLabelAttribute(): string
    {
        if ($this->is_completed) return 'Selesai';
        if ($this->progress_percentage === 0) return 'Belum Dimulai';

        return "Progres {$this->progress_percentage}%";
    }

    public function getEnrolledAtFormattedAttribute(): string
    {
        return tanggal_indo($this->enrolled_at);
    }
}
