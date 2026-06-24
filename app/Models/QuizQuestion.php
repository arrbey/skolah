<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizQuestion extends Model
{
    protected $fillable = [
        'quiz_id',
        'question',
        'type',
        'explanation',
        'points',
        'order',
    ];

    protected $casts = [
        'points' => 'integer',
        'order'  => 'integer',
    ];

    // ── Relasi ──────────────────────────────────────────────────────────────

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuizOption::class, 'question_id')->orderBy('order');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QuizAnswer::class, 'question_id');
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    public function isMultipleChoice(): bool
    {
        return $this->type === 'multiple_choice';
    }

    public function isTrueFalse(): bool
    {
        return $this->type === 'true_false';
    }

    public function isEssay(): bool
    {
        return $this->type === 'essay';
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'multiple_choice' => 'Pilihan Ganda',
            'true_false'      => 'Benar/Salah',
            'essay'           => 'Essay',
            default           => $this->type,
        };
    }

    public function getCorrectOptionAttribute(): ?QuizOption
    {
        return $this->options->firstWhere('is_correct', true);
    }
}
