@extends('emails.layouts.base')

@section('title', 'Bootcamp Baru' . ' — ' . \App\Models\Setting::get('site_name', '' . \App\Models\Setting::get('site_name', 'Skolah.com') . ''))

@section('content')
<div style="text-align: center; margin-bottom: 24px;">
    <div style="font-size: 48px; margin-bottom: 8px;">🚀</div>
    <h2 style="font-size: 22px; color: #0F172A; margin: 12px 0 4px;">
        Bootcamp Segera Dimulai!
    </h2>
    <p style="color: #64748B; font-size: 14px; margin: 0; line-height: 1.6;">
        Halo <strong>{{ $user->name }}</strong>, jangan lewatkan bootcamp keren ini!
    </p>
</div>

<hr class="divider">

{{-- Bootcamp Card --}}
<div style="background: linear-gradient(135deg, #7C3AED, #EC4899); border-radius: 16px; padding: 24px; margin: 16px 0; text-align: center; color: #FFFFFF;">
    <p style="font-size: 12px; text-transform: uppercase; letter-spacing: 2px; margin: 0 0 8px; opacity: 0.85;">
        {{ ucfirst($bootcamp->type) }} Bootcamp
    </p>
    <h3 style="font-size: 24px; font-weight: 800; margin: 0 0 12px; line-height: 1.3;">{{ $bootcamp->title }}</h3>

    @if($bootcamp->instructor)
        <p style="font-size: 13px; margin: 0 0 12px; opacity: 0.9;">
            👨‍🏫 oleh <strong>{{ $bootcamp->instructor->name }}</strong>
        </p>
    @endif

    <div style="background: rgba(255,255,255,0.2); display: inline-block; padding: 10px 28px; border-radius: 10px; margin-bottom: 8px;">
        @if($bootcamp->has_discount)
            <span style="font-size: 14px; text-decoration: line-through; opacity: 0.7; margin-right: 8px;">{{ rupiah($bootcamp->price) }}</span>
            <span style="font-size: 24px; font-weight: 800;">{{ rupiah($bootcamp->effective_price) }}</span>
        @elseif($bootcamp->price === 0)
            <span style="font-size: 24px; font-weight: 800;">GRATIS</span>
        @else
            <span style="font-size: 24px; font-weight: 800;">{{ rupiah($bootcamp->price) }}</span>
        @endif
    </div>
</div>

{{-- Custom Message --}}
@if($customMessage)
<div style="background: #F8FAFC; border-radius: 12px; padding: 16px; margin: 16px 0;">
    <p style="font-size: 14px; color: #1E293B; margin: 0; line-height: 1.6;">
        {{ $customMessage }}
    </p>
</div>
@endif

{{-- Bootcamp Details --}}
<div class="info-box">
    <table>
        <tr>
            <td class="info-label">Tanggal</td>
            <td class="info-value">
                @if($bootcamp->start_date)
                    {{ $bootcamp->start_date->translatedFormat('d F Y') }}
                    @if($bootcamp->end_date && $bootcamp->end_date->ne($bootcamp->start_date))
                        — {{ $bootcamp->end_date->translatedFormat('d F Y') }}
                    @endif
                @else
                    Akan diumumkan
                @endif
            </td>
        </tr>
        <tr>
            <td class="info-label">Tipe</td>
            <td class="info-value">{{ ucfirst($bootcamp->type) }}
                @if($bootcamp->platform)
                    ({{ $bootcamp->platform }})
                @endif
            </td>
        </tr>
        @if($bootcamp->location)
        <tr>
            <td class="info-label">Lokasi</td>
            <td class="info-value">{{ $bootcamp->location }}</td>
        </tr>
        @endif
        <tr>
            <td class="info-label">Harga</td>
            <td class="info-value">
                @if($bootcamp->has_discount)
                    <span style="text-decoration: line-through; color: #94A3B8;">{{ rupiah($bootcamp->price) }}</span>
                    → <strong style="color: #10B981;">{{ rupiah($bootcamp->effective_price) }}</strong>
                @else
                    {{ $bootcamp->price_formatted }}
                @endif
            </td>
        </tr>
        @if($bootcamp->max_participants)
        <tr>
            <td class="info-label">Kuota</td>
            <td class="info-value">{{ $bootcamp->max_participants - $bootcamp->total_registered }} slot tersisa</td>
        </tr>
        @endif
    </table>
</div>

{{-- Description --}}
@if($bootcamp->description)
<div style="background: #FDF4FF; border-radius: 12px; padding: 16px; margin: 16px 0;">
    <p style="font-size: 13px; color: #1E293B; margin: 0; line-height: 1.6;">
        {{ Str::limit(strip_tags($bootcamp->description), 250) }}
    </p>
</div>
@endif

{{-- CTA --}}
<div style="text-align: center; margin: 24px 0;">
    <a href="{{ url('/bootcamps/' . $bootcamp->slug) }}" class="cta-button" style="background: linear-gradient(135deg, #7C3AED, #EC4899);">
        🚀 Daftar Bootcamp →
    </a>
</div>

<p style="font-size: 12px; color: #94A3B8; text-align: center; line-height: 1.5;">
    *Kuota terbatas. Harga dan ketersediaan dapat berubah sewaktu-waktu.
</p>
@endsection
