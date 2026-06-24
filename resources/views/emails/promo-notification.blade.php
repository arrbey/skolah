@extends('emails.layouts.base')

@section('title', 'Promo Spesial' . ' — ' . \App\Models\Setting::get('site_name', '' . \App\Models\Setting::get('site_name', 'Skolah.com') . ''))

@section('content')
<div style="text-align: center; margin-bottom: 24px;">
    <div style="font-size: 48px; margin-bottom: 8px;">🎉</div>
    <h2 style="font-size: 22px; color: #0F172A; margin: 12px 0 4px;">
        Ada Promo Spesial Untukmu!
    </h2>
    <p style="color: #64748B; font-size: 14px; margin: 0; line-height: 1.6;">
        Halo <strong>{{ $user->name }}</strong>, jangan lewatkan kesempatan ini!
    </p>
</div>

<hr class="divider">

{{-- Promo Card --}}
<div style="background: linear-gradient(135deg, #2563EB, #7C3AED); border-radius: 16px; padding: 24px; margin: 16px 0; text-align: center; color: #FFFFFF;">
    <p style="font-size: 14px; margin: 0 0 8px; opacity: 0.9;">Kode Promo:</p>
    <div style="background: rgba(255,255,255,0.2); display: inline-block; padding: 12px 32px; border-radius: 10px; margin-bottom: 16px;">
        <span style="font-size: 28px; font-weight: 800; letter-spacing: 4px; font-family: monospace;">{{ $promoCode->code }}</span>
    </div>

    <p style="font-size: 36px; font-weight: 800; margin: 0;">
        @if($promoCode->discount_type === 'percent')
            DISKON {{ $promoCode->discount_value }}%
        @else
            HEMAT {{ rupiah($promoCode->discount_value) }}
        @endif
    </p>

    @if($promoCode->min_purchase)
    <p style="font-size: 13px; margin: 8px 0 0; opacity: 0.85;">
        Min. pembelian {{ rupiah($promoCode->min_purchase) }}
    </p>
    @endif
</div>

{{-- Custom Message --}}
@if($customMessage)
<div style="background: #F8FAFC; border-radius: 12px; padding: 16px; margin: 16px 0;">
    <p style="font-size: 14px; color: #1E293B; margin: 0; line-height: 1.6;">
        {{ $customMessage }}
    </p>
</div>
@endif

{{-- Promo Details --}}
<div class="info-box">
    <table>
        <tr>
            <td class="info-label">Berlaku</td>
            <td class="info-value">
                @if($promoCode->expires_at)
                    Sampai {{ $promoCode->expires_at->translatedFormat('d F Y') }}
                @else
                    Tanpa batas waktu
                @endif
            </td>
        </tr>
        @if($promoCode->max_uses)
        <tr>
            <td class="info-label">Kuota</td>
            <td class="info-value">{{ $promoCode->max_uses - $promoCode->used_count }} tersisa</td>
        </tr>
        @endif
        <tr>
            <td class="info-label">Berlaku untuk</td>
            <td class="info-value">Kursus, Bootcamp, Buku & Membership</td>
        </tr>
    </table>
</div>

{{-- How To Use --}}
<div style="background: #ECFDF5; border-radius: 12px; padding: 16px; margin: 16px 0;">
    <p style="font-size: 14px; color: #065F46; margin: 0 0 8px; font-weight: 600;">
        🛒 Cara Pakai:
    </p>
    <ol style="font-size: 13px; color: #1E293B; margin: 0; padding-left: 20px; line-height: 1.8;">
        <li>Pilih kursus, bootcamp, atau buku yang kamu inginkan</li>
        <li>Tambahkan ke keranjang belanja</li>
        <li>Masukkan kode <strong>{{ $promoCode->code }}</strong> di halaman checkout</li>
        <li>Nikmati diskonnya! 🎊</li>
    </ol>
</div>

{{-- CTA --}}
<div style="text-align: center; margin: 24px 0;">
    <a href="{{ url('/courses') }}" class="cta-button">
        🛍️ Belanja Sekarang →
    </a>
</div>

<p style="font-size: 12px; color: #94A3B8; text-align: center; line-height: 1.5;">
    *Promo dapat berakhir sewaktu-waktu. Syarat dan ketentuan berlaku.
</p>
@endsection
