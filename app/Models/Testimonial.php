<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'content',
        'rating',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'rating'      => 'integer',
            'is_featured' => 'boolean',
        ];
    }

    // ── Relasi ────────────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeHighRated(Builder $query, int $min = 4): Builder
    {
        return $query->where('rating', '>=', $min);
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getStarsAttribute(): array
    {
        $filled = (int) $this->rating;

        return [
            'filled' => $filled,
            'empty'  => 5 - $filled,
        ];
    }
}
