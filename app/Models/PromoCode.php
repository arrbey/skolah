<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'applicable_type',
        'min_purchase',
        'max_uses',
        'used_count',
        'expires_at',
        'is_active',
        'image',
    ];

    protected function casts(): array
    {
        return [
            'discount_value' => 'integer',
            'min_purchase'   => 'integer',
            'max_uses'       => 'integer',
            'used_count'     => 'integer',
            'expires_at'     => 'datetime',
            'is_active'      => 'boolean',
        ];
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Promo yang masih valid: aktif, belum kadaluarsa, belum habis kuota.
     */
    public function scopeValid(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(function (Builder $q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->where(function (Builder $q) {
                $q->whereNull('max_uses')
                  ->orWhereColumn('used_count', '<', 'max_uses');
            });
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getIsValidAttribute(): bool
    {
        if (! $this->is_active) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        if ($this->max_uses && $this->used_count >= $this->max_uses) return false;

        return true;
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function getDiscountLabelAttribute(): string
    {
        if ($this->discount_type === 'percent') {
            return $this->discount_value . '%';
        }

        return rupiah($this->discount_value);
    }

    /**
     * Label human-readable untuk applicable_type.
     */
    public function getApplicableLabelAttribute(): string
    {
        return match ($this->applicable_type) {
            'all'                  => 'Semua Produk',
            'course'               => 'Kursus',
            'bootcamp'             => 'Bootcamp',
            'book'                 => 'Buku',
            'membership'           => 'Membership (Semua)',
            'membership_monthly'   => 'Membership Bulanan',
            'membership_yearly'    => 'Membership Tahunan',
            default                => 'Semua Produk',
        };
    }

    /**
     * Cek apakah promo berlaku untuk tipe cart item tertentu.
     *
     * @param string $cartableType  FQCN model (Course::class, dll)
     * @param string|null $billingCycle  'monthly' atau 'yearly' (khusus membership)
     */
    public function isApplicableTo(string $cartableType, ?string $billingCycle = null): bool
    {
        // Promo berlaku untuk semua
        if ($this->applicable_type === 'all') {
            return true;
        }

        // Map cartable_type (FQCN) ke applicable_type key
        $typeMap = [
            \App\Models\Course::class         => 'course',
            \App\Models\Bootcamp::class       => 'bootcamp',
            \App\Models\Book::class           => 'book',
            \App\Models\MembershipPlan::class => 'membership',
        ];

        $itemType = $typeMap[$cartableType] ?? null;

        if (! $itemType) {
            return false;
        }

        // Untuk membership, cek juga billing cycle jika promo khusus monthly/yearly
        if ($itemType === 'membership') {
            return match ($this->applicable_type) {
                'membership'         => true,
                'membership_monthly' => $billingCycle === 'monthly',
                'membership_yearly'  => $billingCycle === 'yearly',
                default              => false,
            };
        }

        return $this->applicable_type === $itemType;
    }

    public function getExpiresAtFormattedAttribute(): ?string
    {
        return $this->expires_at ? tanggal_indo($this->expires_at) : null;
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? storageUrl($this->image) : null;
    }

    // ── Methods ───────────────────────────────────────────────────────────────

    /**
     * Daftar tipe yang tersedia (untuk dropdown di admin).
     */
    public static function applicableTypes(): array
    {
        return [
            'all'                => 'Semua Produk',
            'course'             => 'Kursus',
            'bootcamp'           => 'Bootcamp',
            'book'               => 'Buku',
            'membership'         => 'Membership (Semua)',
            'membership_monthly' => 'Membership Bulanan',
            'membership_yearly'  => 'Membership Tahunan',
        ];
    }

    /**
     * Hitung besaran diskon berdasarkan subtotal (dalam rupiah).
     */
    public function calculateDiscount(int $subtotal): int
    {
        if (! $this->is_valid) return 0;
        if ($this->min_purchase && $subtotal < $this->min_purchase) return 0;

        if ($this->discount_type === 'percent') {
            return (int) round($subtotal * ($this->discount_value / 100));
        }

        return min($this->discount_value, $subtotal);
    }

    /**
     * Tandai promo digunakan (+1 used_count).
     */
    public function markAsUsed(): void
    {
        $this->increment('used_count');
    }
}
