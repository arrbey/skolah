<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'order_id',
        'quantity',
        'price',
        'purchase_type',
        'shipping_address',
        'shipping_status',
        'tracking_number',
        'courier',
        'delivery_photo',
        'shipped_at',
        'delivered_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity'         => 'integer',
            'price'            => 'integer',
            'shipping_address' => 'encrypted:array',
            'shipped_at'       => 'datetime',
            'delivered_at'     => 'datetime',
        ];
    }

    // ── Relasi ────────────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(BookOrderHistory::class)->orderBy('created_at', 'asc');
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopePending(Builder $query): Builder
    {
        return $query->where('shipping_status', 'pending');
    }

    public function scopeShipped(Builder $query): Builder
    {
        return $query->where('shipping_status', 'shipped');
    }

    public function scopeDelivered(Builder $query): Builder
    {
        return $query->where('shipping_status', 'delivered');
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getTotalPriceAttribute(): int
    {
        return $this->price * $this->quantity;
    }

    public function getTotalPriceFormattedAttribute(): string
    {
        return rupiah($this->total_price);
    }

    public function getCourierLabelAttribute(): string
    {
        return match ($this->courier) {
            'jne' => 'JNE',
            'jnt' => 'J&T Express',
            default => $this->courier ?? '-',
        };
    }

    public function getDeliveryPhotoUrlAttribute(): ?string
    {
        return $this->delivery_photo
            ? storageUrl($this->delivery_photo)
            : null;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->shipping_status) {
            'pending'    => 'Menunggu Pengiriman',
            'processing' => 'Diproses',
            'shipped'    => 'Dalam Pengiriman',
            'delivered'  => 'Terkirim',
            'cancelled'  => 'Dibatalkan',
            default      => ucfirst($this->shipping_status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->shipping_status) {
            'pending'    => 'yellow',
            'processing' => 'blue',
            'shipped'    => 'indigo',
            'delivered'  => 'green',
            'cancelled'  => 'red',
            default      => 'gray',
        };
    }
}
