<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'cartable_type',
        'cartable_id',
        'quantity',
        'price',
        'course_variant_id',
        'flash_sale_item_id',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'price'    => 'integer',
        ];
    }

    // ── Relasi ────────────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cartable(): MorphTo
    {
        return $this->morphTo();
    }

    public function courseVariant(): BelongsTo
    {
        return $this->belongsTo(CourseVariant::class, 'course_variant_id');
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getItemTypeAttribute(): string
    {
        return match ($this->cartable_type) {
            Course::class         => 'course',
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
            'bootcamp'   => 'Bootcamp',
            'book'       => 'Buku',
            'membership' => 'Membership',
            default      => 'Item',
        };
    }

    public function getSubtotalAttribute(): int
    {
        return $this->price * $this->quantity;
    }

    public function getSubtotalFormattedAttribute(): string
    {
        return rupiah($this->subtotal);
    }
}
