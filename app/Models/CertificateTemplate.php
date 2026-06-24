<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CertificateTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_active',
        'background_image',
        'name_x', 'name_y', 'name_font_size', 'name_font_color', 'name_align', 'name_bold',
        'course_x', 'course_y', 'course_font_size', 'course_font_color', 'course_align', 'course_bold',
        'show_cert_number', 'cert_num_x', 'cert_num_y', 'cert_num_font_size', 'cert_num_font_color',
        'show_date', 'date_x', 'date_y', 'date_font_size', 'date_font_color',
    ];

    protected function casts(): array
    {
        return [
            'is_active'        => 'boolean',
            'name_bold'        => 'boolean',
            'course_bold'      => 'boolean',
            'show_cert_number' => 'boolean',
            'show_date'        => 'boolean',
            'name_x'           => 'float',
            'name_y'           => 'float',
            'course_x'         => 'float',
            'course_y'         => 'float',
            'cert_num_x'       => 'float',
            'cert_num_y'       => 'float',
            'date_x'           => 'float',
            'date_y'           => 'float',
        ];
    }

    // ── Accessor ──────────────────────────────────────────────────────────────

    /**
     * URL publik background image dari MinIO.
     * Dipakai untuk HTML preview (iframe) DAN DomPDF (dengan enable_remote=true).
     */
    public function getBackgroundUrlAttribute(): ?string
    {
        return $this->background_image
            ? Storage::disk('s3')->url($this->background_image)
            : null;
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ── Static Helpers ────────────────────────────────────────────────────────

    public static function getActive(): self
    {
        return static::active()->first() ?? static::makeDefault();
    }

    public static function makeDefault(): self
    {
        return new static([
            'name'                => 'Default',
            'background_image'    => null,
            'name_x'              => 50,    'name_y'              => 52,
            'name_font_size'      => 36,    'name_font_color'     => '#1E3A5F',
            'name_align'          => 'center', 'name_bold'        => true,
            'course_x'            => 50,    'course_y'            => 64,
            'course_font_size'    => 18,    'course_font_color'   => '#2563EB',
            'course_align'        => 'center', 'course_bold'      => true,
            'show_cert_number'    => true,
            'cert_num_x'          => 50,    'cert_num_y'          => 76,
            'cert_num_font_size'  => 11,    'cert_num_font_color' => '#64748B',
            'show_date'           => true,
            'date_x'              => 50,    'date_y'              => 82,
            'date_font_size'      => 12,    'date_font_color'     => '#475569',
        ]);
    }

    public function setAsActive(): void
    {
        static::query()->update(['is_active' => false]);
        $this->update(['is_active' => true]);
    }
}
