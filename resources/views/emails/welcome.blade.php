@extends('emails.layouts.base')

@section('title', 'Selamat Datang di ' . \App\Models\Setting::get('site_name', 'Skolah.com') . '')

@section('content')
<div style="text-align: center; margin-bottom: 24px;">
    <div style="font-size: 48px; margin-bottom: 8px;">🎓</div>
    <h2 style="font-size: 22px; color: #0F172A; margin: 12px 0 4px;">
        Selamat Datang, {{ $user->name }}!
    </h2>
    <p style="color: #64748B; font-size: 14px; margin: 0; line-height: 1.6;">
        Akun {{ \App\Models\Setting::get('site_name', 'Skolah.com') }} kamu sudah berhasil dibuat. Saatnya mulai perjalanan belajarmu!
    </p>
</div>

<hr class="divider">

{{-- Fitur Unggulan --}}
<h3 style="font-size: 16px; font-weight: 600; color: #0F172A; margin-bottom: 16px;">
    Yang bisa kamu lakukan di {{ \App\Models\Setting::get('site_name', 'Skolah.com') }}:
</h3>

<table style="width: 100%; border-collapse: collapse;">
    <tr>
        <td style="padding: 12px 0; vertical-align: top; width: 40px;">
            <span style="font-size: 24px;">📚</span>
        </td>
        <td style="padding: 12px 0; vertical-align: top;">
            <strong style="color: #0F172A; font-size: 14px;">Kursus Online</strong>
            <p style="color: #64748B; font-size: 13px; margin: 4px 0 0;">
                Akses ribuan video pembelajaran dari instruktur berpengalaman.
            </p>
        </td>
    </tr>
    <tr>
        <td style="padding: 12px 0; vertical-align: top; width: 40px;">
            <span style="font-size: 24px;">🏕️</span>
        </td>
        <td style="padding: 12px 0; vertical-align: top;">
            <strong style="color: #0F172A; font-size: 14px;">Bootcamp & Webinar</strong>
            <p style="color: #64748B; font-size: 13px; margin: 4px 0 0;">
                Ikuti bootcamp intensif dan webinar interaktif secara online.
            </p>
        </td>
    </tr>
    <tr>
        <td style="padding: 12px 0; vertical-align: top; width: 40px;">
            <span style="font-size: 24px;">📖</span>
        </td>
        <td style="padding: 12px 0; vertical-align: top;">
            <strong style="color: #0F172A; font-size: 14px;">Toko Buku Digital</strong>
            <p style="color: #64748B; font-size: 13px; margin: 4px 0 0;">
                Koleksi e-book dan buku fisik pilihan untuk menambah wawasan.
            </p>
        </td>
    </tr>
    <tr>
        <td style="padding: 12px 0; vertical-align: top; width: 40px;">
            <span style="font-size: 24px;">🏆</span>
        </td>
        <td style="padding: 12px 0; vertical-align: top;">
            <strong style="color: #0F172A; font-size: 14px;">Sertifikat Resmi</strong>
            <p style="color: #64748B; font-size: 13px; margin: 4px 0 0;">
                Dapatkan sertifikat setelah menyelesaikan kursus, sebagai bukti kompetensimu.
            </p>
        </td>
    </tr>
</table>

<hr class="divider">

{{-- Info Akun --}}
<div class="info-box">
    <table>
        <tr>
            <td class="info-label">Nama</td>
            <td class="info-value">{{ $user->name }}</td>
        </tr>
        <tr>
            <td class="info-label">Email</td>
            <td class="info-value">{{ $user->email }}</td>
        </tr>
        <tr>
            <td class="info-label">Terdaftar</td>
            <td class="info-value">{{ now()->translatedFormat('d F Y') }}</td>
        </tr>
    </table>
</div>

{{-- CTA --}}
<div style="text-align: center; margin: 24px 0;">
    <a href="{{ url('/courses') }}" class="cta-button">
        Jelajahi Kursus Sekarang →
    </a>
</div>

<p style="font-size: 13px; color: #64748B; text-align: center; line-height: 1.6;">
    Tip: Lengkapi profilmu di <a href="{{ url('/dashboard/settings') }}" style="color: #2563EB;">halaman pengaturan</a>
    untuk pengalaman belajar yang lebih personal.
</p>
@endsection
