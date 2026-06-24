@extends('errors.layout')

@section('title', '500 — Kesalahan Server')

@section('content')
    <div class="icon-wrap">
        {{-- Alert triangle --}}
        <svg viewBox="0 0 24 24">
            <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            <line x1="12" y1="9" x2="12" y2="13"/>
            <line x1="12" y1="17" x2="12.01" y2="17"/>
        </svg>
    </div>

    <div class="error-code">500</div>

    <h1>Terjadi Kesalahan Server</h1>
    <p class="subtitle">
        Maaf, terjadi kesalahan pada server kami. Tim teknis sudah diberitahu.<br>
        Silakan coba lagi dalam beberapa saat.
    </p>

    <div class="actions">
        <a href="{{ url('/') }}" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            Kembali ke Beranda
        </a>
        <a href="javascript:location.reload()" class="btn btn-secondary">
            🔄 Coba Lagi
        </a>
    </div>
@endsection
