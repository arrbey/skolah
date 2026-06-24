@extends('layouts.auth')

@section('title', 'Lupa Kata Sandi')

@section('auth-header')
    <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-dark">
        Lupa kata sandi?
    </h2>
    <p class="mt-2 text-center text-sm text-gray-600">
        Masukkan email Anda dan kami akan mengirimkan tautan untuk mereset kata sandi.
    </p>
@endsection

@section('content')
    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                Alamat Email
            </label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                required
                autocomplete="email"
                autofocus
                placeholder="nama@email.com"
                class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 ring-1 ring-inset
                       ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset
                       focus:ring-primary-600 transition-shadow text-sm
                       @error('email') ring-red-400 focus:ring-red-500 @enderror"
            >
            @error('email')
                <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Submit --}}
        <button type="submit"
                class="flex w-full justify-center items-center gap-2 rounded-xl bg-primary-600 px-4 py-3
                       text-sm font-semibold text-white shadow-sm hover:bg-primary-700 focus-visible:outline
                       focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600
                       transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            Kirim Tautan Reset
        </button>

        {{-- Back to login --}}
        <div class="text-center">
            <a href="{{ route('login') }}"
               class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke halaman masuk
            </a>
        </div>
    </form>
@endsection
