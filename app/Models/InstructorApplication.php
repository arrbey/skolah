<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstructorApplication extends Model
{
    protected $fillable = [
        'user_id',
        'motivation',
        'expertise',
        'portfolio_url',
        'phone',
        'status',
        'admin_notes',
        'reviewed_at',
        'reviewed_by',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }

    // ─── Relations ───────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // ─── Scopes ──────────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // ─── Accessors ───────────────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'  => 'Menunggu Review',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default    => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending'  => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            default    => 'gray',
        };
    }

    public function getIsPendingAttribute(): bool
    {
        return $this->status === 'pending';
    }
}
