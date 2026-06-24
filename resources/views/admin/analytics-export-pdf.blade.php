<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Export Analytics - {{ \App\Models\Setting::get('site_name', 'Skolah.com') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #2563EB; }
        .header h1 { font-size: 18px; color: #2563EB; margin-bottom: 4px; }
        .header p { font-size: 10px; color: #666; }
        .summary { display: table; width: 100%; margin-bottom: 20px; }
        .summary-item { display: table-cell; text-align: center; padding: 10px; background: #f8fafc; border: 1px solid #e2e8f0; }
        .summary-item .label { font-size: 9px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
        .summary-item .value { font-size: 16px; font-weight: bold; color: #1e293b; margin-top: 4px; }
        h3 { font-size: 13px; color: #1e293b; margin: 15px 0 8px 0; padding-bottom: 5px; border-bottom: 1px solid #e2e8f0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th { background: #f8fafc; color: #64748b; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; padding: 6px; border-bottom: 2px solid #e2e8f0; text-align: left; }
        td { padding: 5px 6px; border-bottom: 1px solid #f1f5f9; font-size: 10px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .footer { margin-top: 20px; padding-top: 10px; border-top: 1px solid #e2e8f0; text-align: center; font-size: 9px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ \App\Models\Setting::get('site_name', 'Skolah.com') }} — Laporan Analytics</h1>
        <p>Periode: {{ $period }} hari terakhir &bull; Diekspor pada: {{ now()->translatedFormat('d F Y H:i') }} WIB</p>
    </div>

    {{-- Summary --}}
    <div class="summary">
        <div class="summary-item">
            <div class="label">Total Revenue</div>
            <div class="value">{{ rupiah($summary['total_revenue'] ?? 0) }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Total Transaksi</div>
            <div class="value">{{ $summary['total_orders'] ?? 0 }}</div>
        </div>
        <div class="summary-item">
            <div class="label">User Baru</div>
            <div class="value">{{ $summary['new_users'] ?? 0 }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Rata-rata Order</div>
            <div class="value">{{ rupiah($summary['avg_order'] ?? 0) }}</div>
        </div>
    </div>

    {{-- Revenue by Type --}}
    <h3>Revenue per Tipe Produk</h3>
    <table>
        <thead>
            <tr>
                <th>Tipe Produk</th>
                <th class="text-right">Total Revenue</th>
            </tr>
        </thead>
        <tbody>
            @foreach($revenueByType as $type)
                @php
                    $typeName = match(class_basename($type->itemable_type ?? '')) {
                        'Course' => 'Kursus',
                        'Bootcamp' => 'Bootcamp',
                        'Book' => 'Buku',
                        'MembershipPlan' => 'Membership',
                        default => $type->itemable_type ?? 'Lainnya',
                    };
                @endphp
                <tr>
                    <td>{{ $typeName }}</td>
                    <td class="text-right" style="font-weight: 600;">{{ rupiah($type->total ?? 0) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Top Courses --}}
    <h3>Top 10 Kursus</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kursus</th>
                <th class="text-center">Siswa</th>
                <th class="text-center">Rating</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topCourses as $i => $course)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $course->title }}</td>
                    <td class="text-center">{{ $course->total_students }}</td>
                    <td class="text-center">{{ number_format($course->rating, 1) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Revenue Chart Data --}}
    <h3>Data Pendapatan Harian</h3>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th class="text-right">Revenue</th>
            </tr>
        </thead>
        <tbody>
            @foreach($revenueChart as $row)
                <tr>
                    <td>{{ $row->date }}</td>
                    <td class="text-right">{{ rupiah($row->total) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        &copy; {{ date('Y') }} Skolah.com — Platform Edukasi Digital Terlengkap di Indonesia
    </div>
</body>
</html>
