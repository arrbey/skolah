<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'subtotal',
        'discount_amount',
        'total',
        'status',
        'payment_method',
        'midtrans_transaction_id',
        'midtrans_snap_token',
        'midtrans_order_id',
        'promo_code',
        'paid_at',
        'payment_expires_at',
        'payment_reminder_sent',
    ];

    // ── Hidden (sensitive payment data) ───────────────────────────────────────
    protected $hidden = [
        'midtrans_snap_token',
        'midtrans_transaction_id',
    ];

    protected function casts(): array
    {
        return [
            'subtotal'                => 'integer',
            'discount_amount'         => 'integer',
            'total'                   => 'integer',
            'paid_at'                 => 'datetime',
            'payment_expires_at'      => 'datetime',
            'payment_reminder_sent'   => 'boolean',
            'midtrans_snap_token'     => 'encrypted',
            'midtrans_transaction_id' => 'encrypted',
            'midtrans_order_id'       => 'encrypted',
        ];
    }

    // ── Boot ──────────────────────────────────────────────────────────────────

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Order $order) {
            if (empty($order->order_number)) {
                $order->order_number = static::generateNumber();
            }
        });
    }

    // ── Helper ────────────────────────────────────────────────────────────────

    public static function generateNumber(): string
    {
        $prefix = 'SKL-' . date('Ymd') . '-';
        $last   = static::where('order_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('order_number');

        $seq = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    // ── Relasi ────────────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', 'paid');
    }

    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', 'failed');
    }

    public function scopeRefunded(Builder $query): Builder
    {
        return $query->where('status', 'refunded');
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeExpiredPayment(Builder $query): Builder
    {
        return $query->where('status', 'pending')
            ->whereNotNull('payment_expires_at')
            ->where('payment_expires_at', '<=', now());
    }

    public function scopePayableAndExpiringSoon(Builder $query, int $hoursLeft = 3): Builder
    {
        return $query->where('status', 'pending')
            ->where('payment_reminder_sent', false)
            ->whereNotNull('payment_expires_at')
            ->where('payment_expires_at', '>', now())
            ->where('payment_expires_at', '<=', now()->addHours($hoursLeft));
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getSubtotalFormattedAttribute(): string
    {
        return rupiah($this->subtotal);
    }

    public function getDiscountAmountFormattedAttribute(): string
    {
        return rupiah($this->discount_amount);
    }

    public function getTotalFormattedAttribute(): string
    {
        return rupiah($this->total);
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->status === 'paid';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'  => 'Menunggu Pembayaran',
            'paid'     => 'Lunas',
            'failed'   => 'Gagal',
            'refunded' => 'Dikembalikan',
            default    => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending'  => 'yellow',
            'paid'     => 'green',
            'failed'   => 'red',
            'refunded' => 'purple',
            default    => 'gray',
        };
    }

    public function getPaidAtFormattedAttribute(): ?string
    {
        return $this->paid_at ? tanggal_waktu_indo($this->paid_at) : null;
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->status === 'pending'
            && $this->payment_expires_at
            && $this->payment_expires_at->isPast();
    }

    public function getTimeRemainingAttribute(): ?string
    {
        if (! $this->payment_expires_at || $this->status !== 'pending') {
            return null;
        }

        if ($this->payment_expires_at->isPast()) {
            return 'Kedaluwarsa';
        }

        return $this->payment_expires_at->diffForHumans(now(), [
            'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE,
            'parts'  => 2,
        ]);
    }
}
