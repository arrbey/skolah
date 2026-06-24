<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'delivery_type',
        'label',
        'price',
        'discount_price',
        'schedule_start',
        'schedule_end',
        'location',
        'platform',
        'meeting_link',
        'max_participants',
        'total_enrolled',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price'            => 'integer',
            'discount_price'   => 'integer',
            'schedule_start'   => 'datetime',
            'schedule_end'     => 'datetime',
            'max_participants' => 'integer',
            'total_enrolled'   => 'integer',
            'is_active'        => 'boolean',
            'sort_order'       => 'integer',
        ];
    }

    // ── Relasi ────────────────────────────────────────────────────────────────

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('delivery_type', $type);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->active()->where(function (Builder $q) {
            // Online selalu tersedia, offline/hybrid cek jadwal belum lewat
            $q->where('delivery_type', 'online')
              ->orWhere(function (Builder $q2) {
                  $q2->whereNotNull('schedule_start')
                     ->where('schedule_start', '>', now());
              });
        });
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

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

    public function getDeliveryTypeLabelAttribute(): string
    {
        return match ($this->delivery_type) {
            'online'  => '🎥 Online',
            'offline' => '🏢 Offline',
            'hybrid'  => '🔀 Hybrid',
            default   => ucfirst($this->delivery_type),
        };
    }

    public function getDeliveryTypeBadgeAttribute(): string
    {
        return match ($this->delivery_type) {
            'online'  => 'primary',
            'offline' => 'warning',
            'hybrid'  => 'secondary',
            default   => 'gray',
        };
    }

    public function getDisplayLabelAttribute(): string
    {
        if ($this->label) {
            return $this->label;
        }

        return $this->delivery_type_label;
    }

    public function getIsFullAttribute(): bool
    {
        if ($this->max_participants === 0) return false; // unlimited
        return $this->total_enrolled >= $this->max_participants;
    }

    public function getSpotsLeftAttribute(): ?int
    {
        if ($this->max_participants === 0) return null; // unlimited
        return max(0, $this->max_participants - $this->total_enrolled);
    }

    public function getScheduleFormattedAttribute(): ?string
    {
        if (! $this->schedule_start) return null;

        $start = $this->schedule_start->translatedFormat('d M Y, H:i');

        if ($this->schedule_end) {
            // Jika tanggal sama, hanya tampilkan waktu end
            if ($this->schedule_start->isSameDay($this->schedule_end)) {
                return $start . ' – ' . $this->schedule_end->translatedFormat('H:i') . ' WIB';
            }
            return $start . ' – ' . $this->schedule_end->translatedFormat('d M Y, H:i') . ' WIB';
        }

        return $start . ' WIB';
    }
}
