<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Sertifikat{{ isset($certificate->certificate_number) ? ' - ' . $certificate->certificate_number : '' }}</title>
@php
    $tpl = $template ?? \App\Models\CertificateTemplate::makeDefault();

    // Background image: URL publik dari MinIO (S3).
    $resolvedBg = null;
    if (!empty($tpl->background_image)) {
        $resolvedBg = \Illuminate\Support\Facades\Storage::disk('s3')->url($tpl->background_image);
    }

    /**
     * Helper posisi — harus SAMA PERSIS dengan live preview (Alpine _buildStyle).
     *
     * Live preview menggunakan:
     *   - top: y%  (top-left anchor, tanpa transform)
     *   - center align: left:0; width:100%; text-align:center
     *   - left align:   left:x%; text-align:left
     *   - right align:  right:(100-x)%; text-align:right
     *
     * PDF harus pakai logika yang sama persis agar WYSIWYG.
     */
    if (!function_exists('posStyle')) {
        function posStyle($x, $y, $align, $fontSize = 14) {
            $top = $y . '%';

            if ($align === 'center') {
                return "position:absolute; top:{$top}; left:0; width:100%; text-align:center;";
            } elseif ($align === 'right') {
                $right = (100 - $x) . '%';
                return "position:absolute; top:{$top}; right:{$right}; text-align:right;";
            } else {
                return "position:absolute; top:{$top}; left:{$x}%; text-align:left;";
            }
        }
    }
@endphp
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
@page { size: A4 landscape; margin: 0; }
body {
    width: 297mm;
    height: 210mm;
    font-family: 'DejaVu Sans', Arial, sans-serif;
    overflow: hidden;
    background: #ffffff;
}
.cert-wrap {
    position: relative;
    width: 297mm;
    height: 210mm;
    overflow: hidden;
    background: #ffffff;
}
.cert-bg {
    position: absolute;
    top: 0; left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    z-index: 0;
}
.cert-overlay {
    position: absolute;
    top: 0; left: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
}
/* Fallback jika tidak ada background */
.cert-no-bg {
    position: absolute;
    top: 0; left: 0;
    width: 100%;
    height: 100%;
    background: #ffffff;
    border: 3px solid #1E3A5F;
}
.cert-no-bg-inner {
    position: absolute;
    top: 4mm; left: 4mm; right: 4mm; bottom: 4mm;
    border: 1px solid #B8860B;
}
.cert-no-bg-label {
    position: absolute;
    top: 40%; left: 0; width: 100%;
    text-align: center;
    font-size: 9pt;
    color: #94a3b8;
}
/* Teks overlay — posisi harus SAMA dengan live preview */
.text-name {
    {{ posStyle($tpl->name_x ?? 50, $tpl->name_y ?? 52, $tpl->name_align ?? 'center', $tpl->name_font_size ?? 36) }}
    font-size: {{ $tpl->name_font_size ?? 36 }}pt;
    color: {{ $tpl->name_font_color ?? '#1E3A5F' }};
    font-weight: {{ ($tpl->name_bold ?? true) ? 'bold' : 'normal' }};
    line-height: 1.2;
    z-index: 2;
}
.text-course {
    {{ posStyle($tpl->course_x ?? 50, $tpl->course_y ?? 64, $tpl->course_align ?? 'center', $tpl->course_font_size ?? 18) }}
    font-size: {{ $tpl->course_font_size ?? 18 }}pt;
    color: {{ $tpl->course_font_color ?? '#2563EB' }};
    font-weight: {{ ($tpl->course_bold ?? true) ? 'bold' : 'normal' }};
    line-height: 1.3;
    z-index: 2;
}
.text-cert-num {
    {{ posStyle($tpl->cert_num_x ?? 50, $tpl->cert_num_y ?? 76, 'center', $tpl->cert_num_font_size ?? 11) }}
    font-size: {{ $tpl->cert_num_font_size ?? 11 }}pt;
    color: {{ $tpl->cert_num_font_color ?? '#64748B' }};
    z-index: 2;
}
.text-date {
    {{ posStyle($tpl->date_x ?? 50, $tpl->date_y ?? 82, 'center', $tpl->date_font_size ?? 12) }}
    font-size: {{ $tpl->date_font_size ?? 12 }}pt;
    color: {{ $tpl->date_font_color ?? '#475569' }};
    z-index: 2;
}
</style>
</head>
<body>
<div class="cert-wrap">

    @if($resolvedBg)
        {{-- Background dari Canva/desainer --}}
        <img src="{{ $resolvedBg }}" class="cert-bg" alt="background">
    @else
        {{-- Fallback: sertifikat polos jika belum ada background --}}
        <div class="cert-no-bg">
            <div class="cert-no-bg-inner"></div>
            <div class="cert-no-bg-label">[ Belum ada gambar background — Upload di menu Desain Sertifikat ]</div>
        </div>
    @endif

    <div class="cert-overlay">

        {{-- Nama Penerima --}}
        <div class="text-name">{{ $user->name }}</div>

        {{-- Nama Kursus --}}
        <div class="text-course">{{ $course->title }}</div>

        {{-- Nomor Sertifikat --}}
        @if($tpl->show_cert_number ?? true)
            <div class="text-cert-num">{{ $certificate->certificate_number }}</div>
        @endif

        {{-- Tanggal --}}
        @if($tpl->show_date ?? true)
            <div class="text-date">
                {{ \Carbon\Carbon::parse($issuedAt)->locale('id')->translatedFormat('d F Y') }}
            </div>
        @endif

    </div>

</div>
</body>
</html>
