@extends('emails.layouts.base')

@section('title', 'Bonus Promo Code' . ' — ' . \App\Models\Setting::get('site_name', '' . \App\Models\Setting::get('site_name', 'Skolah.com') . ''))

@section('extra-styles')
<style>
    .promo-card {
        background: linear-gradient(135deg, #4F46E5, #7C3AED);
        border-radius: 16px;
        padding: 28px 24px;
        text-align: center;
        margin: 24px 0;
    }
    .promo-label {
        color: rgba(255,255,255,0.8);
        font-size: 13px;
        margin: 0 0 10px;
        letter-spacing: 1px;
        text-transform: uppercase;
    }
    .promo-code-box {
        display: inline-block;
        background: #FFFFFF;
        color: #4F46E5;
        font-size: 28px;
        font-weight: 800;
        letter-spacing: 4px;
        padding: 12px 32px;
        border-radius: 12px;
        margin: 8px 0 16px;
    }
    .promo-discount {
        color: #FFFFFF;
        font-size: 15px;
        font-weight: 600;
        margin: 0;
    }
    .membership-badge {
        display: inline-block;
        background: #EEF2FF;
        color: #4F46E5;
        font-size: 12px;
        font-weight: 600;
        padding: 5px 14px;
        border-radius: 50px;
        margin-bottom: 16px;
    }
</style>
@endsection

@section('content')
    <p style="font-size: 16px; color: #1E293B; margin-top: 0;">Halo, <strong>{{ $user->name }}</strong>! 👋</p>

    <span class="membership-badge">🌟 Member {{ $plan->name }}</span>

    <p style="color: #475569; font-size: 14px; line-height: 1.7;">
        Terima kasih telah berlangganan paket <strong>{{ $plan->name }}</strong> di Skolah.com!
        Sebagai bentuk apresiasi kami, kamu mendapatkan <strong>bonus kode promo eksklusif</strong> berikut:
    </p>

    {{-- Promo Code Card --}}
    <div class="promo-card">
        <p class="promo-label">Kode Promo Spesial Kamu</p>
        <div class="promo-code-box">{{ $promoCode->code }}</div>
        <p class="promo-discount">
            Diskon {{ $promoCode->discount_label }}
            @if($promoCode->discount_type === 'percent') untuk pembelian kamu @endif
        </p>
    </div>

    {{-- Detail --}}
    <div class="info-box">
        <table>
            <tr>
                <td class="info-label">Tipe Diskon</td>
                <td class="info-value">{{ $promoCode->discount_type === 'percent' ? 'Persentase' : 'Nominal Tetap' }}</td>
            </tr>
            <tr>
                <td class="info-label">Nilai Diskon</td>
                <td class="info-value" style="color: #7C3AED; font-size: 16px;">{{ $promoCode->discount_label }}</td>
            </tr>
            @if($promoCode->min_purchase)
            <tr>
                <td class="info-label">Min. Pembelian</td>
                <td class="info-value">{{ rupiah($promoCode->min_purchase) }}</td>
            </tr>
            @endif
            @if($promoCode->applicable_type !== 'all')
            <tr>
                <td class="info-label">Berlaku Untuk</td>
                <td class="info-value">{{ $promoCode->applicable_label }}</td>
            </tr>
            @endif
            <tr>
                <td class="info-label">Berlaku Sampai</td>
                <td class="info-value">{{ $promoCode->expires_at ? $promoCode->expires_at->translatedFormat('d F Y') : 'Tanpa batas waktu' }}</td>
            </tr>
            <tr>
                <td class="info-label">Sisa Kuota</td>
                <td class="info-value">
                    @if($promoCode->max_uses)
                        {{ $promoCode->max_uses - $promoCode->used_count }} dari {{ $promoCode->max_uses }}
                    @else
                        Tidak terbatas
                    @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- Cara pakai --}}
    <p style="font-size: 14px; color: #475569; line-height: 1.7;">
        <strong>Cara menggunakan:</strong><br>
        Masukkan kode <strong style="color: #4F46E5;">{{ $promoCode->code }}</strong> di halaman
        <em>Keranjang Belanja</em> saat checkout, lalu klik <em>"Terapkan"</em>.
    </p>

    <hr class="divider">

    <div class="text-center">
        <a href="{{ config('app.url') }}/courses" class="cta-button" style="background: linear-gradient(135deg, #4F46E5, #7C3AED);">
            🛍️ Belanja Sekarang
        </a>
    </div>

    <p style="font-size: 13px; color: #94A3B8; text-align: center; margin-top: 16px; line-height: 1.6;">
        Promo code ini diberikan secara eksklusif untuk member <strong>{{ $plan->name }}</strong>.<br>
        Jangan dibagikan ke orang lain ya! 😊
    </p>
@endsection
