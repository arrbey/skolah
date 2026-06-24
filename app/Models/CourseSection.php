<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
        ];
    }

    // ── Relasi ────────────────────────────────────────────────────────────────

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(CourseLesson::class, 'section_id')->orderBy('order');
    }

    public function publishedLessons(): HasMany
    {
        return $this->hasMany(CourseLesson::class, 'section_id')
            ->where('is_published', true)
            ->orderBy('order');
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order');
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getTotalDurationAttribute(): int
    {
        return $this->lessons()->sum('video_duration');
    }

    public function getTotalDurationFormattedAttribute(): string
    {
        return formatDuration($this->total_duration);
    }

    public function getTotalLessonsAttribute(): int
    {
        return $this->lessons()->count();
    }
}
