<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'itemable_type',
        'itemable_id',
        'item_name',
        'price',
        'quantity',
        'meta',
        'course_variant_id',
        'flash_sale_item_id',
    ];

    protected function casts(): array
    {
        return [
            'price'    => 'integer',
            'quantity' => 'integer',
            'meta'     => 'array',
        ];
    }

    // ── Relasi ────────────────────────────────────────────────────────────────

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function itemable(): MorphTo
    {
        return $this->morphTo();
    }

    public function courseVariant(): BelongsTo
    {
        return $this->belongsTo(CourseVariant::class, 'course_variant_id');
    }

    public function flashSaleItem(): BelongsTo
    {
        return $this->belongsTo(FlashSaleItem::class, 'flash_sale_item_id');
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getPriceFormattedAttribute(): string
    {
        return rupiah($this->price);
    }

    public function getSubtotalAttribute(): int
    {
        return $this->price * $this->quantity;
    }

    public function getSubtotalFormattedAttribute(): string
    {
        return rupiah($this->subtotal);
    }

    public function getItemTypeAttribute(): string
    {
        return match ($this->itemable_type) {
            Course::class         => 'course',
            Bundle::class         => 'bundle',
            Bootcamp::class       => 'bootcamp',
            Book::class           => 'book',
            MembershipPlan::class => 'membership',
            default               => 'item',
        };
    }

    public function getItemTypeLabelAttribute(): string
    {
        return match ($this->item_type) {
            'course'     => 'Kursus',
            'bundle'     => 'Bundle Kursus',
            'bootcamp'   => 'Bootcamp',
            'book'       => 'Buku',
            'membership' => 'Membership',
            default      => 'Item',
        };
    }
}
