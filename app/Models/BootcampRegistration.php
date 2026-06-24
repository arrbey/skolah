<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BootcampRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bootcamp_id',
        'ticket_code',
        'payment_status',
        'registered_at',
        'checked_in',
        'checked_in_at',
        'reminder_sent_1day',
        'reminder_sent_1hour',
    ];

    protected function casts(): array
    {
        return [
            'registered_at'       => 'datetime',
            'checked_in'          => 'boolean',
            'checked_in_at'       => 'datetime',
            'reminder_sent_1day'  => 'boolean',
            'reminder_sent_1hour' => 'boolean',
        ];
    }

    // ── Boot ──────────────────────────────────────────────────────────────────

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (BootcampRegistration $reg) {
            if (empty($reg->ticket_code)) {
                $reg->ticket_code = strtoupper('TKT-' . substr(md5(uniqid()), 0, 8));
            }
            if (empty($reg->registered_at)) {
                $reg->registered_at = now();
            }
        });
    }

    // ── Relasi ────────────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bootcamp(): BelongsTo
    {
        return $this->belongsTo(Bootcamp::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getIsPaidAttribute(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function getIsOfflineAttribute(): bool
    {
        return $this->bootcamp?->type === 'offline';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {
            'paid'    => 'Lunas',
            'pending' => 'Menunggu Pembayaran',
            'failed'  => 'Gagal',
            default   => ucfirst($this->payment_status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->payment_status) {
            'paid'    => 'green',
            'pending' => 'yellow',
            'failed'  => 'red',
            default   => 'gray',
        };
    }

    /**
     * URL halaman verifikasi QR — diakses saat QR code discan.
     * Hanya relevan untuk bootcamp offline.
     */
    public function getQrVerifyUrlAttribute(): string
    {
        return route('tickets.verify', $this->ticket_code);
    }

    /**
     * URL QR code image (SVG base64 data URI) menggunakan SimpleSoftwareIO.
     * Tidak lagi bergantung pada external API.
     */
    public function getQrImageUrlAttribute(): string
    {
        $svg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
            ->size(300)
            ->errorCorrection('H')
            ->margin(1)
            ->generate($this->qr_verify_url);

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    /**
     * Generate QR code sebagai SVG string.
     * Digunakan untuk download dan embed di PDF.
     */
    public function generateQrSvg(int $size = 500): string
    {
        return \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
            ->size($size)
            ->errorCorrection('H')
            ->margin(1)
            ->generate($this->qr_verify_url);
    }

    /**
     * Apakah tiket dalam kondisi valid (paid & bootcamp belum selesai).
     */
    public function getIsValidAttribute(): bool
    {
        return $this->payment_status === 'paid'
            && $this->bootcamp?->status !== 'cancelled';
    }
}
