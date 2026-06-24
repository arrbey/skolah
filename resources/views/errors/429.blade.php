@extends('errors.layout')

@section('title', '429 — Terlalu Banyak Permintaan')

@section('content')
    <div class="icon-wrap">
        {{-- Zap / throttle icon --}}
        <svg viewBox="0 0 24 24">
            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
        </svg>
    </div>

    <div class="error-code">429</div>

    <h1>Terlalu Banyak Permintaan</h1>
    <p class="subtitle">
        Anda mengirim terlalu banyak permintaan dalam waktu singkat.<br>
        Silakan tunggu beberapa saat, lalu coba lagi.
    </p>

    <div class="actions">
        <a href="javascript:setTimeout(()=>location.reload(), 1000)" class="btn btn-primary">
            ⏳ Tunggu &amp; Coba Lagi
        </a>
        <a href="{{ url('/') }}" class="btn btn-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            Kembali ke Beranda
        </a>
    </div>
@endsection
