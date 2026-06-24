@extends('layouts.app')

@section('title', 'Verifikasi Email')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-sm border border-gray-200 p-8 text-center">

        {{-- Icon --}}
        <div class="mx-auto w-16 h-16 rounded-full bg-blue-50 flex items-center justify-center mb-6">
            <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </div>

        <h1 class="text-2xl font-bold text-gray-900 mb-2">Verifikasi Email Anda</h1>
        <p class="text-gray-600 mb-6">
            Kami telah mengirimkan link verifikasi ke email
            <strong class="text-gray-900">{{ Auth::user()->email }}</strong>.
            Silakan cek inbox (atau folder spam) dan klik link tersebut untuk mengaktifkan akun Anda.
        </p>

        @if (session('success'))
            <div class="mb-4 p-3 rounded-xl bg-green-50 border border-green-200 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        {{-- Kirim Ulang --}}
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit"
                class="w-full py-3 px-6 rounded-xl font-semibold text-white text-sm
                       bg-primary-600 hover:bg-primary-700 transition-colors">
                Kirim Ulang Link Verifikasi
            </button>
        </form>

        <div class="mt-6 flex items-center justify-center gap-4 text-sm">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-gray-500 hover:text-gray-700 underline">
                    Logout
                </button>
            </form>
        </div>

        <p class="mt-6 text-xs text-gray-400">
            Tidak menerima email? Pastikan email Anda benar atau cek folder spam.
        </p>
    </div>
</div>
@endsection
