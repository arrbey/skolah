<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'quiz_id',
        'score',
        'total_points',
        'earned_points',
        'passed',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'score'        => 'integer',
        'total_points' => 'integer',
        'earned_points'=> 'integer',
        'passed'       => 'boolean',
        'started_at'   => 'datetime',
        'completed_at' => 'datetime',
    ];

    // ── Relasi ──────────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QuizAnswer::class, 'attempt_id');
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    public function getDurationAttribute(): ?string
    {
        if (!$this->started_at || !$this->completed_at) return null;

        $minutes = $this->started_at->diffInMinutes($this->completed_at);
        $seconds = $this->started_at->diffInSeconds($this->completed_at) % 60;

        return $minutes > 0
            ? "{$minutes} menit {$seconds} detik"
            : "{$seconds} detik";
    }

    public function getStatusLabelAttribute(): string
    {
        if (!$this->completed_at) return 'Belum selesai';
        return $this->passed ? 'Lulus' : 'Tidak Lulus';
    }

    public function getStatusColorAttribute(): string
    {
        if (!$this->completed_at) return 'gray';
        return $this->passed ? 'green' : 'red';
    }
}
