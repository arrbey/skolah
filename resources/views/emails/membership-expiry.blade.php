@extends('emails.layouts.base')

@section('title', 'Membership Akan Berakhir' . ' — ' . \App\Models\Setting::get('site_name', '' . \App\Models\Setting::get('site_name', 'Skolah.com') . ''))

@section('content')
<div style="text-align: center; margin-bottom: 24px;">
    <div style="font-size: 48px; margin-bottom: 8px;">⚡</div>
    <div class="badge badge-warning">⏳ Membership Akan Berakhir</div>
    <h2 style="font-size: 22px; color: #0F172A; margin: 12px 0 4px;">
        Halo, {{ $user->name }}!
    </h2>
    <p style="color: #64748B; font-size: 14px; margin: 0; line-height: 1.6;">
        Membership <strong>{{ $plan->name }}</strong> kamu akan berakhir dalam
        <strong style="color: #D97706;">{{ $membership->days_remaining }} hari</strong>.
    </p>
</div>

<hr class="divider">

{{-- Membership Info --}}
<div class="info-box">
    <table>
        <tr>
            <td class="info-label">Paket</td>
            <td class="info-value">{{ $plan->name }}</td>
        </tr>
        <tr>
            <td class="info-label">Siklus</td>
            <td class="info-value">{{ $membership->billing_cycle_label }}</td>
        </tr>
        <tr>
            <td class="info-label">Berlaku Sejak</td>
            <td class="info-value">{{ $membership->started_at_formatted }}</td>
        </tr>
        <tr>
            <td class="info-label">Berakhir Pada</td>
            <td class="info-value" style="color: #D97706; font-weight: 700;">{{ $membership->expires_at_formatted }}</td>
        </tr>
    </table>
</div>

{{-- Benefits Reminder --}}
@if($plan->features && count($plan->features) > 0)
<div style="background: #EFF6FF; border-radius: 12px; padding: 16px; margin: 16px 0;">
    <p style="font-size: 14px; color: #1D4ED8; margin: 0 0 8px; font-weight: 600;">
        🎁 Benefit yang akan kamu kehilangan:
    </p>
    <ul style="font-size: 13px; color: #1E293B; margin: 0; padding-left: 20px; line-height: 1.8;">
        @foreach(array_slice($plan->features, 0, 5) as $feature)
            <li>{{ $feature }}</li>
        @endforeach
    </ul>
</div>
@endif

{{-- CTA --}}
<div style="text-align: center; margin: 24px 0;">
    <a href="{{ url('/membership') }}" class="cta-button cta-button-success">
        🔄 Perpanjang Membership →
    </a>
</div>

<p style="font-size: 13px; color: #94A3B8; text-align: center; line-height: 1.6;">
    Perpanjang sekarang agar akses premium kamu tidak terputus.
</p>
@endsection
