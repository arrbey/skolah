<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Bootcamp extends Model
{
    use HasFactory;

    protected $fillable = [
        'instructor_id',
        'institution_id',
        'title',
        'slug',
        'description',
        'thumbnail',
        'price',
        'discount_price',
        'type',
        'platform',
        'meeting_link',
        'location',
        'start_date',
        'end_date',
        'max_participants',
        'total_registered',
        'status',
        'meta_title',
        'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'price'            => 'integer',
            'discount_price'   => 'integer',
            'start_date'       => 'datetime',
            'end_date'         => 'datetime',
            'max_participants' => 'integer',
            'total_registered' => 'integer',
        ];
    }

    // ── Boot ──────────────────────────────────────────────────────────────────

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Bootcamp $bootcamp) {
            if (empty($bootcamp->slug)) {
                $bootcamp->slug = Str::slug($bootcamp->title);
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
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(BootcampRegistration::class);
    }

    public function paidRegistrations(): HasMany
    {
        return $this->hasMany(BootcampRegistration::class)
            ->where('payment_status', 'paid');
    }

    public function flashSaleItems(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(FlashSaleItem::class, 'itemable');
    }

    /**
     * Get the currently active flash sale item for this bootcamp.
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

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('status', 'upcoming');
    }

    public function scopeOngoing(Builder $query): Builder
    {
        return $query->where('status', 'ongoing');
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    public function scopeOnline(Builder $query): Builder
    {
        return $query->where('type', 'online');
    }

    public function scopeOffline(Builder $query): Builder
    {
        return $query->where('type', 'offline');
    }

    public function scopeFree(Builder $query): Builder
    {
        return $query->where('price', 0);
    }

    public function scopeByInstructor(Builder $query, int $instructorId): Builder
    {
        return $query->where('instructor_id', $instructorId);
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
        return storageUrl($this->thumbnail, asset('images/placeholder-bootcamp.jpg'));
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

    public function getStartDateFormattedAttribute(): string
    {
        return tanggal_waktu_indo($this->start_date);
    }

    public function getEndDateFormattedAttribute(): string
    {
        return tanggal_waktu_indo($this->end_date);
    }

    public function getIsFullAttribute(): bool
    {
        if ($this->max_participants === 0) return false;

        return $this->total_registered >= $this->max_participants;
    }

    public function getRemainingSeatsAttribute(): ?int
    {
        if ($this->max_participants === 0) return null;

        return max(0, $this->max_participants - $this->total_registered);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'upcoming'  => 'Segera Hadir',
            'ongoing'   => 'Sedang Berlangsung',
            'completed' => 'Selesai',
            default     => ucfirst($this->status),
        };
    }

    public function getPlatformLabelAttribute(): string
    {
        return match ($this->platform) {
            'Zoom'        => 'Zoom Meeting',
            'Google Meet' => 'Google Meet',
            'offline'     => 'Tatap Muka',
            default       => $this->platform ?? '-',
        };
    }

    public function getUrlAttribute(): string
    {
        return route('bootcamps.show', $this->slug);
    }
}
