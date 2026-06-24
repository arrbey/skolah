@extends('errors.layout')

@section('title', '404 — Halaman Tidak Ditemukan')

@section('content')
    <div class="icon-wrap">
        {{-- Search/magnifying glass with X --}}
        <svg viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8"/>
            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            <line x1="9" y1="9" x2="13" y2="13"/>
            <line x1="13" y1="9" x2="9" y2="13"/>
        </svg>
    </div>

    <div class="error-code">404</div>

    <h1>Halaman Tidak Ditemukan</h1>
    <p class="subtitle">
        Maaf, halaman yang Anda cari tidak ada atau telah dipindahkan.<br>
        Pastikan URL sudah benar, atau kembali ke beranda.
    </p>

    <div class="actions">
        <a href="{{ url('/') }}" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            Kembali ke Beranda
        </a>
        <a href="{{ url('/courses') }}" class="btn btn-secondary">
            Jelajahi Kursus
        </a>
    </div>
@endsection
