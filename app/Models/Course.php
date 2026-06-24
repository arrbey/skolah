<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Str;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'instructor_id',
        'institution_id',
        'category_id',
        'title',
        'slug',
        'description',
        'thumbnail',
        'trailer_url',
        'price',
        'discount_price',
        'level',
        'language',
        'status',
        'is_featured',
        'total_students',
        'rating',
        'rating_count',
        'meta_title',
        'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'price'          => 'integer',
            'discount_price' => 'integer',
            'is_featured'    => 'boolean',
            'total_students' => 'integer',
            'rating'         => 'float',
            'rating_count'   => 'integer',
        ];
    }

    // ── Boot ──────────────────────────────────────────────────────────────────

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Course $course) {
            if (empty($course->slug)) {
                $course->slug = Str::slug($course->title);
            }
        });
    }

    // ── Relasi ────────────────────────────────────────────────────────────────

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id')->withDefault([
            'name' => 'Administrator',
            'avatar' => null,
        ]);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(CourseSection::class)->orderBy('order');
    }

    public function lessons(): HasManyThrough
    {
        return $this->hasManyThrough(
            CourseLesson::class,
            CourseSection::class,
            'course_id',   // FK di course_sections
            'section_id',  // FK di course_lessons
        );
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    public function pretest(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Quiz::class)->where('type', 'pretest');
    }

    public function posttest(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Quiz::class)->where('type', 'posttest');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(CourseReview::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'course_tags');
    }

    public function membershipPlans(): BelongsToMany
    {
        return $this->belongsToMany(MembershipPlan::class, 'course_membership_plan')
            ->withTimestamps();
    }

    public function variants(): HasMany
    {
        return $this->hasMany(CourseVariant::class)->ordered();
    }

    public function bundles(): BelongsToMany
    {
        return $this->belongsToMany(Bundle::class);
    }

    public function flashSaleItems(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(FlashSaleItem::class, 'itemable');
    }

    /**
     * Get the currently active flash sale item for this course.
     */
    public function getActiveFlashSaleAttribute(): ?FlashSaleItem
    {
        return $this->flashSaleItems()
            ->whereHas('flashSale', function ($query) {
                $query->active();
            })
            ->first();
    }

    /**
     * Get the current effective price (Flash Sale > Discount > Regular)
     */
    public function getCurrentPriceAttribute(): int
    {
        $flash = $this->active_flash_sale;
        if ($flash && $flash->is_available) {
            return $flash->flash_sale_price;
        }

        return $this->discount_price ?? $this->price;
    }

    public function activeVariants(): HasMany
    {
        return $this->hasMany(CourseVariant::class)->active()->ordered();
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', 'draft');
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeFree(Builder $query): Builder
    {
        return $query->where('price', 0);
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('price', '>', 0);
    }

    public function scopeByLevel(Builder $query, string $level): Builder
    {
        return $query->where('level', $level);
    }

    public function scopeByCategory(Builder $query, int|string $category): Builder
    {
        if (is_string($category)) {
            // Single subquery: find category IDs where slug matches OR parent's slug matches
            return $query->whereIn('category_id', function ($sub) use ($category) {
                $sub->select('c.id')
                    ->from('categories as c')
                    ->leftJoin('categories as p', 'c.parent_id', '=', 'p.id')
                    ->where('c.slug', $category)
                    ->orWhere('p.slug', $category);
            });
        }

        return $query->where('category_id', $category);
    }

    public function scopeByInstructor(Builder $query, int $instructorId): Builder
    {
        return $query->where('instructor_id', $instructorId);
    }

    public function scopePopular(Builder $query): Builder
    {
        return $query->orderByDesc('total_students');
    }

    public function scopeTopRated(Builder $query): Builder
    {
        return $query->orderByDesc('rating');
    }

    public function scopeSearch(Builder $query, string $keyword): Builder
    {
        return $query->where(function (Builder $q) use ($keyword) {
            $q->where('title', 'like', "%{$keyword}%")
              ->orWhere('description', 'like', "%{$keyword}%");
        });
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getThumbnailUrlAttribute(): string
    {
        return storageUrl($this->thumbnail, asset('images/placeholder-course.jpg'));
    }

    public function getEffectivePriceAttribute(): int
    {
        return $this->discount_price ?? $this->price;
    }

    public function getPriceFormattedAttribute(): string
    {
        return $this->price === 0 ? 'Gratis' : rupiah($this->price);
    }

    public function getEffectivePriceFormattedAttribute(): string
    {
        return $this->effective_price === 0 ? 'Gratis' : rupiah($this->effective_price);
    }

    public function getHasDiscountAttribute(): bool
    {
        return ! is_null($this->discount_price) && $this->discount_price < $this->price;
    }

    public function getDiscountPercentAttribute(): int
    {
        if (! $this->has_discount || $this->price === 0) return 0;

        return (int) round((($this->price - $this->discount_price) / $this->price) * 100);
    }

    public function getIsPublishedAttribute(): bool
    {
        return $this->status === 'published';
    }

    public function getLevelLabelAttribute(): string
    {
        return match ($this->level) {
            'beginner'     => 'Pemula',
            'intermediate' => 'Menengah',
            'advanced'     => 'Mahir',
            default        => ucfirst($this->level),
        };
    }

    public function getUrlAttribute(): string
    {
        return route('courses.show', $this->slug);
    }

    // ── Variant Helpers ───────────────────────────────────────────────────

    /**
     * Apakah course ini memiliki variant aktif?
     */
    public function getHasVariantsAttribute(): bool
    {
        // Gunakan relasi jika sudah di-load, hindari query tambahan
        if ($this->relationLoaded('activeVariants')) {
            return $this->activeVariants->isNotEmpty();
        }
        if ($this->relationLoaded('variants')) {
            return $this->variants->where('is_active', true)->isNotEmpty();
        }
        return $this->activeVariants()->exists();
    }

    /**
     * Harga terendah dari variant aktif (untuk ditampilkan di card listing).
     * Fallback ke course price jika tidak ada variant.
     */
    public function getMinVariantPriceAttribute(): int
    {
        if ($this->relationLoaded('activeVariants') && $this->activeVariants->isNotEmpty()) {
            return $this->activeVariants->min(fn ($v) => $v->effective_price);
        }
        if ($this->relationLoaded('variants')) {
            $active = $this->variants->where('is_active', true);
            if ($active->isNotEmpty()) {
                return $active->min(fn ($v) => $v->effective_price);
            }
        }
        return $this->effective_price;
    }

    /**
     * Harga terendah formatted (misal: "Mulai dari Rp 299.000").
     */
    public function getMinVariantPriceFormattedAttribute(): string
    {
        $min = $this->min_variant_price;
        return $min === 0 ? 'Gratis' : rupiah($min);
    }

    // ── Trailer Helpers ───────────────────────────────────────────────────

    public function getTrailerYoutubeId(): ?string
    {
        if (! $this->trailer_url) return null;
        preg_match('/(?:v=|youtu\.be\/|embed\/)([a-zA-Z0-9_\-]{11})/', $this->trailer_url, $m);
        return $m[1] ?? null;
    }

    public function getTrailerThumbnailAttribute(): ?string
    {
        $id = $this->getTrailerYoutubeId();
        return $id ? "https://img.youtube.com/vi/{$id}/maxresdefault.jpg" : null;
    }
}
