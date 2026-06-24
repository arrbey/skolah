<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'rating',
        'review',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
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

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeHighRated(Builder $query, int $min = 4): Builder
    {
        return $query->where('rating', '>=', $min);
    }

    public function scopeForCourse(Builder $query, int $courseId): Builder
    {
        return $query->where('course_id', $courseId);
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getStarsAttribute(): array
    {
        return [
            'filled' => $this->rating,
            'empty'  => 5 - $this->rating,
        ];
    }

    public function getCreatedAtFormattedAttribute(): string
    {
        return tanggal_indo($this->created_at);
    }
}
