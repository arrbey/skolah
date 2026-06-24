<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class AuditLog extends Model
{
    use MassPrunable;

    /**
     * Audit log hanya punya created_at (tanpa updated_at).
     */
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'ip_address',
        'method',
        'url',
        'route_name',
        'payload',
        'status_code',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'payload'    => 'array',
        'created_at' => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => 'Guest / Deleted User',
        ]);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    /**
     * Filter berdasarkan user.
     */
    public function scopeByUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Filter berdasarkan method HTTP.
     */
    public function scopeByMethod(Builder $query, string $method): Builder
    {
        return $query->where('method', strtoupper($method));
    }

    /**
     * Filter berdasarkan route name pattern.
     */
    public function scopeByRoute(Builder $query, string $pattern): Builder
    {
        return $query->where('route_name', 'like', $pattern . '%');
    }

    /**
     * Filter berdasarkan rentang tanggal.
     */
    public function scopeBetweenDates(Builder $query, string $from, string $to): Builder
    {
        return $query->whereBetween('created_at', [$from, $to . ' 23:59:59']);
    }

    /**
     * Search URL atau route_name.
     */
    public function scopeSearch(Builder $query, string $keyword): Builder
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('url', 'like', "%{$keyword}%")
              ->orWhere('route_name', 'like', "%{$keyword}%")
              ->orWhere('ip_address', 'like', "%{$keyword}%");
        });
    }

    // ── MassPrunable ──────────────────────────────────────────────────────────

    /**
     * Hapus audit logs yang lebih dari 90 hari.
     * Jalankan: php artisan model:prune --model=App\Models\AuditLog
     */
    public function prunable(): Builder
    {
        return static::where('created_at', '<', now()->subDays(90));
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Badge color untuk HTTP method.
     */
    public function getMethodBadgeAttribute(): string
    {
        return match ($this->method) {
            'POST'   => 'bg-green-100 text-green-800',
            'PUT'    => 'bg-blue-100 text-blue-800',
            'PATCH'  => 'bg-yellow-100 text-yellow-800',
            'DELETE' => 'bg-red-100 text-red-800',
            default  => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Badge color untuk status code.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match (true) {
            $this->status_code >= 200 && $this->status_code < 300 => 'bg-green-100 text-green-800',
            $this->status_code >= 300 && $this->status_code < 400 => 'bg-blue-100 text-blue-800',
            $this->status_code >= 400 && $this->status_code < 500 => 'bg-yellow-100 text-yellow-800',
            $this->status_code >= 500 => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
