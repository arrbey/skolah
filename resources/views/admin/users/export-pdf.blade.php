<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Data Pengguna — {{ \App\Models\Setting::get('site_name', 'Skolah.com') }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #333; }
        h1 { font-size: 16px; margin-bottom: 4px; }
        .meta { font-size: 9px; color: #666; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #2563EB; color: #fff; text-align: left; padding: 6px 8px; font-size: 9px; text-transform: uppercase; }
        td { padding: 5px 8px; border-bottom: 1px solid #eee; }
        tr:nth-child(even) td { background: #f9fafb; }
        .badge { display: inline-block; padding: 1px 6px; border-radius: 8px; font-size: 8px; font-weight: bold; }
        .badge-user { background: #DBEAFE; color: #1E40AF; }
        .badge-instructor { background: #EDE9FE; color: #6B21A8; }
        .badge-admin { background: #FEE2E2; color: #991B1B; }
        .footer { text-align: center; margin-top: 16px; font-size: 8px; color: #999; }
    </style>
</head>
<body>
    <h1>Data Pengguna — {{ \App\Models\Setting::get('site_name', 'Skolah.com') }}</h1>
    <p class="meta">Diekspor pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB &middot; Total: {{ $users->count() }} pengguna</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Terdaftar</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $i => $user)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td><span class="badge badge-{{ $user->role }}">{{ ucfirst($user->role) }}</span></td>
                    <td>{{ $user->created_at->format('d/m/Y') }}</td>
                    <td>{{ $user->suspended_at ? 'Suspended' : 'Aktif' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p class="footer">&copy; {{ date('Y') }} Skolah.com — Platform Edukasi Digital Terlengkap di Indonesia</p>
</body>
</html>
