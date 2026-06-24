<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingProgram extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'features',
        'button_text',
        'button_link',
        'image',
        'alignment',
        'order',
        'is_active',
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
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
