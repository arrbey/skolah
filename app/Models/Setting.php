<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    // ── Static helpers ────────────────────────────────────────────────────────

    /**
     * Ambil nilai setting berdasarkan key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("setting.{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();

            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set atau buat setting berdasarkan key.
     */
    public static function set(string $key, mixed $value, string $group = 'general'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );

        Cache::forget("setting.{$key}");
        Cache::forget("settings.group.{$group}");
    }

    /**
     * Ambil semua setting berdasarkan group sebagai Collection of Setting models.
     */
    public static function getGroup(string $group): \Illuminate\Support\Collection
    {
        return static::where('group', $group)->get();
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeByGroup(Builder $query, string $group): Builder
    {
        return $query->where('group', $group);
    }
}
