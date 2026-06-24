@extends('emails.layouts.base')

@section('title', 'Status Pengajuan Instruktur' . ' — ' . \App\Models\Setting::get('site_name', '' . \App\Models\Setting::get('site_name', 'Skolah.com') . ''))

@section('content')
<div style="text-align: center; margin-bottom: 24px;">
    @if($isApproved)
        <div style="font-size: 48px; margin-bottom: 8px;">🎉</div>
        <div class="badge badge-success">✅ Pengajuan Disetujui</div>
    @else
        <div style="font-size: 48px; margin-bottom: 8px;">📋</div>
        <div class="badge badge-danger">❌ Pengajuan Ditolak</div>
    @endif

    <h2 style="font-size: 22px; color: #0F172A; margin: 12px 0 4px;">
        Halo, {{ $user->name }}!
    </h2>

    @if($isApproved)
        <p style="color: #64748B; font-size: 14px; margin: 0; line-height: 1.6;">
            Selamat! Pengajuan kamu sebagai instruktur di {{ \App\Models\Setting::get('site_name', 'Skolah.com') }} telah <strong style="color: #059669;">disetujui</strong>.
        </p>
    @else
        <p style="color: #64748B; font-size: 14px; margin: 0; line-height: 1.6;">
            Pengajuan kamu sebagai instruktur di {{ \App\Models\Setting::get('site_name', 'Skolah.com') }} belum dapat kami setujui saat ini.
        </p>
    @endif
</div>

<hr class="divider">

{{-- Detail Pengajuan --}}
<div class="info-box">
    <table>
        <tr>
            <td class="info-label">Bidang Keahlian</td>
            <td class="info-value">{{ $application->expertise }}</td>
        </tr>
        <tr>
            <td class="info-label">Tanggal Pengajuan</td>
            <td class="info-value">{{ $application->created_at->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td class="info-label">Tanggal Review</td>
            <td class="info-value">{{ $application->reviewed_at ? $application->reviewed_at->translatedFormat('d F Y') : '-' }}</td>
        </tr>
    </table>
</div>

@if($application->admin_notes)
<div style="background: {{ $isApproved ? '#ECFDF5' : '#FEF2F2' }}; border-radius: 12px; padding: 16px; margin: 16px 0;">
    <p style="font-size: 13px; color: #64748B; margin: 0 0 4px; font-weight: 600;">
        📝 Catatan dari Admin:
    </p>
    <p style="font-size: 14px; color: #0F172A; margin: 0; line-height: 1.6;">
        {{ $application->admin_notes }}
    </p>
</div>
@endif

@if($isApproved)
    {{-- Next Steps untuk Approved --}}
    <div style="background: #EFF6FF; border-radius: 12px; padding: 16px; margin: 16px 0;">
        <p style="font-size: 14px; color: #1D4ED8; margin: 0 0 8px; font-weight: 600;">
            🚀 Langkah Selanjutnya:
        </p>
        <ol style="font-size: 13px; color: #1E293B; margin: 0; padding-left: 20px; line-height: 1.8;">
            <li>Login ke akun {{ \App\Models\Setting::get('site_name', 'Skolah.com') }} kamu</li>
            <li>Akses <strong>Instructor Dashboard</strong> yang sudah aktif</li>
            <li>Buat kursus pertamamu dan upload materi video</li>
            <li>Publish kursus dan mulai mendapat penghasilan!</li>
        </ol>
    </div>

    <div style="text-align: center; margin: 24px 0;">
        <a href="{{ url('/instructor/dashboard') }}" class="cta-button cta-button-success">
            🎓 Buka Instructor Dashboard →
        </a>
    </div>
@else
    {{-- Encouragement untuk Rejected --}}
    <div style="background: #EFF6FF; border-radius: 12px; padding: 16px; margin: 16px 0;">
        <p style="font-size: 14px; color: #1E293B; margin: 0; line-height: 1.6;">
            Jangan berkecil hati! Kamu dapat mengajukan kembali setelah memperbaiki kekurangan
            yang disebutkan di atas. Kami selalu terbuka untuk instruktur baru yang berkualitas.
        </p>
    </div>

    <div style="text-align: center; margin: 24px 0;">
        <a href="{{ url('/dashboard/become-instructor') }}" class="cta-button">
            📝 Ajukan Kembali →
        </a>
    </div>
@endif
@endsection
