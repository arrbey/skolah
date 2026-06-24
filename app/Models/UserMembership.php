<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserMembership extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'started_at',
        'expires_at',
        'billing_cycle',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    // ── Relasi ────────────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(MembershipPlan::class, 'plan_id');
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active')
            ->where('expires_at', '>', now());
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->where('status', 'expired')
              ->orWhere('expires_at', '<=', now());
        });
    }

    public function scopeMonthly(Builder $query): Builder
    {
        return $query->where('billing_cycle', 'monthly');
    }

    public function scopeYearly(Builder $query): Builder
    {
        return $query->where('billing_cycle', 'yearly');
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active' && $this->expires_at->isFuture();
    }

    public function getIsExpiredAttribute(): bool
    {
        return ! $this->is_active;
    }

    public function getDaysRemainingAttribute(): int
    {
        if ($this->is_expired) return 0;

        return (int) now()->diffInDays($this->expires_at);
    }

    public function getStartedAtFormattedAttribute(): string
    {
        return tanggal_indo($this->started_at);
    }

    public function getExpiresAtFormattedAttribute(): string
    {
        return tanggal_indo($this->expires_at);
    }

    public function getBillingCycleLabelAttribute(): string
    {
        return $this->billing_cycle === 'yearly' ? 'Tahunan' : 'Bulanan';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active'    => 'Aktif',
            'expired'   => 'Kedaluwarsa',
            'cancelled' => 'Dibatalkan',
            default     => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'active'    => 'green',
            'expired'   => 'red',
            'cancelled' => 'gray',
            default     => 'gray',
        };
    }
}
