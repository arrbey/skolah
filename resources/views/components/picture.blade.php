{{--
    <x-picture> — WebP image with fallback for older browsers.

    Strategy 2: render <picture> with <source> WebP first, <img> original fallback.
    - Browser modern → pakai .webp (smaller)
    - Browser lama / gagal load → otomatis fallback ke .jpg/.png
    - Kalau src bukan jpg/png (sudah webp, atau external URL, atau asset local) →
      render <img> biasa tanpa wrapper

    Props:
    - src       (required) URL gambar original (jpg/png) dari DB atau helper
    - alt       (string)   alt text
    - class     (string)   CSS class untuk <img>
    - loading   (string)   'lazy' (default) | 'eager' untuk LCP image
    - fetchpriority (string) 'auto' (default) | 'high' untuk hero image
    - width/height (int)   dimensi optional (bagus untuk CLS)

    Usage:
    <x-picture :src="storageUrl($course->thumbnail)" alt="{{ $course->title }}" class="w-full h-48 object-cover" />
    <x-picture :src="$banner->image_url" alt="Hero" loading="eager" fetchpriority="high" />
--}}

@props([
    'src'           => null,
    'alt'           => '',
    'class'         => '',
    'loading'       => 'lazy',
    'fetchpriority' => 'auto',
    'width'         => null,
    'height'        => null,
])

@php
    // Safety: ensure src and alt are always strings
    $src   = is_string($src) ? $src : (string) ($src ?? '');
    $alt   = is_string($alt) ? $alt : (string) ($alt ?? '');
    $class = is_string($class) ? $class : (string) ($class ?? '');

    // Cek apakah src adalah .jpg/.jpeg/.png (yang punya WebP sibling di MinIO)
    $hasWebpSibling = $src && preg_match('/\.(jpe?g|png)(\?|$)/i', $src);
    $webpSrc = $hasWebpSibling ? preg_replace('/\.(jpe?g|png)(\?|$)/i', '.webp$2', $src) : null;

    // Extra attributes (like style, title, etc.) from the parent
    $extras = $attributes->except(['src', 'alt', 'class', 'loading', 'fetchpriority', 'width', 'height']);
@endphp

@if($hasWebpSibling)
    {{-- display:contents makes <picture> invisible to layout — child <img> behaves
         as if it were placed directly in the parent container. --}}
    <picture style="display:contents">
        <source srcset="{{ $webpSrc }}" type="image/webp">
        <img
            src="{{ $src }}"
            alt="{{ $alt }}"
            class="{{ $class }}"
            loading="{{ $loading }}"
            decoding="async"
            fetchpriority="{{ $fetchpriority }}"
            @if($width) width="{{ $width }}" @endif
            @if($height) height="{{ $height }}" @endif
            {{ $extras }}
        >
    </picture>
@else
    <img
        src="{{ $src }}"
        alt="{{ $alt }}"
        class="{{ $class }}"
        loading="{{ $loading }}"
        decoding="async"
        fetchpriority="{{ $fetchpriority }}"
        @if($width) width="{{ $width }}" @endif
        @if($height) height="{{ $height }}" @endif
        {{ $extras }}
    >
@endif
