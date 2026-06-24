<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'image',
        'link',
        'position',
        'order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'order'     => 'integer',
            'is_active' => 'boolean',
        ];
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeByPosition(Builder $query, string $position): Builder
    {
        return $query->where('position', $position);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order');
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getImageUrlAttribute(): string
    {
        return storageUrl($this->image, asset('images/placeholder-banner.jpg'));
    }
}
