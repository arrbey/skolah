<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') — {{ \App\Models\Setting::get('site_name', 'Skolah.com') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #0F172A;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            top: -50%; left: -50%;
            width: 200%; height: 200%;
            background: radial-gradient(circle at 30% 40%, rgba(37, 99, 235, 0.15), transparent 50%),
                        radial-gradient(circle at 70% 60%, rgba(124, 58, 237, 0.1), transparent 50%),
                        radial-gradient(circle at 50% 80%, rgba(56, 189, 248, 0.08), transparent 50%);
            animation: bgFloat 20s ease-in-out infinite;
        }

        @keyframes bgFloat {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(2%, -2%) rotate(1deg); }
            66% { transform: translate(-1%, 1%) rotate(-1deg); }
        }

        .container {
            position: relative;
            z-index: 1;
            text-align: center;
            padding: 2rem;
            max-width: 540px;
            width: 100%;
        }

        .logo {
            display: inline-flex;
            align-items: center;
            gap: 0.625rem;
            margin-bottom: 2.5rem;
            text-decoration: none;
        }
        .logo-icon {
            width: 44px; height: 44px;
            background: linear-gradient(135deg, #2563EB, #7C3AED);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .logo-icon svg { width: 24px; height: 24px; fill: #fff; }
        .logo-text {
            font-size: 1.5rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.025em;
        }

        .error-code {
            font-size: 7rem;
            font-weight: 800;
            background: linear-gradient(135deg, #3B82F6, #7C3AED, #38BDF8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 1rem;
        }

        .icon-wrap {
            margin-bottom: 1.5rem;
        }
        .icon-wrap svg {
            width: 64px; height: 64px;
            stroke: #3B82F6;
            fill: none;
            stroke-width: 1.5;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        h1 {
            font-size: 1.5rem;
            font-weight: 800;
            color: #F1F5F9;
            margin-bottom: 0.75rem;
            letter-spacing: -0.025em;
        }

        .subtitle {
            font-size: 0.95rem;
            color: #94A3B8;
            line-height: 1.7;
            margin-bottom: 2rem;
        }

        .actions {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 2.5rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            border-radius: 10px;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2563EB, #7C3AED);
            color: #fff;
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 20px rgba(37, 99, 235, 0.4);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #94A3B8;
        }
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #F1F5F9;
        }

        .footer-links {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1.5rem;
            flex-wrap: wrap;
        }
        .footer-links a {
            font-size: 0.8rem;
            color: #64748B;
            text-decoration: none;
            transition: color 0.2s;
        }
        .footer-links a:hover { color: #3B82F6; }
        .footer-sep {
            width: 3px; height: 3px;
            border-radius: 50%;
            background: #334155;
        }

        .particles {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            pointer-events: none;
            overflow: hidden;
            z-index: 0;
        }
        .particle {
            position: absolute;
            width: 4px; height: 4px;
            border-radius: 50%;
            background: rgba(59, 130, 246, 0.3);
            animation: float linear infinite;
        }
        .particle:nth-child(1) { left:10%; animation-duration:12s; animation-delay:0s; width:3px; height:3px; }
        .particle:nth-child(2) { left:25%; animation-duration:15s; animation-delay:2s; width:5px; height:5px; background:rgba(124,58,237,0.2); }
        .particle:nth-child(3) { left:40%; animation-duration:10s; animation-delay:4s; width:3px; height:3px; }
        .particle:nth-child(4) { left:55%; animation-duration:18s; animation-delay:1s; width:4px; height:4px; background:rgba(56,189,248,0.2); }
        .particle:nth-child(5) { left:70%; animation-duration:14s; animation-delay:3s; width:3px; height:3px; }
        .particle:nth-child(6) { left:85%; animation-duration:11s; animation-delay:5s; width:5px; height:5px; background:rgba(124,58,237,0.15); }

        @keyframes float {
            0%   { transform: translateY(100vh) scale(0); opacity:0; }
            10%  { opacity:1; transform: translateY(90vh) scale(1); }
            90%  { opacity:1; }
            100% { transform: translateY(-10vh) scale(0); opacity:0; }
        }
    </style>
</head>
<body>
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <div class="container">
        <a href="{{ url('/') }}" class="logo">
            <div class="logo-icon">
                <svg viewBox="0 0 24 24"><path d="M12 3L1 9l4 2.18v6L12 21l7-3.82v-6l2-1.09V17h2V9L12 3zm6.82 6L12 12.72 5.18 9 12 5.28 18.82 9zM17 15.99l-5 2.73-5-2.73v-3.72L12 15l5-2.73v3.72z"/></svg>
            </div>
            <span class="logo-text">{{ \App\Models\Setting::get('site_name', 'Skolah.com') }}</span>
        </a>

        @yield('content')

        <div class="footer-links">
            <a href="{{ url('/') }}">🏠 Beranda</a>
            <div class="footer-sep"></div>
            <a href="mailto:{{\App\Models\Setting::get('site_email', 'admin@skolah.com')}}">📧 {{\App\Models\Setting::get('site_email', 'admin@skolah.com')}}</a>
            <div class="footer-sep"></div>
            <a href="{{ url('/contact') }}">💬 Hubungi Kami</a>
        </div>
    </div>
</body>
</html>
