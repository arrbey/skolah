@extends('emails.layouts.base')

@section('title', 'Segera Selesaikan Pembayaran' . ' — ' . \App\Models\Setting::get('site_name', '' . \App\Models\Setting::get('site_name', 'Skolah.com') . ''))

@section('content')
<div style="text-align: center; margin-bottom: 24px;">
    <div style="font-size: 48px; margin-bottom: 8px;">⏰</div>
    <div class="badge badge-warning">⚠️ Menunggu Pembayaran</div>
    <h2 style="font-size: 22px; color: #0F172A; margin: 12px 0 4px;">
        Halo, {{ $user->name }}!
    </h2>
    <p style="color: #64748B; font-size: 14px; margin: 0; line-height: 1.6;">
        Kamu memiliki pesanan yang belum diselesaikan. Segera bayar sebelum batas waktu habis!
    </p>
</div>

<hr class="divider">

{{-- Warning Box --}}
<div style="background: #FEF3C7; border: 1px solid #FDE68A; border-radius: 12px; padding: 16px; margin: 16px 0; text-align: center;">
    <p style="font-size: 14px; color: #92400E; margin: 0; font-weight: 600;">
        ⏳ Batas waktu pembayaran:
    </p>
    <p style="font-size: 20px; color: #92400E; margin: 8px 0 0; font-weight: 700;">
        {{ $order->payment_expires_at->translatedFormat('d F Y, H:i') }} WIB
    </p>
    @if($order->payment_expires_at->isFuture())
    <p style="font-size: 13px; color: #B45309; margin: 4px 0 0;">
        ({{ $order->time_remaining }} lagi)
    </p>
    @endif
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
            <td class="info-label">Total Bayar</td>
            <td class="info-value" style="color: #2563EB;">{{ $order->total_formatted }}</td>
        </tr>
    </table>
</div>

{{-- Items --}}
<h3 style="font-size: 14px; font-weight: 600; color: #0F172A; margin: 16px 0 8px;">Pesananmu:</h3>
@foreach($items as $item)
<div style="display: flex; align-items: center; padding: 8px 0; border-bottom: 1px solid #F1F5F9;">
    <div style="flex: 1;">
        <p style="font-size: 14px; color: #0F172A; margin: 0; font-weight: 500;">{{ $item->item_name }}</p>
    </div>
    <span style="font-size: 14px; color: #0F172A; font-weight: 600;">{{ rupiah($item->price) }}</span>
</div>
@endforeach

{{-- CTA --}}
<div style="text-align: center; margin: 24px 0;">
    <a href="{{ url('/dashboard/orders/' . $order->id . '/pay') }}" class="cta-button">
        💳 Bayar Sekarang →
    </a>
</div>

<p style="font-size: 13px; color: #94A3B8; text-align: center; line-height: 1.6;">
    Jika tidak membayar sebelum batas waktu, pesanan akan otomatis dibatalkan.
</p>
@endsection
