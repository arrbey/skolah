@extends('errors.layout')

@section('title', '419 — Sesi Kedaluwarsa')

@section('content')
    <div class="icon-wrap">
        {{-- Clock icon --}}
        <svg viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10"/>
            <polyline points="12 6 12 12 16 14"/>
        </svg>
    </div>

    <div class="error-code">419</div>

    <h1>Sesi Anda Telah Kedaluwarsa</h1>
    <p class="subtitle">
        Token keamanan sesi Anda sudah tidak valid. Hal ini biasanya terjadi jika halaman terlalu lama terbuka.<br>
        Silakan muat ulang halaman dan coba lagi.
    </p>

    <div class="actions">
        <a href="javascript:location.reload()" class="btn btn-primary">
            🔄 Muat Ulang Halaman
        </a>
        <a href="{{ route('login') }}" class="btn btn-secondary">
            Login Kembali
        </a>
    </div>
@endsection
