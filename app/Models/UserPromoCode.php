<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPromoCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'promo_code_id',
        'source_type',
        'source_id',
        'is_used',
        'used_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'is_used'    => 'boolean',
            'used_at'    => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    // ── Relasi ────────────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function promoCode(): BelongsTo
    {
        return $this->belongsTo(PromoCode::class);
    }
}
