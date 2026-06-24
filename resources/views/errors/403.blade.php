@extends('errors.layout')

@section('title', '403 — Akses Ditolak')

@section('content')
    <div class="icon-wrap">
        {{-- Shield with X --}}
        <svg viewBox="0 0 24 24">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            <line x1="9.5" y1="9.5" x2="14.5" y2="14.5"/>
            <line x1="14.5" y1="9.5" x2="9.5" y2="14.5"/>
        </svg>
    </div>

    <div class="error-code">403</div>

    <h1>Akses Ditolak</h1>
    <p class="subtitle">
        Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.<br>
        Jika Anda yakin ini kesalahan, silakan hubungi administrator.
    </p>

    <div class="actions">
        <a href="{{ url('/') }}" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            Kembali ke Beranda
        </a>
        <a href="javascript:history.back()" class="btn btn-secondary">
            ← Kembali
        </a>
    </div>
@endsection
