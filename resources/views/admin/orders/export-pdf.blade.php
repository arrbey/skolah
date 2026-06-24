<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Export Pesanan - {{ \App\Models\Setting::get('site_name', 'Skolah.com') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #2563EB; }
        .header h1 { font-size: 18px; color: #2563EB; margin-bottom: 4px; }
        .header p { font-size: 10px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #f8fafc; color: #64748b; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; padding: 8px 6px; border-bottom: 2px solid #e2e8f0; text-align: left; }
        td { padding: 6px; border-bottom: 1px solid #f1f5f9; font-size: 10px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 9px; font-weight: 600; }
        .badge-paid { background: #dcfce7; color: #166534; }
        .badge-pending { background: #fef9c3; color: #854d0e; }
        .badge-failed { background: #fee2e2; color: #991b1b; }
        .badge-refunded { background: #e0e7ff; color: #3730a3; }
        .footer { margin-top: 20px; padding-top: 10px; border-top: 1px solid #e2e8f0; text-align: center; font-size: 9px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ \App\Models\Setting::get('site_name', 'Skolah.com') }} — Laporan Pesanan</h1>
        <p>Diekspor pada: {{ now()->translatedFormat('d F Y H:i') }} WIB &bull; Total: {{ $orders->count() }} pesanan</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Order</th>
                <th>User</th>
                <th class="text-right">Total</th>
                <th class="text-center">Status</th>
                <th>Metode</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $i => $order)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td style="font-family: monospace; font-size: 9px;">{{ $order->order_number }}</td>
                    <td>{{ $order->user?->name ?? '-' }}</td>
                    <td class="text-right" style="font-weight: 600;">{{ rupiah($order->total) }}</td>
                    <td class="text-center">
                        <span class="badge badge-{{ $order->status }}">{{ $order->status_label }}</span>
                    </td>
                    <td>{{ $order->payment_method ?? '-' }}</td>
                    <td>{{ $order->created_at?->format('d/m/Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        &copy; {{ date('Y') }} Skolah.com — Platform Edukasi Digital Terlengkap di Indonesia
    </div>
</body>
</html>
