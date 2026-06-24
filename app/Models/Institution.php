<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Institution extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'description',
        'is_active',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($institution) {
            if (empty($institution->slug)) {
                $institution->slug = Str::slug($institution->name);
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function bootcamps(): HasMany
    {
        return $this->hasMany(Bootcamp::class);
    }

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    public function getLogoUrlAttribute(): string
    {
        return storageUrl($this->logo, asset('images/placeholder-institution.jpg'));
    }
}
