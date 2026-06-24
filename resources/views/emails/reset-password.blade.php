@extends('emails.layouts.base')

@section('title', 'Reset Password' . ' — ' . \App\Models\Setting::get('site_name', '' . \App\Models\Setting::get('site_name', 'Skolah.com') . ''))

@section('content')
<div style="text-align: center; margin-bottom: 24px;">
    <div style="font-size: 48px; margin-bottom: 8px;">🔐</div>
    <h2 style="font-size: 22px; color: #0F172A; margin: 12px 0 4px;">
        Reset Password
    </h2>
    <p style="color: #64748B; font-size: 14px; margin: 0; line-height: 1.6;">
        Halo <strong>{{ $user->name }}</strong>, kami menerima permintaan untuk mereset password akun Skolah.com kamu.
    </p>
</div>

<hr class="divider">

<p style="color: #1E293B; font-size: 14px; line-height: 1.6; margin-bottom: 8px;">
    Klik tombol di bawah ini untuk membuat password baru:
</p>

{{-- CTA --}}
<div style="text-align: center; margin: 24px 0;">
    <a href="{{ $url }}" class="cta-button">
        🔑 Reset Password Sekarang
    </a>
</div>

<div class="info-box">
    <p style="font-size: 13px; color: #64748B; margin: 0; line-height: 1.6;">
        <strong>⏰ Link ini akan kadaluarsa dalam 60 menit.</strong><br>
        Jika kamu tidak meminta reset password, abaikan email ini — akunmu tetap aman.
    </p>
</div>

<hr class="divider">

<p style="font-size: 12px; color: #94A3B8; line-height: 1.6;">
    Jika tombol di atas tidak berfungsi, salin dan paste URL berikut ke browser kamu:
</p>
<p style="font-size: 12px; color: #2563EB; word-break: break-all; line-height: 1.6;">
    {{ $url }}
</p>
@endsection
