<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Berhasil — {{ \App\Models\Setting::get('site_name', 'Skolah.com') }}</title>
    <style>
        body { margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #F8FAFC; color: #1E293B; }
        .container { max-width: 600px; margin: 0 auto; background: #FFFFFF; }
        .header { background: linear-gradient(135deg, #2563EB, #7C3AED); padding: 32px 24px; text-align: center; }
        .header h1 { color: #FFFFFF; font-size: 24px; margin: 0; font-weight: 700; }
        .header p { color: rgba(255,255,255,0.85); font-size: 14px; margin: 8px 0 0; }
        .content { padding: 32px 24px; }
        .success-badge { display: inline-block; background: #ECFDF5; color: #059669; font-size: 14px; font-weight: 600; padding: 8px 16px; border-radius: 50px; margin-bottom: 16px; }
        .info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #F1F5F9; font-size: 14px; }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: #64748B; }
        .info-value { color: #0F172A; font-weight: 600; }
        .items-table { width: 100%; border-collapse: collapse; margin: 16px 0; }
        .items-table th { text-align: left; font-size: 12px; text-transform: uppercase; color: #64748B; font-weight: 600; padding: 8px 0; border-bottom: 2px solid #E2E8F0; }
        .items-table td { padding: 12px 0; font-size: 14px; border-bottom: 1px solid #F1F5F9; vertical-align: top; }
        .items-table .item-name { color: #0F172A; font-weight: 500; }
        .items-table .item-type { color: #64748B; font-size: 12px; }
        .items-table .item-price { text-align: right; color: #0F172A; font-weight: 600; }
        .total-section { background: #F8FAFC; border-radius: 12px; padding: 16px; margin: 24px 0; }
        .total-row { display: flex; justify-content: space-between; padding: 4px 0; font-size: 14px; }
        .total-row.grand { font-size: 18px; font-weight: 700; color: #2563EB; padding-top: 8px; border-top: 2px solid #E2E8F0; margin-top: 8px; }
        .cta-button { display: inline-block; background: #2563EB; color: #FFFFFF; text-decoration: none; padding: 14px 32px; border-radius: 12px; font-size: 14px; font-weight: 600; margin: 16px 0; }
        .footer { background: #F8FAFC; padding: 24px; text-align: center; font-size: 12px; color: #94A3B8; border-top: 1px solid #E2E8F0; }
        .footer a { color: #2563EB; text-decoration: none; }

        /* Responsive table fallback */
        table { width: 100%; }
    </style>
</head>
<body>
    <div class="container">
        {{-- Header --}}
        <div class="header">
            <h1>{{ \App\Models\Setting::get('site_name', 'Skolah.com') }}</h1>
            <p>Platform Edukasi Digital Terlengkap di Indonesia</p>
        </div>

        {{-- Content --}}
        <div class="content">
            <div style="text-align: center; margin-bottom: 24px;">
                <div class="success-badge">✅ Pembayaran Berhasil</div>
                <h2 style="font-size: 20px; color: #0F172A; margin: 12px 0 4px;">
                    Halo, {{ $user->name }}!
                </h2>
                <p style="color: #64748B; font-size: 14px; margin: 0;">
                    Pembayaranmu telah kami terima dan diproses.
                </p>
            </div>

            {{-- Info Order --}}
            <div style="background: #F8FAFC; border-radius: 12px; padding: 16px; margin-bottom: 24px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 6px 0; font-size: 14px; color: #64748B;">No. Order</td>
                        <td style="padding: 6px 0; font-size: 14px; color: #0F172A; font-weight: 600; text-align: right;">{{ $order->order_number }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; font-size: 14px; color: #64748B;">Tanggal Bayar</td>
                        <td style="padding: 6px 0; font-size: 14px; color: #0F172A; font-weight: 600; text-align: right;">{{ $order->paid_at_formatted ?? tanggal_waktu_indo(now()) }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; font-size: 14px; color: #64748B;">Metode</td>
                        <td style="padding: 6px 0; font-size: 14px; color: #0F172A; font-weight: 600; text-align: right;">Midtrans</td>
                    </tr>
                </table>
            </div>

            {{-- Item Table --}}
            <h3 style="font-size: 16px; font-weight: 600; color: #0F172A; margin-bottom: 8px;">Detail Pesanan</h3>

            <table class="items-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th style="text-align: center;">Qty</th>
                        <th style="text-align: right;">Harga</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>
                        <td>
                            <div class="item-name">{{ $item->item_name }}</div>
                            <div class="item-type">{{ $item->item_type_label }}</div>
                        </td>
                        <td style="text-align: center;">{{ $item->quantity }}</td>
                        <td class="item-price">{{ $item->subtotal_formatted }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Total --}}
            <div class="total-section">
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
                        <td style="padding: 12px 0 4px; font-size: 18px; font-weight: 700; color: #2563EB; border-top: 2px solid #E2E8F0;">Total</td>
                        <td style="padding: 12px 0 4px; font-size: 18px; font-weight: 700; color: #2563EB; text-align: right; border-top: 2px solid #E2E8F0;">{{ $order->total_formatted }}</td>
                    </tr>
                </table>
            </div>

            {{-- CTA --}}
            <div style="text-align: center; margin: 24px 0;">
                <a href="{{ url('/dashboard') }}" class="cta-button">
                    Mulai Belajar Sekarang →
                </a>
            </div>

            <p style="font-size: 13px; color: #94A3B8; text-align: center; line-height: 1.6;">
                Jika kamu memiliki pertanyaan, silakan hubungi tim support kami
                melalui email <a href="mailto:{{\App\Models\Setting::get('site_email', 'support@skolah.com')}}" style="color: #2563EB;">{{\App\Models\Setting::get('site_email', 'support@skolah.com')}}</a>
            </p>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <p>&copy; {{ date('Y') }} Skolah.com — Platform Edukasi Digital Terlengkap di Indonesia</p>
            <p>Email ini dikirim otomatis, mohon tidak membalas email ini.</p>
        </div>
    </div>
</body>
</html>
