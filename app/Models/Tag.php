<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    // ── Boot ──────────────────────────────────────────────────────────────────

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Tag $tag) {
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }

    // ── Relasi ────────────────────────────────────────────────────────────────

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_tags');
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getUrlAttribute(): string
    {
        return route('courses.index', ['tag' => $this->slug]);
    }
}
