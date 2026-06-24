<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', \App\Models\Setting::get('site_name', 'Skolah.com'))</title>
    <style>
        /* ── Reset ────────────────────────────────────────────────────────── */
        body { margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #F8FAFC; color: #1E293B; -webkit-font-smoothing: antialiased; }
        img { border: 0; max-width: 100%; }
        a { color: #2563EB; text-decoration: none; }

        /* ── Container ────────────────────────────────────────────────────── */
        .email-wrapper { width: 100%; background-color: #F8FAFC; padding: 32px 0; }
        .email-container { max-width: 600px; margin: 0 auto; background: #FFFFFF; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.06); }

        /* ── Header ───────────────────────────────────────────────────────── */
        .email-header { background: linear-gradient(135deg, #2563EB, #7C3AED); padding: 32px 24px; text-align: center; }
        .email-header h1 { color: #FFFFFF; font-size: 24px; margin: 0; font-weight: 700; letter-spacing: 0.5px; }
        .email-header p { color: rgba(255,255,255,0.85); font-size: 14px; margin: 8px 0 0; }

        /* ── Content ──────────────────────────────────────────────────────── */
        .email-content { padding: 32px 24px; }

        /* ── Badge ────────────────────────────────────────────────────────── */
        .badge { display: inline-block; font-size: 14px; font-weight: 600; padding: 8px 16px; border-radius: 50px; margin-bottom: 16px; }
        .badge-success { background: #ECFDF5; color: #059669; }
        .badge-info { background: #EFF6FF; color: #2563EB; }
        .badge-warning { background: #FFFBEB; color: #D97706; }
        .badge-danger { background: #FEF2F2; color: #DC2626; }

        /* ── Info Box ─────────────────────────────────────────────────────── */
        .info-box { background: #F8FAFC; border-radius: 12px; padding: 16px; margin: 16px 0; }
        .info-box table { width: 100%; border-collapse: collapse; }
        .info-box td { padding: 6px 0; font-size: 14px; }
        .info-label { color: #64748B; }
        .info-value { color: #0F172A; font-weight: 600; text-align: right; }

        /* ── CTA Button ───────────────────────────────────────────────────── */
        .cta-button { display: inline-block; background: #2563EB; color: #FFFFFF !important; text-decoration: none; padding: 14px 32px; border-radius: 12px; font-size: 14px; font-weight: 600; margin: 16px 0; }
        .cta-button:hover { background: #1D4ED8; }
        .cta-button-success { background: #059669; }
        .cta-button-success:hover { background: #047857; }

        /* ── Divider ──────────────────────────────────────────────────────── */
        .divider { border: none; border-top: 1px solid #E2E8F0; margin: 24px 0; }

        /* ── Footer ───────────────────────────────────────────────────────── */
        .email-footer { background: #F8FAFC; padding: 24px; text-align: center; font-size: 12px; color: #94A3B8; border-top: 1px solid #E2E8F0; }
        .email-footer a { color: #2563EB; text-decoration: none; }

        /* ── Helper ───────────────────────────────────────────────────────── */
        .text-center { text-align: center; }
        .text-muted { color: #94A3B8; }
        .text-small { font-size: 13px; }
        .mt-0 { margin-top: 0; }
        .mb-0 { margin-bottom: 0; }
    </style>
    @yield('extra-styles')
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            {{-- ── Header ──────────────────────────────────────────────────── --}}
            <div class="email-header">
                <h1>{{ \App\Models\Setting::get('site_name', 'Skolah.com') }}</h1>
                <p>Platform Edukasi Digital Terlengkap di Indonesia</p>
            </div>

            {{-- ── Content ─────────────────────────────────────────────────── --}}
            <div class="email-content">
                @yield('content')

                {{-- Support note --}}
                @hasSection('hide-support')
                @else
                <p style="font-size: 13px; color: #94A3B8; text-align: center; line-height: 1.6; margin-top: 24px;">
                    Jika kamu memiliki pertanyaan, silakan hubungi tim support kami
                    melalui email <a href="mailto:{{\App\Models\Setting::get('site_email', 'support@skolah.com')}}" style="color: #2563EB;">{{\App\Models\Setting::get('site_email', 'support@skolah.com')}}</a>
                </p>
                @endif
            </div>

            {{-- ── Footer ──────────────────────────────────────────────────── --}}
            <div class="email-footer">
                <p>&copy; {{ date('Y') }} Skolah.com — Platform Edukasi Digital Terlengkap di Indonesia</p>
                <p>Email ini dikirim otomatis, mohon tidak membalas email ini.</p>
            </div>
        </div>
    </div>
</body>
</html>
