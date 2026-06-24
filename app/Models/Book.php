<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'instructor_id',
        'institution_id',
        'title',
        'slug',
        'description',
        'cover_image',
        'price',
        'discount_price',
        'type',
        'stock',
        'file_path',
        'isbn',
        'author',
        'publisher',
        'pages',
        'status',
        'meta_title',
        'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'price'          => 'integer',
            'discount_price' => 'integer',
            'stock'          => 'integer',
            'pages'          => 'integer',
        ];
    }

    // ── Boot ──────────────────────────────────────────────────────────────────

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Book $book) {
            if (empty($book->slug)) {
                $book->slug = Str::slug($book->title);
            }
        });
    }

    // ── Relasi ────────────────────────────────────────────────────────────────

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(BookOrder::class);
    }

    public function flashSaleItems(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(FlashSaleItem::class, 'itemable');
    }

    /**
     * Get the currently active flash sale item for this book.
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

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', 'draft');
    }

    public function scopeDigital(Builder $query): Builder
    {
        return $query->whereIn('type', ['digital', 'both']);
    }

    public function scopePhysical(Builder $query): Builder
    {
        return $query->whereIn('type', ['physical', 'both']);
    }

    public function scopeInStock(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->whereIn('type', ['digital', 'both'])
              ->orWhere(function (Builder $q2) {
                  $q2->where('type', 'physical')->where('stock', '>', 0);
              });
        });
    }

    public function scopeByInstructor(Builder $query, int $instructorId): Builder
    {
        return $query->where('instructor_id', $instructorId);
    }

    public function scopeSearch(Builder $query, string $keyword): Builder
    {
        return $query->where(function (Builder $q) use ($keyword) {
            $q->where('title', 'like', "%{$keyword}%")
              ->orWhere('author', 'like', "%{$keyword}%")
              ->orWhere('description', 'like', "%{$keyword}%")
              ->orWhere('isbn', 'like', "%{$keyword}%");
        });
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getCoverUrlAttribute(): string
    {
        return storageUrl($this->cover_image, asset('images/placeholder-book.jpg'));
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

    public function getIsDigitalAttribute(): bool
    {
        return in_array($this->type, ['digital', 'both']);
    }

    public function getIsPhysicalAttribute(): bool
    {
        return in_array($this->type, ['physical', 'both']);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'physical' => 'Buku Fisik',
            'digital'  => 'E-Book',
            'both'     => 'Fisik & E-Book',
            default    => ucfirst($this->type),
        };
    }

    public function getIsInStockAttribute(): bool
    {
        if ($this->is_digital) return true;

        return $this->stock > 0;
    }

    public function getUrlAttribute(): string
    {
        return route('books.show', $this->slug);
    }
}
