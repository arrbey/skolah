<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    @page {
        size: A5 portrait;
        margin: 0;
    }

    body {
        font-family: DejaVu Sans, sans-serif;
        width: 148mm;
        height: 210mm;
        background: #ffffff;
        color: #1E293B;
        position: relative;
        overflow: hidden;
    }

    /* ── TOP STRIP ───────────────────────────────── */
    .top-strip {
        width: 100%;
        height: 8mm;
        background: linear-gradient(90deg, #2563EB 0%, #7C3AED 50%, #38BDF8 100%);
    }

    /* ── MAIN CONTENT ────────────────────────────── */
    .main {
        padding: 6mm 8mm 4mm 8mm;
    }

    /* Logo & header */
    .header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 5mm;
        padding-bottom: 4mm;
        border-bottom: 0.5mm solid #E2E8F0;
    }

    .logo-box {
        display: flex;
        align-items: center;
        gap: 2mm;
    }

    .logo-icon {
        width: 8mm;
        height: 8mm;
        background: #2563EB;
        border-radius: 2mm;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .logo-icon span {
        color: white;
        font-size: 5mm;
        font-weight: 800;
    }

    .logo-text {
        font-size: 4.5mm;
        font-weight: 800;
        color: #0F172A;
    }

    .ticket-label {
        text-align: right;
    }

    .ticket-label .label {
        font-size: 2.5mm;
        color: #94A3B8;
        text-transform: uppercase;
        letter-spacing: 0.5mm;
    }

    .ticket-label .type {
        font-size: 3mm;
        font-weight: 700;
        color: #D97706;
        margin-top: 0.5mm;
    }

    /* Event name */
    .event-name {
        font-size: 5mm;
        font-weight: 800;
        color: #0F172A;
        line-height: 1.3;
        margin-bottom: 3mm;
    }

    /* Badges */
    .badges {
        display: flex;
        gap: 2mm;
        margin-bottom: 4mm;
    }

    .badge {
        padding: 1mm 3mm;
        border-radius: 2mm;
        font-size: 2.8mm;
        font-weight: 700;
    }

    .badge-offline {
        background: #FEF3C7;
        color: #D97706;
        border: 0.3mm solid #FCD34D;
    }

    .badge-status-upcoming { background: #DBEAFE; color: #1D4ED8; border: 0.3mm solid #BFDBFE; }
    .badge-status-ongoing  { background: #D1FAE5; color: #047857; border: 0.3mm solid #6EE7B7; }
    .badge-status-completed{ background: #F1F5F9; color: #475569; border: 0.3mm solid #CBD5E1; }

    /* Info row grid */
    .info-grid {
        display: flex;
        gap: 3mm;
        margin-bottom: 4mm;
    }

    .info-cell {
        flex: 1;
        background: #F8FAFC;
        border: 0.3mm solid #E2E8F0;
        border-radius: 2mm;
        padding: 2.5mm 3mm;
    }

    .info-cell .label {
        font-size: 2.3mm;
        color: #94A3B8;
        text-transform: uppercase;
        letter-spacing: 0.4mm;
        margin-bottom: 1mm;
        font-weight: 700;
    }

    .info-cell .value {
        font-size: 3mm;
        font-weight: 700;
        color: #1E293B;
        line-height: 1.3;
    }

    /* Dashed separator */
    .dashed-sep {
        border-top: 0.5mm dashed #CBD5E1;
        margin: 4mm 0;
        position: relative;
    }

    /* Peserta section */
    .peserta {
        display: flex;
        align-items: center;
        gap: 3mm;
        margin-bottom: 4mm;
    }

    .peserta-avatar {
        width: 10mm;
        height: 10mm;
        border-radius: 50%;
        background: #DBEAFE;
        border: 0.5mm solid #BFDBFE;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 5mm;
        font-weight: 800;
        color: #2563EB;
        overflow: hidden;
        flex-shrink: 0;
    }

    .peserta-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    .peserta-info .label {
        font-size: 2.3mm;
        color: #94A3B8;
        text-transform: uppercase;
        letter-spacing: 0.4mm;
        margin-bottom: 0.5mm;
        font-weight: 700;
    }

    .peserta-info .name {
        font-size: 4mm;
        font-weight: 800;
        color: #0F172A;
    }

    .peserta-info .email {
        font-size: 2.8mm;
        color: #64748B;
        margin-top: 0.5mm;
    }

    /* Bottom section: ticket code + QR */
    .bottom-section {
        display: flex;
        gap: 4mm;
        align-items: flex-start;
    }

    .ticket-code-block {
        flex: 1;
    }

    .ticket-code-block .label {
        font-size: 2.3mm;
        color: #94A3B8;
        text-transform: uppercase;
        letter-spacing: 0.4mm;
        margin-bottom: 2mm;
        font-weight: 700;
    }

    .ticket-code-value {
        background: #0F172A;
        border-radius: 3mm;
        padding: 3mm 4mm;
        color: #ffffff;
        font-size: 4mm;
        font-family: DejaVu Sans Mono, monospace;
        font-weight: 700;
        letter-spacing: 0.5mm;
        text-align: center;
    }

    .registered-info {
        margin-top: 3mm;
        font-size: 2.5mm;
        color: #94A3B8;
        line-height: 1.5;
    }

    .registered-info strong {
        color: #475569;
    }

    /* QR code block */
    .qr-block {
        text-align: center;
        flex-shrink: 0;
    }

    .qr-block img {
        width: 28mm;
        height: 28mm;
        border: 0.5mm solid #E2E8F0;
        border-radius: 2mm;
        display: block;
    }

    .qr-block .qr-label {
        font-size: 2.2mm;
        color: #94A3B8;
        margin-top: 1mm;
        text-align: center;
    }

    /* Bottom strip */
    .bottom-strip {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 6mm;
        background: linear-gradient(90deg, #2563EB 0%, #7C3AED 50%, #38BDF8 100%);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .bottom-strip span {
        color: rgba(255,255,255,0.9);
        font-size: 2.3mm;
        font-weight: 600;
        letter-spacing: 0.5mm;
        text-transform: uppercase;
    }

    /* Watermark background */
    .watermark {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-30deg);
        font-size: 22mm;
        font-weight: 900;
        color: rgba(37, 99, 235, 0.03);
        white-space: nowrap;
        pointer-events: none;
        z-index: 0;
        letter-spacing: 2mm;
    }
</style>
</head>
<body>

<div class="watermark">SKOLAH.COM</div>

{{-- TOP STRIP --}}
<div class="top-strip"></div>

{{-- MAIN CONTENT --}}
<div class="main">

    {{-- Header: logo + label tiket --}}
    <div class="header">
        <div class="logo-box">
            <div class="logo-icon"><span>S</span></div>
            <span class="logo-text">{{ \App\Models\Setting::get('site_name', 'Skolah.com') }}</span>
        </div>
        <div class="ticket-label">
            <div class="label">E-Tiket Resmi</div>
            <div class="type">📍 Event Offline</div>
        </div>
    </div>

    {{-- Nama event --}}
    <div class="event-name">{{ $registration->bootcamp->title }}</div>

    {{-- Badges --}}
    <div class="badges">
        <span class="badge badge-offline">📍 Offline</span>
        @php
            $statusClass = match($registration->bootcamp->status) {
                'upcoming'  => 'badge-status-upcoming',
                'ongoing'   => 'badge-status-ongoing',
                default     => 'badge-status-completed',
            };
        @endphp
        <span class="badge {{ $statusClass }}">{{ $registration->bootcamp->status_label }}</span>
    </div>

    {{-- Info grid: tanggal + lokasi --}}
    <div class="info-grid">
        <div class="info-cell">
            <div class="label">Tanggal Mulai</div>
            <div class="value">{{ $registration->bootcamp->start_date->translatedFormat('d F Y') }}</div>
            <div style="font-size:2.5mm;color:#64748B;margin-top:0.5mm;">
                Pukul {{ $registration->bootcamp->start_date->format('H:i') }} WIB
            </div>
        </div>
        <div class="info-cell">
            <div class="label">Lokasi</div>
            <div class="value">{{ $registration->bootcamp->location ?: 'Tatap Muka' }}</div>
        </div>
    </div>

    {{-- Dashed separator --}}
    <div class="dashed-sep"></div>

    {{-- Peserta --}}
    <div class="peserta">
        <div class="peserta-avatar">
            @if($registration->user->avatar)
                <img src="{{ public_path('storage/' . ltrim($registration->user->avatar, '/storage/')) }}"
                     alt="">
            @else
                {{ strtoupper(substr($registration->user->name, 0, 1)) }}
            @endif
        </div>
        <div class="peserta-info">
            <div class="label">Pemegang Tiket</div>
            <div class="name">{{ $registration->user->name }}</div>
            <div class="email">{{ $registration->user->email }}</div>
        </div>
    </div>

    {{-- Dashed separator --}}
    <div class="dashed-sep"></div>

    {{-- Bottom: kode tiket + QR --}}
    <div class="bottom-section">
        <div class="ticket-code-block">
            <div class="label">Kode Tiket</div>
            <div class="ticket-code-value">{{ $registration->ticket_code }}</div>
            <div class="registered-info">
                Terdaftar: <strong>{{ $registration->registered_at?->translatedFormat('d M Y') }}</strong><br>
                Status: <strong>{{ $registration->status_label }}</strong><br>
                @if($registration->checked_in)
                    Check-in: <strong>{{ $registration->checked_in_at?->translatedFormat('d M Y, H:i') }} WIB</strong>
                @else
                    Check-in: <strong>Belum check-in</strong>
                @endif
            </div>
        </div>

        <div class="qr-block">
            <img src="{{ $qrImageUrl }}" alt="QR Code">
            <div class="qr-label">Scan untuk verifikasi</div>
        </div>
    </div>

</div>

{{-- BOTTOM STRIP --}}
<div class="bottom-strip">
    <span>{{ \App\Models\Setting::get('site_name', 'Skolah.com') }} &middot; Platform Edukasi Digital Terlengkap di Indonesia</span>
</div>

</body>
</html>
