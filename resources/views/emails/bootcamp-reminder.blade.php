@extends('emails.layouts.base')

@section('title', 'Pengingat Bootcamp' . ' — ' . \App\Models\Setting::get('site_name', '' . \App\Models\Setting::get('site_name', 'Skolah.com') . ''))

@section('content')
<div style="text-align: center; margin-bottom: 24px;">
    @if($reminderType === '1hour')
        <div style="font-size: 48px; margin-bottom: 8px;">🔴</div>
        <div class="badge badge-danger">⏰ Dimulai 1 Jam Lagi!</div>
    @else
        <div style="font-size: 48px; margin-bottom: 8px;">📅</div>
        <div class="badge badge-info">📆 Besok Dimulai</div>
    @endif

    <h2 style="font-size: 22px; color: #0F172A; margin: 12px 0 4px;">
        Halo, {{ $user->name }}!
    </h2>
    <p style="color: #64748B; font-size: 14px; margin: 0; line-height: 1.6;">
        @if($reminderType === '1hour')
            Bootcamp yang kamu ikuti akan dimulai <strong style="color: #DC2626;">1 jam lagi</strong>. Siapkan dirimu!
        @else
            Pengingat bahwa bootcamp yang kamu ikuti akan dimulai <strong style="color: #2563EB;">besok</strong>.
        @endif
    </p>
</div>

<hr class="divider">

{{-- Bootcamp Info --}}
<div style="background: #F8FAFC; border-radius: 12px; padding: 20px; margin: 16px 0;">
    @if($bootcamp->thumbnail)
    <div style="text-align: center; margin-bottom: 16px;">
        <img src="{{ storageUrl($bootcamp->thumbnail) }}" alt="{{ $bootcamp->title }}"
             style="width: 100%; max-width: 480px; border-radius: 8px;">
    </div>
    @endif

    <h3 style="font-size: 18px; color: #0F172A; margin: 0 0 16px; font-weight: 700;">
        {{ $bootcamp->title }}
    </h3>

    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 8px 0; font-size: 14px; color: #64748B;">📅 Tanggal</td>
            <td style="padding: 8px 0; font-size: 14px; color: #0F172A; font-weight: 600; text-align: right;">
                {{ $bootcamp->start_date->translatedFormat('l, d F Y') }}
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-size: 14px; color: #64748B;">🕐 Waktu</td>
            <td style="padding: 8px 0; font-size: 14px; color: #0F172A; font-weight: 600; text-align: right;">
                {{ $bootcamp->start_date->format('H:i') }} WIB
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-size: 14px; color: #64748B;">📍 Tipe</td>
            <td style="padding: 8px 0; font-size: 14px; color: #0F172A; font-weight: 600; text-align: right;">
                {{ ucfirst($bootcamp->type) }}
                @if($bootcamp->platform)
                    ({{ $bootcamp->platform }})
                @endif
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-size: 14px; color: #64748B;">👨‍🏫 Instruktur</td>
            <td style="padding: 8px 0; font-size: 14px; color: #0F172A; font-weight: 600; text-align: right;">
                {{ $bootcamp->instructor->name ?? '-' }}
            </td>
        </tr>
    </table>
</div>

{{-- Meeting Link (hanya untuk online dan reminder 1 jam) --}}
@if($bootcamp->type === 'online' && $bootcamp->meeting_link)
<div style="background: #ECFDF5; border: 1px solid #A7F3D0; border-radius: 12px; padding: 16px; margin: 16px 0; text-align: center;">
    <p style="font-size: 14px; color: #065F46; margin: 0 0 12px; font-weight: 600;">
        🔗 Link Meeting {{ $bootcamp->platform ?? 'Online' }}
    </p>
    <a href="{{ $bootcamp->meeting_link }}"
       style="display: inline-block; background: #059669; color: #FFFFFF; text-decoration: none; padding: 12px 24px; border-radius: 10px; font-size: 14px; font-weight: 600;">
        Gabung {{ $bootcamp->platform ?? 'Meeting' }} →
    </a>
    <p style="font-size: 12px; color: #6B7280; margin: 8px 0 0; word-break: break-all;">
        {{ $bootcamp->meeting_link }}
    </p>
</div>
@elseif($bootcamp->type === 'offline' && $bootcamp->location)
<div style="background: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 12px; padding: 16px; margin: 16px 0;">
    <p style="font-size: 14px; color: #1D4ED8; margin: 0 0 4px; font-weight: 600;">
        📍 Lokasi:
    </p>
    <p style="font-size: 14px; color: #1E293B; margin: 0; line-height: 1.6;">
        {{ $bootcamp->location }}
    </p>
</div>
@endif

{{-- Tips --}}
<div style="background: #FFFBEB; border-radius: 12px; padding: 16px; margin: 16px 0;">
    <p style="font-size: 14px; color: #92400E; margin: 0 0 8px; font-weight: 600;">
        💡 Persiapan:
    </p>
    <ul style="font-size: 13px; color: #1E293B; margin: 0; padding-left: 20px; line-height: 1.8;">
        @if($bootcamp->type === 'online')
            <li>Pastikan koneksi internet stabil</li>
            <li>Siapkan headset/earphone</li>
            <li>Buka link meeting 5 menit sebelum mulai</li>
        @else
            <li>Datang 15 menit lebih awal</li>
            <li>Bawa alat tulis dan laptop jika diperlukan</li>
        @endif
        <li>Siapkan pertanyaan yang ingin ditanyakan</li>
    </ul>
</div>

{{-- CTA --}}
<div style="text-align: center; margin: 24px 0;">
    <a href="{{ url('/bootcamps/' . $bootcamp->slug) }}" class="cta-button">
        📋 Lihat Detail Bootcamp →
    </a>
</div>
@endsection
