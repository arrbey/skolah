<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sedang Maintenance — {{ \App\Models\Setting::get('site_name', 'Skolah.com') }}</title>
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

        /* Animated background gradient */
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

        /* Logo */
        .logo {
            display: inline-flex;
            align-items: center;
            gap: 0.625rem;
            margin-bottom: 3rem;
        }
        .logo-icon {
            width: 44px; height: 44px;
            background: linear-gradient(135deg, #2563EB, #7C3AED);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .logo-icon svg {
            width: 24px; height: 24px;
            fill: #fff;
        }
        .logo-text {
            font-size: 1.5rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.025em;
        }

        /* Animated gear icon */
        .gear-wrap {
            margin-bottom: 2rem;
        }
        .gear-icon {
            width: 80px; height: 80px;
            margin: 0 auto;
            animation: gearSpin 8s linear infinite;
        }
        .gear-icon svg {
            width: 100%; height: 100%;
            stroke: #3B82F6;
            fill: none;
        }

        @keyframes gearSpin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Text */
        h1 {
            font-size: 1.75rem;
            font-weight: 800;
            color: #F1F5F9;
            margin-bottom: 0.75rem;
            letter-spacing: -0.025em;
        }
        .subtitle {
            font-size: 1rem;
            color: #94A3B8;
            line-height: 1.7;
            margin-bottom: 2.5rem;
        }

        /* Status badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1.25rem;
            background: rgba(37, 99, 235, 0.1);
            border: 1px solid rgba(37, 99, 235, 0.25);
            border-radius: 999px;
            margin-bottom: 2.5rem;
        }
        .pulse-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: #F59E0B;
            animation: pulse 2s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.8); }
        }
        .status-text {
            font-size: 0.8rem;
            font-weight: 600;
            color: #60A5FA;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Progress bar */
        .progress-bar {
            width: 100%;
            max-width: 280px;
            height: 4px;
            background: rgba(255,255,255,0.08);
            border-radius: 99px;
            margin: 0 auto 2.5rem;
            overflow: hidden;
        }
        .progress-bar-inner {
            width: 30%;
            height: 100%;
            background: linear-gradient(90deg, #2563EB, #7C3AED);
            border-radius: 99px;
            animation: progressSlide 2s ease-in-out infinite;
        }
        @keyframes progressSlide {
            0% { width: 10%; margin-left: 0; }
            50% { width: 50%; margin-left: 25%; }
            100% { width: 10%; margin-left: 90%; }
        }

        /* Footer links */
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
        .footer-links a:hover {
            color: #3B82F6;
        }
        .footer-sep {
            width: 3px; height: 3px;
            border-radius: 50%;
            background: #334155;
        }

        /* Floating particles */
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
        .particle:nth-child(1)  { left:10%;  animation-duration:12s; animation-delay:0s;   width:3px; height:3px; }
        .particle:nth-child(2)  { left:25%;  animation-duration:15s; animation-delay:2s;   width:5px; height:5px; background:rgba(124,58,237,0.2); }
        .particle:nth-child(3)  { left:40%;  animation-duration:10s; animation-delay:4s;   width:3px; height:3px; }
        .particle:nth-child(4)  { left:55%;  animation-duration:18s; animation-delay:1s;   width:4px; height:4px; background:rgba(56,189,248,0.2); }
        .particle:nth-child(5)  { left:70%;  animation-duration:14s; animation-delay:3s;   width:3px; height:3px; }
        .particle:nth-child(6)  { left:85%;  animation-duration:11s; animation-delay:5s;   width:5px; height:5px; background:rgba(124,58,237,0.15); }
        .particle:nth-child(7)  { left:15%;  animation-duration:16s; animation-delay:6s;   width:4px; height:4px; }
        .particle:nth-child(8)  { left:60%;  animation-duration:13s; animation-delay:2.5s; width:3px; height:3px; background:rgba(56,189,248,0.15); }

        @keyframes float {
            0%   { transform: translateY(100vh) scale(0); opacity:0; }
            10%  { opacity:1; transform: translateY(90vh) scale(1); }
            90%  { opacity:1; }
            100% { transform: translateY(-10vh) scale(0); opacity:0; }
        }
    </style>
</head>
<body>
    <!-- Floating particles -->
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <div class="container">
        <!-- Logo -->
        <div class="logo">
            <div class="logo-icon">
                <svg viewBox="0 0 24 24"><path d="M12 3L1 9l4 2.18v6L12 21l7-3.82v-6l2-1.09V17h2V9L12 3zm6.82 6L12 12.72 5.18 9 12 5.28 18.82 9zM17 15.99l-5 2.73-5-2.73v-3.72L12 15l5-2.73v3.72z"/></svg>
            </div>
            <span class="logo-text">{{ \App\Models\Setting::get('site_name', 'Skolah.com') }}</span>
        </div>

        <!-- Animated gear -->
        <div class="gear-wrap">
            <div class="gear-icon">
                <svg viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 15a3 3 0 100-6 3 3 0 000 6z"/>
                    <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z"/>
                </svg>
            </div>
        </div>

        <!-- Status badge -->
        <div class="status-badge">
            <div class="pulse-dot"></div>
            <span class="status-text">Sedang Maintenance</span>
        </div>

        <!-- Message -->
        <h1>Kami Sedang Melakukan Pemeliharaan</h1>
        <p class="subtitle">{{ $message ?? 'Sedang dalam pemeliharaan. Kami akan kembali segera!' }}</p>

        <!-- Progress bar -->
        <div class="progress-bar">
            <div class="progress-bar-inner"></div>
        </div>

        <!-- Footer links -->
        <div class="footer-links">
            <a href="mailto:{{\App\Models\Setting::get('site_email', 'admin@skolah.com')}}">📧 {{\App\Models\Setting::get('site_email', 'admin@skolah.com')}}</a>
            <div class="footer-sep"></div>
            <a href="https://wa.me/6281234567890" target="_blank">💬 WhatsApp</a>
            <div class="footer-sep"></div>
            <a href="{{ url('/admin') }}">🔐 Admin</a>
        </div>
    </div>
</body>
</html>
