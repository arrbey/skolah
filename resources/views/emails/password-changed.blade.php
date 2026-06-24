@extends('emails.layouts.base')

@section('title', 'Password Berhasil Diubah' . ' — ' . \App\Models\Setting::get('site_name', '' . \App\Models\Setting::get('site_name', 'Skolah.com') . ''))

@section('content')
<div style="text-align: center; margin-bottom: 24px;">
    <div style="font-size: 48px; margin-bottom: 8px;">✅</div>
    <h2 style="font-size: 22px; color: #0F172A; margin: 12px 0 4px;">
        Password Berhasil Diubah
    </h2>
    <p style="color: #64748B; font-size: 14px; margin: 0; line-height: 1.6;">
        Halo <strong>{{ $user->name }}</strong>, password akun Skolah.com kamu telah berhasil diperbarui.
    </p>
</div>

<hr class="divider">

<div class="info-box">
    <table>
        <tr>
            <td class="info-label">Email Akun</td>
            <td class="info-value">{{ $user->email }}</td>
        </tr>
        <tr>
            <td class="info-label">Waktu Perubahan</td>
            <td class="info-value">{{ now()->translatedFormat('d F Y, H:i') }} WIB</td>
        </tr>
    </table>
</div>

<div style="background: #FEF2F2; border-radius: 12px; padding: 16px; margin: 20px 0;">
    <p style="font-size: 14px; color: #DC2626; margin: 0; line-height: 1.6;">
        <strong>⚠️ Bukan kamu yang mengubah?</strong><br>
        Jika kamu merasa tidak melakukan perubahan password ini, segera hubungi tim support kami
        di <a href="mailto:{{\App\Models\Setting::get('site_email', 'support@skolah.com')}}" style="color: #DC2626; font-weight: 600;">{{\App\Models\Setting::get('site_email', 'support@skolah.com')}}</a>
        atau reset password kamu kembali.
    </p>
</div>

{{-- CTA --}}
<div style="text-align: center; margin: 24px 0;">
    <a href="{{ url('/login') }}" class="cta-button">
        Login dengan Password Baru →
    </a>
</div>
@endsection
