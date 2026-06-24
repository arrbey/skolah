<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookOrderHistory extends Model
{
    protected $fillable = [
        'book_order_id',
        'actor_id',
        'actor_name',
        'status',
        'tracking_number',
        'courier',
        'delivery_photo',
        'note',
    ];

    // ── Relasi ─────────────────────────────────────────────────────────────

    public function bookOrder(): BelongsTo
    {
        return $this->belongsTo(BookOrder::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    // ── Accessors ──────────────────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'    => 'Menunggu Pengiriman',
            'processing' => 'Diproses',
            'shipped'    => 'Dalam Pengiriman',
            'delivered'  => 'Terkirim',
            'cancelled'  => 'Dibatalkan',
            default      => ucfirst($this->status),
        };
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
}
