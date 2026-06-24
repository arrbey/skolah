@extends('emails.layouts.base')

@section('title', 'Segera Bayar Pesananmu' . ' — ' . \App\Models\Setting::get('site_name', '' . \App\Models\Setting::get('site_name', 'Skolah.com') . ''))

@section('content')
<div style="text-align: center; margin-bottom: 24px;">
    <div style="font-size: 48px; margin-bottom: 8px;">🛒</div>
    <div class="badge badge-warning">💳 Menunggu Pembayaran</div>
    <h2 style="font-size: 22px; color: #0F172A; margin: 12px 0 4px;">
        Halo, {{ $user->name }}!
    </h2>
    <p style="color: #64748B; font-size: 14px; margin: 0; line-height: 1.6;">
        Pesananmu sudah dibuat dan menunggu pembayaran. Segera selesaikan pembayaran agar kamu bisa langsung belajar!
    </p>
</div>

<hr class="divider">

{{-- Urgency Box --}}
<div style="background: linear-gradient(135deg, #FEF3C7, #FDE68A); border: 1px solid #F59E0B; border-radius: 12px; padding: 20px; margin: 16px 0; text-align: center;">
    <p style="font-size: 14px; color: #92400E; margin: 0 0 4px; font-weight: 600;">
        ⏰ Batas waktu pembayaran:
    </p>
    <p style="font-size: 22px; color: #78350F; margin: 8px 0 0; font-weight: 700;">
        {{ $order->payment_expires_at ? $order->payment_expires_at->translatedFormat('d F Y, H:i') . ' WIB' : '24 jam dari sekarang' }}
    </p>
    <p style="font-size: 13px; color: #B45309; margin: 8px 0 0;">
        Pesanan akan otomatis dibatalkan jika tidak dibayar sebelum batas waktu.
    </p>
</div>

{{-- Order Info --}}
<div class="info-box">
    <table>
        <tr>
            <td class="info-label">No. Order</td>
            <td class="info-value">{{ $order->order_number }}</td>
        </tr>
        <tr>
            <td class="info-label">Tanggal Order</td>
            <td class="info-value">{{ $order->created_at->translatedFormat('d F Y, H:i') }}</td>
        </tr>
        <tr>
            <td class="info-label">Status</td>
            <td class="info-value">
                <span style="background: #FFFBEB; color: #D97706; font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 50px;">
                    Menunggu Pembayaran
                </span>
            </td>
        </tr>
    </table>
</div>

{{-- Items --}}
<h3 style="font-size: 16px; font-weight: 600; color: #0F172A; margin: 20px 0 8px;">📦 Detail Pesanan</h3>

<table style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th style="text-align: left; font-size: 12px; text-transform: uppercase; color: #64748B; font-weight: 600; padding: 8px 0; border-bottom: 2px solid #E2E8F0;">Item</th>
            <th style="text-align: center; font-size: 12px; text-transform: uppercase; color: #64748B; font-weight: 600; padding: 8px 0; border-bottom: 2px solid #E2E8F0;">Qty</th>
            <th style="text-align: right; font-size: 12px; text-transform: uppercase; color: #64748B; font-weight: 600; padding: 8px 0; border-bottom: 2px solid #E2E8F0;">Harga</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $item)
        <tr>
            <td style="padding: 12px 0; font-size: 14px; border-bottom: 1px solid #F1F5F9; vertical-align: top;">
                <span style="color: #0F172A; font-weight: 500;">{{ $item->item_name }}</span>
                <br>
                <span style="color: #64748B; font-size: 12px;">{{ $item->item_type_label }}</span>
            </td>
            <td style="padding: 12px 0; font-size: 14px; border-bottom: 1px solid #F1F5F9; text-align: center; color: #64748B;">{{ $item->quantity }}</td>
            <td style="padding: 12px 0; font-size: 14px; border-bottom: 1px solid #F1F5F9; text-align: right; color: #0F172A; font-weight: 600;">{{ rupiah($item->price) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- Total --}}
<div style="background: #F8FAFC; border-radius: 12px; padding: 16px; margin: 20px 0;">
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 4px 0; font-size: 14px; color: #64748B;">Subtotal</td>
            <td style="padding: 4px 0; font-size: 14px; color: #0F172A; text-align: right;">{{ $order->subtotal_formatted }}</td>
        </tr>
        @if($order->discount_amount > 0)
        <tr>
            <td style="padding: 4px 0; font-size: 14px; color: #059669;">
                Diskon
                @if($order->promo_code)
                    ({{ $order->promo_code }})
                @endif
            </td>
            <td style="padding: 4px 0; font-size: 14px; color: #059669; text-align: right;">-{{ $order->discount_amount_formatted }}</td>
        </tr>
        @endif
        <tr>
            <td style="padding: 12px 0 4px; font-size: 20px; font-weight: 700; color: #2563EB; border-top: 2px solid #E2E8F0;">Total Bayar</td>
            <td style="padding: 12px 0 4px; font-size: 20px; font-weight: 700; color: #2563EB; text-align: right; border-top: 2px solid #E2E8F0;">{{ $order->total_formatted }}</td>
        </tr>
    </table>
</div>

{{-- CTA --}}
<div style="text-align: center; margin: 28px 0;">
    <a href="{{ route('dashboard.orders.pay', $order->id) }}" class="cta-button" style="font-size: 16px; padding: 16px 40px; background: linear-gradient(135deg, #2563EB, #7C3AED);">
        💳 Bayar Sekarang →
    </a>
</div>

{{-- Steps --}}
<div style="background: #EFF6FF; border-radius: 12px; padding: 20px; margin: 16px 0;">
    <h4 style="font-size: 14px; font-weight: 600; color: #1E40AF; margin: 0 0 12px;">📋 Cara Bayar:</h4>
    <ol style="margin: 0; padding-left: 20px; font-size: 13px; color: #1E40AF; line-height: 2;">
        <li>Klik tombol <strong>"Bayar Sekarang"</strong> di atas</li>
        <li>Pilih metode pembayaran yang kamu inginkan</li>
        <li>Selesaikan pembayaran sesuai instruksi</li>
        <li>Akses konten akan langsung tersedia setelah pembayaran berhasil! 🎉</li>
    </ol>
</div>

<p style="font-size: 13px; color: #94A3B8; text-align: center; line-height: 1.6; margin-top: 20px;">
    Jika kamu mengalami masalah saat pembayaran, jangan ragu untuk menghubungi kami.
</p>
@endsection
