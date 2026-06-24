<?php

namespace App\Services;

use DOMAttr;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;

/**
 * HTML Sanitizer — Anti Stored-XSS untuk konten rich-text.
 *
 * Strategi:
 * - Parse HTML dengan DOMDocument (lebih aman daripada regex).
 * - Whitelist tag yang diizinkan (default: tag formatting umum + img + a).
 * - Whitelist atribut per tag.
 * - Strip semua event handler (onclick, onerror, onload, dst).
 * - Strip URL berbahaya (javascript:, data:text/html, vbscript:).
 *
 * Cocok untuk membersihkan output TinyMCE / rich-text editor sebelum disimpan.
 */
class HtmlSanitizerService
{
    /**
     * Tag HTML yang diizinkan secara default.
     */
    protected array $allowedTags = [
        'p', 'br', 'hr', 'span', 'div',
        'strong', 'b', 'em', 'i', 'u', 's', 'mark', 'small', 'sub', 'sup',
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'ul', 'ol', 'li',
        'blockquote', 'pre', 'code',
        'a', 'img',
        'table', 'thead', 'tbody', 'tr', 'th', 'td',
        'figure', 'figcaption',
    ];

    /**
     * Atribut yang diizinkan per tag.
     * '*' = berlaku untuk semua tag.
     */
    protected array $allowedAttributes = [
        '*'   => ['class', 'id', 'style', 'title'],
        'a'   => ['href', 'target', 'rel'],
        'img' => ['src', 'alt', 'width', 'height', 'loading'],
        'td'  => ['colspan', 'rowspan'],
        'th'  => ['colspan', 'rowspan', 'scope'],
    ];

    /**
     * Skema URL yang diizinkan untuk href / src.
     */
    protected array $allowedUrlSchemes = ['http', 'https', 'mailto', 'tel'];

    /**
     * Sanitize HTML string.
     */
    public function clean(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        // Pre-filter: hapus null bytes
        $html = str_replace(chr(0), '', $html);

        // Bungkus dengan wrapper agar fragment HTML bisa diparse oleh DOMDocument
        $wrapped = '<?xml encoding="UTF-8"><div id="__skolah_root__">' . $html . '</div>';

        $dom = new DOMDocument('1.0', 'UTF-8');
        // Suppress warning untuk HTML5 tags yang tidak dikenali libxml
        $previous = libxml_use_internal_errors(true);

        // LIBXML_NONET = blokir resolusi network entity (XXE protection)
        // LIBXML_NOENT off = jangan expand entity
        $dom->loadHTML(
            $wrapped,
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NONET
        );

        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        // Cari root wrapper kita
        $xpath = new DOMXPath($dom);
        $root  = $xpath->query("//div[@id='__skolah_root__']")->item(0);

        if (! $root instanceof DOMElement) {
            return '';
        }

        $this->cleanNode($root);

        // Render kembali innerHTML root
        $output = '';
        foreach ($root->childNodes as $child) {
            $output .= $dom->saveHTML($child);
        }

        return trim($output);
    }

    /**
     * Rekursif: bersihkan node + children.
     */
    protected function cleanNode(DOMNode $node): void
    {
        // Iterasi children secara safe (karena bisa dimodifikasi saat loop)
        $children = [];
        foreach ($node->childNodes as $child) {
            $children[] = $child;
        }

        foreach ($children as $child) {
            if ($child instanceof DOMElement) {
                $tag = strtolower($child->nodeName);

                // ── 1) Hapus tag yang tidak di-whitelist (termasuk script, iframe, object, embed, dll) ─
                if (! in_array($tag, $this->allowedTags, true)) {
                    $child->parentNode?->removeChild($child);
                    continue;
                }

                // ── 2) Bersihkan atribut ─────────────────────────────────────────
                $this->cleanAttributes($child, $tag);

                // ── 3) Rekursif ke anak ──────────────────────────────────────────
                $this->cleanNode($child);
            } elseif ($child->nodeType === XML_COMMENT_NODE) {
                // Hapus komentar HTML (bisa berisi conditional IE script)
                $child->parentNode?->removeChild($child);
            }
            // Text nodes dibiarkan — sudah di-encode oleh DOMDocument saat saveHTML
        }
    }

    /**
     * Bersihkan atribut sebuah element.
     */
    protected function cleanAttributes(DOMElement $el, string $tag): void
    {
        $allowed = array_unique(array_merge(
            $this->allowedAttributes['*'] ?? [],
            $this->allowedAttributes[$tag] ?? []
        ));

        // Kumpulkan dulu nama atribut (jangan modifikasi saat iterasi)
        $attrNames = [];
        foreach ($el->attributes as $attr) {
            /** @var DOMAttr $attr */
            $attrNames[] = $attr->name;
        }

        foreach ($attrNames as $name) {
            $lname = strtolower($name);

            // ── Hapus event handler (on*) ────────────────────────────────────
            if (str_starts_with($lname, 'on')) {
                $el->removeAttribute($name);
                continue;
            }

            // ── Hapus atribut yang tidak di-whitelist ───────────────────────
            if (! in_array($lname, $allowed, true)) {
                $el->removeAttribute($name);
                continue;
            }

            $value = $el->getAttribute($name);

            // ── Sanitize URL untuk href / src ───────────────────────────────
            if (in_array($lname, ['href', 'src'], true)) {
                if (! $this->isSafeUrl($value)) {
                    $el->removeAttribute($name);
                    continue;
                }
            }

            // ── Sanitize style: blokir expression(), url(javascript:...) ────
            if ($lname === 'style' && $this->styleHasUnsafe($value)) {
                $el->removeAttribute($name);
                continue;
            }
        }

        // ── Tambah rel="noopener noreferrer" untuk <a target="_blank"> ──────
        if ($tag === 'a' && strtolower($el->getAttribute('target')) === '_blank') {
            $existing = $el->getAttribute('rel');
            $rel = trim($existing . ' noopener noreferrer');
            $el->setAttribute('rel', $rel);
        }
    }

    /**
     * Cek apakah URL aman (skema diizinkan, bukan javascript:/data:html/vbscript:).
     */
    protected function isSafeUrl(string $url): bool
    {
        $url = trim($url);
        if ($url === '') {
            return false;
        }

        // Relative URL (mis. /images/foo.png atau #anchor) dianggap aman
        if (str_starts_with($url, '/') || str_starts_with($url, '#') || str_starts_with($url, '?')) {
            return true;
        }

        // Cek skema
        $parts = explode(':', $url, 2);
        if (count($parts) < 2) {
            // Tidak ada skema = relative, anggap aman
            return true;
        }

        $scheme = strtolower(trim($parts[0]));

        // Hapus karakter kontrol & whitespace yang sering dipakai bypass (e.g. "java\tscript:")
        $cleanScheme = preg_replace('/[\x00-\x1F\s]/', '', $scheme);

        if (in_array($cleanScheme, ['javascript', 'vbscript', 'data', 'file'], true)) {
            return false;
        }

        return in_array($cleanScheme, $this->allowedUrlSchemes, true);
    }

    /**
     * Cek apakah CSS inline mengandung pola berbahaya.
     */
    protected function styleHasUnsafe(string $style): bool
    {
        $lower = strtolower($style);

        if (str_contains($lower, 'expression(')) {
            return true;
        }

        if (preg_match('/url\s*\(\s*["\']?\s*(javascript|vbscript|data)\s*:/i', $lower)) {
            return true;
        }

        if (preg_match('/(javascript|vbscript)\s*:/i', $lower)) {
            return true;
        }

        return false;
    }
}
