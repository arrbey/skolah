<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Pengiriman — {{ \App\Models\Setting::get('site_name', 'Skolah.com') }}</title>
    <style>
        body { margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #F8FAFC; color: #1E293B; }
        .container { max-width: 600px; margin: 0 auto; background: #FFFFFF; }
        .header { background: linear-gradient(135deg, #2563EB, #7C3AED); padding: 32px 24px; text-align: center; }
        .header h1 { color: #FFFFFF; font-size: 24px; margin: 0; font-weight: 700; }
        .header p { color: rgba(255,255,255,0.85); font-size: 14px; margin: 8px 0 0; }
        .content { padding: 32px 24px; }
        .badge { display: inline-block; font-size: 13px; font-weight: 600; padding: 6px 14px; border-radius: 50px; margin-bottom: 16px; }
        .badge-yellow  { background: #FEF9C3; color: #854D0E; }
        .badge-blue    { background: #DBEAFE; color: #1D4ED8; }
        .badge-indigo  { background: #E0E7FF; color: #4338CA; }
        .badge-green   { background: #DCFCE7; color: #166534; }
        .badge-red     { background: #FEE2E2; color: #991B1B; }
        .info-table { width: 100%; border-collapse: collapse; background: #F8FAFC; border-radius: 12px; overflow: hidden; margin: 20px 0; }
        .info-table td { padding: 10px 16px; font-size: 14px; border-bottom: 1px solid #E2E8F0; }
        .info-table tr:last-child td { border-bottom: none; }
        .info-label { color: #64748B; width: 45%; }
        .info-value { color: #0F172A; font-weight: 600; }
        .timeline { margin: 24px 0; }
        .timeline-item { display: flex; gap: 12px; margin-bottom: 16px; }
        .timeline-dot { width: 10px; height: 10px; border-radius: 50%; margin-top: 4px; shrink: 0; flex-shrink: 0; }
        .dot-done    { background: #2563EB; }
        .dot-current { background: #2563EB; box-shadow: 0 0 0 4px rgba(37,99,235,0.2); }
        .dot-pending { background: #CBD5E1; }
        .timeline-content h4 { font-size: 13px; font-weight: 600; color: #0F172A; margin: 0 0 2px; }
        .timeline-content p  { font-size: 12px; color: #64748B; margin: 0; }
        .cta-button { display: inline-block; background: #2563EB; color: #FFFFFF !important; text-decoration: none; padding: 14px 32px; border-radius: 12px; font-size: 14px; font-weight: 600; margin: 16px 0; }
        .note-box { background: #EFF6FF; border-left: 4px solid #2563EB; border-radius: 0 8px 8px 0; padding: 12px 16px; margin: 16px 0; font-size: 13px; color: #1D4ED8; }
        .footer { background: #F8FAFC; padding: 24px; text-align: center; font-size: 12px; color: #94A3B8; border-top: 1px solid #E2E8F0; }
        .footer a { color: #2563EB; text-decoration: none; }
    </style>
</head>
<body>
<div class="container">
    {{-- Header --}}
    <div class="header">
        <h1>{{ \App\Models\Setting::get('site_name', 'Skolah.com') }}</h1>
        <p>Platform Edukasi Digital Terlengkap di Indonesia</p>
    </div>

    <div class="content">
        {{-- Status Badge --}}
        <div style="text-align:center; margin-bottom: 20px;">
            @php
                $badgeClass = match($bookOrder->shipping_status) {
                    'pending'    => 'badge-yellow',
                    'processing' => 'badge-blue',
                    'shipped'    => 'badge-indigo',
                    'delivered'  => 'badge-green',
                    'cancelled'  => 'badge-red',
                    default      => 'badge-yellow',
                };
                $icon = match($bookOrder->shipping_status) {
                    'pending'    => '🕐',
                    'processing' => '📦',
                    'shipped'    => '🚚',
                    'delivered'  => '✅',
                    'cancelled'  => '❌',
                    default      => '📋',
                };
            @endphp
            <span class="badge {{ $badgeClass }}">{{ $icon }} {{ $bookOrder->status_label }}</span>
            <h2 style="font-size: 20px; color: #0F172A; margin: 8px 0 4px;">
                Halo, {{ $user->name }}!
            </h2>
            <p style="color: #64748B; font-size: 14px; margin: 0;">
                Ada update terbaru untuk pengiriman bukumu.
            </p>
        </div>

        {{-- Info Buku --}}
        <table class="info-table">
            <tr>
                <td class="info-label">Buku</td>
                <td class="info-value">{{ $book->title }}</td>
            </tr>
            <tr>
                <td class="info-label">Status Terbaru</td>
                <td class="info-value">{{ $bookOrder->status_label }}</td>
            </tr>
            @if($bookOrder->courier)
            <tr>
                <td class="info-label">Kurir</td>
                <td class="info-value">{{ $bookOrder->courier_label }}</td>
            </tr>
            @endif
            @if($bookOrder->tracking_number)
            <tr>
                <td class="info-label">No. Resi</td>
                <td class="info-value" style="font-family: monospace;">{{ $bookOrder->tracking_number }}</td>
            </tr>
            @endif
            @if($bookOrder->shipped_at)
            <tr>
                <td class="info-label">Tanggal Kirim</td>
                <td class="info-value">{{ $bookOrder->shipped_at->translatedFormat('d F Y, H:i') }}</td>
            </tr>
            @endif
            @if($bookOrder->delivered_at)
            <tr>
                <td class="info-label">Tanggal Terima</td>
                <td class="info-value">{{ $bookOrder->delivered_at->translatedFormat('d F Y, H:i') }}</td>
            </tr>
            @endif
        </table>

        {{-- Catatan --}}
        @if($note)
        <div class="note-box">
            <strong>Catatan dari tim kami:</strong><br>
            {{ $note }}
        </div>
        @endif

        {{-- Pesan berdasarkan status --}}
        <div style="background: #F8FAFC; border-radius: 12px; padding: 16px; margin: 16px 0; font-size: 14px; color: #475569;">
            @if($bookOrder->shipping_status === 'processing')
                📦 Pesananmu sedang kami siapkan untuk dikirim. Kami akan mengirimkan update lagi saat paket sudah dikirim.
            @elseif($bookOrder->shipping_status === 'shipped')
                🚚 Paketmu sudah dalam perjalanan! Gunakan nomor resi <strong>{{ $bookOrder->tracking_number }}</strong> untuk melacak di website {{ $bookOrder->courier_label }}.
            @elseif($bookOrder->shipping_status === 'delivered')
                🎉 Paketmu sudah tiba! Semoga buku yang kamu beli bermanfaat ya. Selamat belajar!
            @else
                Pantau status pengiriman bukumu secara real-time dari dashboard.
            @endif
        </div>

        {{-- CTA --}}
        <div style="text-align: center; margin-top: 24px;">
            <a href="{{ config('app.url') }}/dashboard/my-books/{{ $bookOrder->id }}" class="cta-button">
                📦 Lihat Detail Pengiriman
            </a>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p>Email ini dikirim otomatis oleh <strong>{{ \App\Models\Setting::get('site_name', 'Skolah.com') }}</strong>.<br>
            Jangan balas email ini. Jika ada pertanyaan, hubungi kami di
            <a href="mailto:{{\App\Models\Setting::get('site_email', 'support@skolah.com')}}">{{\App\Models\Setting::get('site_email', 'support@skolah.com')}}</a>
        </p>
        <p style="margin-top: 8px;">
            <a href="{{ config('app.url') }}">skolah.com</a> ·
            <a href="{{ config('app.url') }}/terms">Syarat & Ketentuan</a> ·
            <a href="{{ config('app.url') }}/privacy">Kebijakan Privasi</a>
        </p>
    </div>
</div>
</body>
</html>
