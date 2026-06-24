<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    protected $fillable = [
        'course_id',
        'type',
        'title',
        'description',
        'passing_score',
        'time_limit',
        'is_active',
        'show_result',
        'randomize_questions',
    ];

    protected $casts = [
        'is_active'           => 'boolean',
        'show_result'         => 'boolean',
        'randomize_questions' => 'boolean',
        'passing_score'       => 'integer',
        'time_limit'          => 'integer',
    ];

    // ── Relasi ──────────────────────────────────────────────────────────────

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('order');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    public function isPretest(): bool
    {
        return $this->type === 'pretest';
    }

    public function isPosttest(): bool
    {
        return $this->type === 'posttest';
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'pretest' ? 'Pre-Test' : 'Post-Test';
    }

    public function getTimeLimitLabelAttribute(): string
    {
        return $this->time_limit ? $this->time_limit . ' menit' : 'Tanpa batas waktu';
    }

    public function getTotalQuestionsAttribute(): int
    {
        return $this->questions()->count();
    }

    public function getTotalPointsAttribute(): int
    {
        return $this->questions()->sum('points');
    }

    /** Cek apakah user sudah pernah attempt quiz ini */
    public function hasAttemptByUser(int $userId): bool
    {
        return $this->attempts()->where('user_id', $userId)->whereNotNull('completed_at')->exists();
    }

    /** Ambil attempt terakhir user */
    public function latestAttemptByUser(int $userId): ?QuizAttempt
    {
        return $this->attempts()
            ->where('user_id', $userId)
            ->whereNotNull('completed_at')
            ->latest()
            ->first();
    }
}
