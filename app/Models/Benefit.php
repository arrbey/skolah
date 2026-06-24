<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Benefit extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'icon',
        'image',
        'order',
        'is_active',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('created_at');
    }
}
