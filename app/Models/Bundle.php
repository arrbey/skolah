<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Bundle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'thumbnail',
        'price',
        'discount_price',
        'status',
        'instructor_id',
    ];

    protected $casts = [
        'price' => 'integer',
        'discount_price' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($bundle) {
            if (empty($bundle->slug)) {
                $bundle->slug = Str::slug($bundle->title);
            }
        });
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id')->withDefault([
            'name' => 'Administrator',
            'avatar' => null,
        ]);
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function getThumbnailUrlAttribute(): string
    {
        if ($this->thumbnail && \Storage::disk('public')->exists($this->thumbnail)) {
            return asset('storage/' . $this->thumbnail);
        }
        return asset('images/bundle-placeholder.jpg');
    }

    public function getFinalPriceAttribute(): int
    {
        return $this->discount_price ?? $this->price;
    }

    public function getFinalPriceFormattedAttribute(): string
    {
        return rupiah($this->final_price);
    }

    public function getOriginalPriceFormattedAttribute(): string
    {
        return rupiah($this->price);
    }

    public function getHasDiscountAttribute(): bool
    {
        return !empty($this->discount_price) && $this->discount_price < $this->price;
    }
}
