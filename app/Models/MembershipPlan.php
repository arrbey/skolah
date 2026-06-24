<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class MembershipPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price_monthly',
        'price_yearly',
        'features',
        'is_popular',
        'is_active',
        'promo_code_id',
    ];

    protected function casts(): array
    {
        return [
            'price_monthly' => 'integer',
            'price_yearly'  => 'integer',
            'features'      => 'array',
            'is_popular'    => 'boolean',
            'is_active'     => 'boolean',
        ];
    }

    // ── Boot ──────────────────────────────────────────────────────────────────

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (MembershipPlan $plan) {
            if (empty($plan->slug)) {
                $plan->slug = Str::slug($plan->name);
            }
        });
    }

    // ── Relasi ────────────────────────────────────────────────────────────────

    public function userMemberships(): HasMany
    {
        return $this->hasMany(UserMembership::class, 'plan_id');
    }

    public function activeMembers(): HasMany
    {
        return $this->hasMany(UserMembership::class, 'plan_id')
            ->where('status', 'active');
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_membership_plan')
            ->withTimestamps();
    }

    public function promoCode(): BelongsTo
    {
        return $this->belongsTo(PromoCode::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopePopular(Builder $query): Builder
    {
        return $query->where('is_popular', true);
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getPriceMonthlyFormattedAttribute(): string
    {
        return rupiah($this->price_monthly);
    }

    public function getPriceYearlyFormattedAttribute(): string
    {
        return rupiah($this->price_yearly);
    }

    /** Selisih penghematan jika bayar tahunan vs bulanan × 12 */
    public function getYearlySavingAttribute(): int
    {
        return ($this->price_monthly * 12) - $this->price_yearly;
    }

    public function getYearlySavingFormattedAttribute(): string
    {
        return rupiah($this->yearly_saving);
    }

    /** Persentase penghematan vs harga bulanan × 12 */
    public function getYearlySavingPercentAttribute(): int
    {
        $monthly12 = $this->price_monthly * 12;
        if ($monthly12 === 0) return 0;

        return (int) round(($this->yearly_saving / $monthly12) * 100);
    }
}
