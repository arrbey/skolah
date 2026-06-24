@extends('emails.layouts.base')

@section('title', 'Enrollment Berhasil' . ' — ' . \App\Models\Setting::get('site_name', '' . \App\Models\Setting::get('site_name', 'Skolah.com') . ''))

@section('content')
<div style="text-align: center; margin-bottom: 24px;">
    <div class="badge badge-success">✅ Enrollment Berhasil</div>
    <h2 style="font-size: 22px; color: #0F172A; margin: 12px 0 4px;">
        Selamat, {{ $user->name }}!
    </h2>
    <p style="color: #64748B; font-size: 14px; margin: 0; line-height: 1.6;">
        Kamu berhasil terdaftar di kursus berikut. Saatnya mulai belajar!
    </p>
</div>

<hr class="divider">

{{-- Course Info --}}
<div style="background: #F8FAFC; border-radius: 12px; padding: 20px; margin: 16px 0;">
    @if($course->thumbnail)
    <div style="text-align: center; margin-bottom: 16px;">
        <img src="{{ storageUrl($course->thumbnail) }}" alt="{{ $course->title }}"
             style="width: 100%; max-width: 480px; border-radius: 8px;">
    </div>
    @endif

    <h3 style="font-size: 18px; color: #0F172A; margin: 0 0 12px; font-weight: 700;">
        {{ $course->title }}
    </h3>

    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 6px 0; font-size: 14px; color: #64748B;">Level</td>
            <td style="padding: 6px 0; font-size: 14px; color: #0F172A; font-weight: 600; text-align: right;">
                {{ ucfirst($course->level ?? 'Semua Level') }}
            </td>
        </tr>
        <tr>
            <td style="padding: 6px 0; font-size: 14px; color: #64748B;">Instruktur</td>
            <td style="padding: 6px 0; font-size: 14px; color: #0F172A; font-weight: 600; text-align: right;">
                {{ $course->instructor->name ?? '-' }}
            </td>
        </tr>
        <tr>
            <td style="padding: 6px 0; font-size: 14px; color: #64748B;">Total Pelajaran</td>
            <td style="padding: 6px 0; font-size: 14px; color: #0F172A; font-weight: 600; text-align: right;">
                {{ $course->lessons_count ?? $course->lessons()->count() }} materi
            </td>
        </tr>
        <tr>
            <td style="padding: 6px 0; font-size: 14px; color: #64748B;">Terdaftar Pada</td>
            <td style="padding: 6px 0; font-size: 14px; color: #0F172A; font-weight: 600; text-align: right;">
                {{ now()->translatedFormat('d F Y') }}
            </td>
        </tr>
    </table>
</div>

{{-- Tips --}}
<div style="background: #EFF6FF; border-radius: 12px; padding: 16px; margin: 16px 0;">
    <p style="font-size: 14px; color: #1D4ED8; margin: 0 0 8px; font-weight: 600;">
        💡 Tips Belajar:
    </p>
    <ul style="font-size: 13px; color: #1E293B; margin: 0; padding-left: 20px; line-height: 1.8;">
        <li>Mulai dari materi pertama secara berurutan</li>
        <li>Konsisten belajar 30 menit setiap hari</li>
        <li>Tandai pelajaran yang sudah selesai untuk melacak progressmu</li>
        <li>Selesaikan semua materi untuk mendapatkan sertifikat</li>
    </ul>
</div>

{{-- CTA --}}
<div style="text-align: center; margin: 24px 0;">
    <a href="{{ url('/learn/' . $course->slug) }}" class="cta-button cta-button-success">
        🎬 Mulai Belajar Sekarang →
    </a>
</div>
@endsection
