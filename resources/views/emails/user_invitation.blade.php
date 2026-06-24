<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Undangan Skolah.com</title>
    <style>
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; line-height: 1.6; color: #1f2937; margin: 0; padding: 0; background-color: #f9fafb; }
        .container { max-width: 600px; margin: 40px auto; padding: 32px; background: #ffffff; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .header { text-align: center; margin-bottom: 32px; }
        .logo { font-size: 24px; font-weight: 800; color: #4f46e5; text-decoration: none; }
        .content { margin-bottom: 32px; }
        .credentials { background: #f3f4f6; padding: 24px; border-radius: 12px; margin: 24px 0; }
        .credential-item { margin-bottom: 12px; }
        .label { font-size: 12px; font-weight: 600; text-transform: uppercase; color: #6b7280; margin-bottom: 4px; }
        .value { font-size: 16px; font-weight: 500; color: #111827; }
        .btn { display: inline-block; padding: 12px 24px; background-color: #4f46e5; color: #ffffff !important; text-decoration: none; border-radius: 8px; font-weight: 600; text-align: center; }
        .footer { text-align: center; font-size: 14px; color: #6b7280; margin-top: 32px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="{{ config('app.url') }}" class="logo">Skolah.com</a>
        </div>
        <div class="content">
            <h1 style="font-size: 24px; font-weight: 700; color: #111827; margin-bottom: 16px;">Selamat Datang, {{ $user->name }}!</h1>
            <p>Anda telah diundang oleh Administrator untuk bergabung sebagai <strong>{{ ucfirst($user->role) }}</strong> di platform Skolah.com.</p>
            <p>Gunakan kredensial berikut untuk masuk ke dashboard Anda:</p>
            
            <div class="credentials">
                <div class="credential-item">
                    <div class="label">Email</div>
                    <div class="value">{{ $user->email }}</div>
                </div>
                <div class="credential-item" style="margin-bottom: 0;">
                    <div class="label">Password Sementara</div>
                    <div class="value" style="font-family: monospace; letter-spacing: 1px;">{{ $password }}</div>
                </div>
            </div>

            <p style="color: #6b7280; font-size: 14px; margin-bottom: 24px;">Demi keamanan akun Anda, Anda akan diminta untuk mengubah password ini segera setelah berhasil login pertama kali.</p>

            <div style="text-align: center;">
                <a href="{{ route('login') }}" class="btn">Login Sekarang</a>
            </div>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Skolah.com. Semua hak dilindungi.</p>
        </div>
    </div>
</body>
</html>
