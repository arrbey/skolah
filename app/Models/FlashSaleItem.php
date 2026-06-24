<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlashSaleItem extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'flash_sale_id',
        'itemable_id',
        'itemable_type',
        'flash_sale_price',
        'limit_quantity',
        'sold_quantity',
    ];

    public function flashSale(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(FlashSale::class);
    }

    public function itemable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    public function getIsAvailableAttribute(): bool
    {
        if (is_null($this->limit_quantity)) {
            return true;
        }
        return $this->sold_quantity < $this->limit_quantity;
    }

    public function getRemainingQuantityAttribute(): ?int
    {
        if (is_null($this->limit_quantity)) {
            return null;
        }
        return max(0, $this->limit_quantity - $this->sold_quantity);
    }
}
