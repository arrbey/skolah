@extends('layouts.auth')

@section('title', 'Reset Kata Sandi')

@section('auth-header')
    <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-dark">
        Buat kata sandi baru
    </h2>
    <p class="mt-2 text-center text-sm text-gray-600">
        Masukkan kata sandi baru yang kuat untuk akun Anda.
    </p>
@endsection

@section('content')
    <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
        @csrf

        {{-- Hidden token & email --}}
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email ?? old('email') }}">

        {{-- Email (readonly display) --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                Akun Email
            </label>
            <div class="flex items-center gap-3 rounded-xl bg-gray-50 border border-gray-200 px-4 py-3">
                <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                </svg>
                <span class="text-sm text-gray-600">{{ $email ?? old('email') }}</span>
            </div>
        </div>

        {{-- New Password --}}
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">
                Kata Sandi Baru
            </label>
            <div class="relative">
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    autocomplete="new-password"
                    autofocus
                    placeholder="Min. 8 karakter, huruf besar, angka"
                    class="block w-full rounded-xl border-0 py-3 px-4 pr-12 text-gray-900 ring-1 ring-inset
                           ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset
                           focus:ring-primary-600 transition-shadow text-sm
                           @error('password') ring-red-400 focus:ring-red-500 @enderror"
                >
                <button type="button"
                        data-toggle-password="password"
                        class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </div>
            @error('password')
                <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Confirm Password --}}
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">
                Konfirmasi Kata Sandi Baru
            </label>
            <div class="relative">
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="Ulangi kata sandi baru"
                    class="block w-full rounded-xl border-0 py-3 px-4 pr-12 text-gray-900 ring-1 ring-inset
                           ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset
                           focus:ring-primary-600 transition-shadow text-sm"
                >
                <button type="button"
                        data-toggle-password="password_confirmation"
                        class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Submit --}}
        <button type="submit"
                class="flex w-full justify-center items-center gap-2 rounded-xl bg-primary-600 px-4 py-3
                       text-sm font-semibold text-white shadow-sm hover:bg-primary-700 focus-visible:outline
                       focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600
                       transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            Simpan Kata Sandi Baru
        </button>
    </form>
@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
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
</script>
@endpush
