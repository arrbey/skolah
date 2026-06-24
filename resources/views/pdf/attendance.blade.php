<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    @page {
        size: A4 portrait;
        margin: 15mm 12mm 15mm 12mm;
    }

    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 10px;
        color: #1E293B;
        line-height: 1.5;
    }

    /* ── HEADER ───────────────────────────────── */
    .header {
        text-align: center;
        padding-bottom: 6mm;
        border-bottom: 1px solid #CBD5E1;
        margin-bottom: 5mm;
    }

    .header h1 {
        font-size: 16px;
        font-weight: bold;
        color: #0F172A;
        margin-bottom: 2px;
    }

    .header h2 {
        font-size: 13px;
        font-weight: bold;
        color: #2563EB;
        margin-bottom: 6px;
    }

    .header .brand {
        font-size: 9px;
        color: #94A3B8;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    /* ── INFO ROW ─────────────────────────────── */
    .info-row {
        width: 100%;
        margin-bottom: 5mm;
    }

    .info-row table {
        width: 100%;
        border-collapse: collapse;
    }

    .info-row td {
        padding: 2px 0;
        font-size: 10px;
        vertical-align: top;
    }

    .info-label {
        color: #64748B;
        width: 25%;
    }

    .info-value {
        color: #0F172A;
        font-weight: bold;
    }

    /* ── STATS ────────────────────────────────── */
    .stats {
        width: 100%;
        margin-bottom: 5mm;
        border-collapse: collapse;
    }

    .stats td {
        text-align: center;
        padding: 4mm 3mm;
        border: 1px solid #E2E8F0;
        background: #F8FAFC;
    }

    .stats .stat-value {
        font-size: 18px;
        font-weight: bold;
        display: block;
    }

    .stats .stat-label {
        font-size: 8px;
        color: #64748B;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-blue { color: #2563EB; }
    .stat-green { color: #10B981; }
    .stat-red { color: #EF4444; }
    .stat-purple { color: #7C3AED; }

    /* ── TABLE ────────────────────────────────── */
    .attendance-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 5mm;
    }

    .attendance-table thead th {
        background: #2563EB;
        color: #FFFFFF;
        font-size: 9px;
        font-weight: bold;
        padding: 3mm 2mm;
        text-align: left;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .attendance-table thead th:first-child {
        width: 8%;
        text-align: center;
    }

    .attendance-table thead th:nth-child(4) {
        text-align: center;
        width: 14%;
    }

    .attendance-table thead th:nth-child(5) {
        text-align: center;
        width: 10%;
    }

    .attendance-table tbody td {
        padding: 2.5mm 2mm;
        font-size: 9px;
        border-bottom: 1px solid #E2E8F0;
        vertical-align: middle;
    }

    .attendance-table tbody tr:nth-child(even) {
        background: #F8FAFC;
    }

    .attendance-table tbody td:first-child {
        text-align: center;
        color: #64748B;
    }

    .attendance-table tbody td:nth-child(4) {
        text-align: center;
    }

    .attendance-table tbody td:nth-child(5) {
        text-align: center;
    }

    .name-cell {
        font-weight: bold;
        color: #0F172A;
    }

    .email-cell {
        font-size: 8px;
        color: #94A3B8;
    }

    .ticket-code {
        font-family: DejaVu Sans Mono, monospace;
        font-size: 8px;
        color: #475569;
        background: #F1F5F9;
        padding: 1px 4px;
        border-radius: 2px;
    }

    .badge-hadir {
        display: inline-block;
        background: #DCFCE7;
        color: #15803D;
        font-size: 8px;
        font-weight: bold;
        padding: 1px 6px;
        border-radius: 8px;
    }

    .badge-belum {
        display: inline-block;
        background: #F1F5F9;
        color: #94A3B8;
        font-size: 8px;
        font-weight: bold;
        padding: 1px 6px;
        border-radius: 8px;
    }

    .time-hadir {
        font-weight: bold;
        color: #15803D;
        font-size: 9px;
    }

    .time-empty {
        color: #CBD5E1;
    }

    /* ── FOOTER ───────────────────────────────── */
    .footer {
        margin-top: 5mm;
        padding-top: 3mm;
        border-top: 1px solid #E2E8F0;
        text-align: center;
        font-size: 8px;
        color: #94A3B8;
    }

    .footer .brand-footer {
        font-weight: bold;
        color: #2563EB;
    }
</style>
</head>
<body>

    {{-- Header --}}
    <div class="header">
        <p class="brand">SKOLAH.COM</p>
        <h1>Daftar Hadir Peserta</h1>
        <h2>{{ $bootcamp->title }}</h2>
    </div>

    {{-- Info Row --}}
    <div class="info-row">
        <table>
            <tr>
                <td class="info-label">Tanggal Event</td>
                <td class="info-value">: {{ $bootcamp->start_date->translatedFormat('d F Y') }}</td>
                <td class="info-label" style="padding-left: 10mm;">Lokasi</td>
                <td class="info-value">: {{ $bootcamp->location ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Waktu</td>
                <td class="info-value">: {{ $bootcamp->start_date->format('H:i') }} - {{ $bootcamp->end_date ? $bootcamp->end_date->format('H:i') : 'Selesai' }}</td>
                <td class="info-label" style="padding-left: 10mm;">Dicetak</td>
                <td class="info-value">: {{ now()->translatedFormat('d F Y H:i') }}</td>
            </tr>
        </table>
    </div>

    {{-- Stats --}}
    <table class="stats">
        <tr>
            <td>
                <span class="stat-value stat-blue">{{ $totalCount }}</span>
                <span class="stat-label">Total Peserta</span>
            </td>
            <td>
                <span class="stat-value stat-green">{{ $checkedInCount }}</span>
                <span class="stat-label">Hadir</span>
            </td>
            <td>
                <span class="stat-value stat-red">{{ $totalCount - $checkedInCount }}</span>
                <span class="stat-label">Belum Hadir</span>
            </td>
            <td>
                <span class="stat-value stat-purple">{{ $totalCount > 0 ? round(($checkedInCount / $totalCount) * 100) : 0 }}%</span>
                <span class="stat-label">Persentase</span>
            </td>
        </tr>
    </table>

    {{-- Attendance Table --}}
    <table class="attendance-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Peserta</th>
                <th>Kode Tiket</th>
                <th>Kehadiran</th>
                <th>Jam</th>
            </tr>
        </thead>
        <tbody>
            @foreach($registrations as $idx => $reg)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td>
                        <span class="name-cell">{{ $reg->user->name }}</span><br>
                        <span class="email-cell">{{ $reg->user->email }}</span>
                    </td>
                    <td><span class="ticket-code">{{ $reg->ticket_code }}</span></td>
                    <td>
                        @if($reg->checked_in)
                            <span class="badge-hadir">✓ Hadir</span>
                        @else
                            <span class="badge-belum">— Belum</span>
                        @endif
                    </td>
                    <td>
                        @if($reg->checked_in)
                            <span class="time-hadir">{{ $reg->checked_in_at->format('H:i') }}</span>
                        @else
                            <span class="time-empty">—</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Footer --}}
    <div class="footer">
        <p>Dokumen ini digenerate otomatis oleh <span class="brand-footer">{{ \App\Models\Setting::get('site_name', 'Skolah.com') }}</span></p>
        <p>{{ now()->translatedFormat('d F Y H:i') }} WIB</p>
    </div>

</body>
</html>
