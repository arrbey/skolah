@extends('layouts.auth')

@section('title', 'Masuk')

@section('auth-header')
    <div class="mb-8">
        <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 mb-2">
            Selamat datang kembali!
        </h2>
        <p class="text-sm text-slate-500">
            Kembangkan potensi tanpa batas. Belum punya akun?
            <a href="{{ route('register') }}" class="font-bold text-blue-600 hover:text-blue-700 hover:underline underline-offset-4 transition-all">
                Daftar gratis
            </a>
        </p>
    </div>
@endsection

@section('content')
    @if(\App\Models\Setting::get('maintenance_mode', '0') === '1')
        <div class="mb-4 rounded-xl border border-amber-300 bg-amber-50 px-4 py-3">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="text-sm font-semibold text-amber-800">Situs sedang dalam pemeliharaan</p>
                    <p class="text-xs text-amber-600 mt-0.5">Hanya administrator yang dapat login saat ini.</p>
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">
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
                placeholder="nama@perusahaan.com"
                class="block w-full rounded-xl border-slate-200 bg-slate-50 py-3.5 px-4 text-slate-900 
                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm
                       @error('email') border-red-300 ring-4 ring-red-100 bg-red-50 @enderror"
            >
            @error('email')
                <p class="mt-2 text-xs text-red-600 font-medium flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <div class="flex items-center justify-between mb-2">
                <label for="password" class="block text-sm font-semibold text-slate-700">
                    Kata Sandi
                </label>
                <a href="{{ route('password.request') }}"
                   class="text-xs font-bold text-blue-600 hover:text-blue-700 hover:underline underline-offset-4 transition-all">
                    Lupa sandi?
                </a>
            </div>
            <div class="relative">
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                    class="block w-full rounded-xl border-slate-200 bg-slate-50 py-3.5 px-4 pr-12 text-slate-900 
                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm
                           @error('password') border-red-300 ring-4 ring-red-100 bg-red-50 @enderror"
                >
                <button type="button"
                        data-toggle-password="password"
                        class="absolute inset-y-0 right-2 flex items-center px-3 text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="eye-password">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </div>
            @error('password')
                <p class="mt-2 text-xs text-red-600 font-medium flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Remember Me --}}
        <div class="flex items-center mt-2 pb-2">
            <input
                id="remember"
                name="remember"
                type="checkbox"
                value="1"
                {{ old('remember') ? 'checked' : '' }}
                class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-600 focus:ring-offset-0 cursor-pointer shadow-sm"
            >
            <label for="remember" class="ml-3 block text-sm text-slate-600 cursor-pointer select-none">
                Ingat saya di perangkat ini
            </label>
        </div>

        {{-- Submit --}}
        <button type="submit"
                class="flex w-full justify-center items-center gap-2 rounded-xl bg-blue-600 px-4 py-3.5
                       text-sm font-bold text-white shadow hover:bg-blue-700 hover:shadow-lg hover:-translate-y-0.5
                       active:scale-[0.98] transition-all disabled:opacity-60 disabled:cursor-not-allowed">
            Masuk ke Akun
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
            </svg>
        </button>
    </form>

    {{-- Divider --}}
    <div class="mt-8">
        <div class="relative">
            <div class="absolute inset-0 flex items-center" aria-hidden="true">
                <div class="w-full border-t border-slate-200"></div>
            </div>
            <div class="relative flex justify-center text-xs">
                <span class="bg-white px-4 text-slate-500 font-medium">Atau masuk dengan</span>
            </div>
        </div>

        {{-- Google Login Button --}}
        <div class="mt-6">
            <a href="{{ route('social.redirect', 'google') }}"
               class="flex w-full items-center justify-center gap-3 rounded-xl bg-white px-4 py-3.5
                      text-sm font-bold text-slate-700 shadow-sm border border-slate-200
                      hover:bg-slate-50 hover:shadow transition-all group">
                <svg class="w-5 h-5 group-hover:scale-110 transition-transform" viewBox="0 0 24 24">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
                Sign in with Google
            </a>
        </div>

        <div class="mt-8 text-center sm:hidden">
            <a href="{{ route('home') }}"
               class="text-sm font-semibold text-slate-500 hover:text-slate-800 transition-colors inline-block pb-1 border-b border-transparent hover:border-slate-800">
                &larr; Kembali ke beranda
            </a>
        </div>
    </div>
@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
(function () {
    document.querySelectorAll('[data-toggle-password]').forEach(function (button) {
        button.addEventListener('click', function () {
            var targetId = this.getAttribute('data-toggle-password');
            var field = document.getElementById(targetId);
            if (field) {
                var isHidden = field.type === 'password';
                field.type = isHidden ? 'text' : 'password';
                var svg = this.querySelector('svg');
                if (svg) svg.style.opacity = isHidden ? '0.5' : '1';
            }
        });
    });
})();
</script>
@endpush
