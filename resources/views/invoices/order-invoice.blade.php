<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice #{{ $order->reference }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 14px; color: #333; margin: 0; padding: 0; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; line-height: 24px; }
        
        .header { display: table; width: 100%; margin-bottom: 40px; border-bottom: 2px solid #f1f5f9; padding-bottom: 20px; }
        .header .logo { display: table-cell; vertical-align: middle; }
        .header .logo h1 { color: #2563eb; margin: 0; font-size: 28px; font-weight: bold; }
        .header .info { display: table-cell; text-align: right; vertical-align: middle; }
        
        .details-table { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        .details-table td { vertical-align: top; padding: 10px 0; }
        .details-table .label { color: #64748b; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: bold; }
        .details-table .value { color: #1e293b; font-weight: bold; font-size: 15px; }

        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .items-table th { background: #f8fafc; text-align: left; padding: 12px 15px; color: #64748b; font-size: 12px; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; }
        .items-table td { padding: 15px; border-bottom: 1px solid #f1f5f9; }
        .items-table .total-row td { border-bottom: none; padding-top: 20px; }
        
        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 50px; font-size: 12px; font-weight: bold; text-transform: uppercase; }
        .status-paid { background: #dcfce7; color: #166534; }
        .status-pending { background: #fef9c3; color: #854d0e; }
        
        .footer { margin-top: 50px; text-align: center; color: #94a3b8; font-size: 12px; border-top: 1px solid #f1f5f9; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="invoice-box">
        {{-- Header --}}
        <div class="header">
            <div class="logo">
                <h1>Skolah.com</h1>
            </div>
            <div class="info">
                <div style="font-size: 20px; font-weight: bold; color: #1e293b; margin-bottom: 5px;">INVOICE</div>
                <div style="color: #64748b;">#{{ $order->reference }}</div>
            </div>
        </div>

        {{-- Info Pembeli & Order --}}
        <table class="details-table">
            <tr>
                <td width="50%">
                    <div class="label">Diterbitkan Untuk:</div>
                    <div class="value">{{ $order->user->name }}</div>
                    <div style="color: #64748b; margin-top: 5px;">{{ $order->user->email }}</div>
                </td>
                <td width="50%" style="text-align: right;">
                    <div class="label">Tanggal Pembelian:</div>
                    <div class="value">{{ $order->created_at->format('d F Y') }}</div>
                    <div class="label" style="margin-top: 15px;">Status:</div>
                    <div class="status-badge {{ $order->status === 'paid' ? 'status-paid' : 'status-pending' }}">
                        {{ $order->status === 'paid' ? 'LUNAS' : 'PENDING' }}
                    </div>
                </td>
            </tr>
        </table>

        {{-- Tabel Produk --}}
        <table class="items-table">
            <thead>
                <tr>
                    <th>Item / Produk</th>
                    <th width="120" style="text-align: right;">Harga</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>
                        <div style="font-weight: bold; color: #1e293b;">{{ $item->product_name }}</div>
                        <div style="color: #64748b; font-size: 12px; margin-top: 3px;">Tipe: {{ ucfirst($item->product_type) }}</div>
                    </td>
                    <td style="text-align: right; font-weight: bold;">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                </tr>
                @endforeach
                
                {{-- Total --}}
                <tr class="total-row">
                    <td style="text-align: right; color: #64748b; font-weight: bold;">TOTAL PEMBAYARAN</td>
                    <td style="text-align: right; font-size: 18px; font-weight: bold; color: #2563eb;">
                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- Catatan --}}
        <div style="margin-top: 40px; padding: 20px; background: #f8fafc; border-radius: 12px;">
            <div class="label" style="margin-bottom: 5px;">Metode Pembayaran:</div>
            <div class="value" style="font-size: 13px;">{{ strtoupper($order->payment_method ?? 'Transfer Bank / Midtrans') }}</div>
            <div style="color: #64748b; font-size: 12px; margin-top: 10px; line-height: 1.5;">
                * Invoice ini adalah bukti pembayaran yang sah dan diterbitkan secara elektronik oleh Skolah.com. Kamu bisa mengakses kembali kursus kamu melalui dashboard.
            </div>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <strong>Skolah.com — Platform Edukasi Digital Terlengkap</strong><br>
            Jl. Pendidikan No. 123, Jakarta Selatan, Indonesia • support@skolah.com
        </div>
    </div>
</body>
</html>
