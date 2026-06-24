<?php

namespace App\Http\Middleware;

use App\Services\HtmlSanitizerService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * FASE SECURITY 3 — SQL Injection & XSS Prevention
 *
 * Sanitize semua request input SEBELUM masuk ke controller.
 *
 * Strategi:
 * - Field biasa       → strip_tags + trim + null-byte removal
 * - Field rich-text   → HtmlSanitizerService (whitelist tag, strip script/event handler/javascript:)
 * - Password fields   → SKIP sepenuhnya (supaya karakter khusus tidak rusak)
 * - File upload       → SKIP (bukan string)
 *
 * CATATAN: TIDAK pakai htmlspecialchars() di input karena Blade {{ }}
 * sudah melakukan HTML entity encoding pada output — double encoding
 * menyebabkan &amp;amp; dan tampilan rusak.
 */
class SanitizeInput
{
    /**
     * Field yang BOLEH mengandung HTML (konten dari rich-text editor).
     * Hanya null bytes & trim yang diterapkan.
     */
    protected array $allowHtml = [
        'description',
        'content',
        'body',
        'about',
        'bio',
        'review',           // review bisa multi-line
        'features_text',    // membership features (admin)
        'hero_title_main',
        'hero_description',
        'landing_benefit_title',
        'landing_benefit_subtitle',
        'landing_program_title',
        'landing_gallery_title',
        'landing_gallery_subtitle',
    ];

    /**
     * Field yang TIDAK BOLEH disanitize sama sekali
     * (password, token, dsb. — karakter khusus harus utuh).
     */
    protected array $skipFields = [
        'password',
        'password_confirmation',
        'current_password',
        'token',
        '_token',
        '_method',
    ];

    protected HtmlSanitizerService $htmlSanitizer;

    public function __construct(HtmlSanitizerService $htmlSanitizer)
    {
        $this->htmlSanitizer = $htmlSanitizer;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Hanya sanitize request POST/PUT/PATCH (yang mengirim data)
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH'])) {
            $input = $request->all();
            $this->sanitizeArray($input);
            $request->merge($input);
        }

        return $next($request);
    }

    /**
     * Rekursif sanitize array input.
     */
    protected function sanitizeArray(array &$input, string $parentKey = ''): void
    {
        foreach ($input as $key => &$value) {
            $fieldName = is_int($key) ? $parentKey : $key;

            if (is_array($value)) {
                $this->sanitizeArray($value, $fieldName);
                continue;
            }

            if (!is_string($value)) {
                continue;
            }

            // Skip password & token fields sepenuhnya
            if (in_array($fieldName, $this->skipFields, true)) {
                continue;
            }

            // ── 1) Hapus null bytes (cegah null byte injection) ────────
            $value = str_replace(chr(0), '', $value);

            // ── 2) Trim whitespace ─────────────────────────────────────
            $value = trim($value);

            // ── 3) Sanitize berdasarkan tipe field ─────────────────────
            if (in_array($fieldName, $this->allowHtml, true)) {
                // Rich-text → HTML purifier (whitelist tag + strip event handler/javascript:)
                $value = $this->htmlSanitizer->clean($value);
            } else {
                // Field biasa → strip semua tag
                $value = strip_tags($value);
            }

            // ── 4) Hapus karakter kontrol berbahaya (kecuali newline/tab) ──
            $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $value);
        }
    }
}
