@extends('layouts.auth')

@section('title', 'Perbarui Password')

@section('auth-header')
    <div class="mb-8 text-center">
        <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 mb-2">
            Amankan Akun Anda
        </h2>
        <p class="text-sm text-slate-500">
            Demi alasan keamanan, silakan perbarui password sementara Anda sebelum melanjutkan ke dashboard.
        </p>
    </div>
@endsection

@section('content')
    <form method="POST" action="{{ route('auth.force-password-change.post') }}" class="space-y-6">
        @csrf

        {{-- New Password --}}
        <div>
            <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">
                Kata Sandi Baru
            </label>
            <div class="relative">
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    placeholder="Minimal 8 karakter, huruf & angka"
                    class="block w-full rounded-xl border-slate-200 bg-slate-50 py-3.5 px-4 pr-12 text-slate-900 
                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm
                           @error('password') border-red-300 ring-4 ring-red-100 bg-red-50 @enderror"
                >
                <button type="button"
                        onclick="togglePassword('password', this)"
                        class="absolute inset-y-0 right-2 flex items-center px-3 text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
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

        {{-- Password Confirmation --}}
        <div>
            <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-2">
                Konfirmasi Kata Sandi Baru
            </label>
            <div class="relative">
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    required
                    placeholder="Ulangi kata sandi baru"
                    class="block w-full rounded-xl border-slate-200 bg-slate-50 py-3.5 px-4 pr-12 text-slate-900 
                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm"
                >
                <button type="button"
                        onclick="togglePassword('password_confirmation', this)"
                        class="absolute inset-y-0 right-2 flex items-center px-3 text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </div>
        </div>

        <button type="submit"
                class="flex w-full justify-center items-center gap-2 rounded-xl bg-indigo-600 px-4 py-3.5
                       text-sm font-bold text-white shadow hover:bg-indigo-700 hover:shadow-lg hover:-translate-y-0.5
                       active:scale-[0.98] transition-all disabled:opacity-60 disabled:cursor-not-allowed">
            Perbarui Password & Masuk Dashboard
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </form>
@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
function togglePassword(fieldId, btn) {
    const field = document.getElementById(fieldId);
    const isHidden = field.type === 'password';
    field.type = isHidden ? 'text' : 'password';
}
</script>
@endpush
