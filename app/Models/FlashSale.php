<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlashSale extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'banner',
        'start_at',
        'end_at',
        'is_active',
    ];

    protected $casts = [
        'start_at'  => 'datetime',
        'end_at'    => 'datetime',
        'is_active' => 'boolean',
    ];

    public function items(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FlashSaleItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('start_at', '<=', now())
            ->where('end_at', '>=', now());
    }

    public function getIsRunningAttribute(): bool
    {
        return $this->is_active && $this->start_at->isPast() && $this->end_at->isFuture();
    }
}
