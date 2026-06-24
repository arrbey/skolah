<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'parent_id',
    ];

    // ── Boot ──────────────────────────────────────────────────────────────────

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Category $category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    // ── Relasi ────────────────────────────────────────────────────────────────

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeParents(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    public function scopeChildren(Builder $query): Builder
    {
        return $query->whereNotNull('parent_id');
    }

    public function scopeWithCourseCount(Builder $query): Builder
    {
        return $query->withCount(['courses' => fn ($q) => $q->where('status', 'published')]);
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getIsParentAttribute(): bool
    {
        return is_null($this->parent_id);
    }

    public function getUrlAttribute(): string
    {
        return route('courses.index', ['category' => $this->slug]);
    }
}
