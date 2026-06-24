{{-- resources/views/pages/certificates/verify.blade.php --}}
@extends('layouts.app')

@section('title', 'Verifikasi Sertifikat')

@push('head')
<meta name="robots" content="noindex">
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 py-16 px-4">
    <div class="max-w-2xl mx-auto">

        {{-- Logo + Title --}}
        <div class="text-center mb-10">
            <div class="inline-flex items-center gap-2.5 mb-4">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-purple-600 rounded-xl flex items-center justify-center shadow-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <span class="text-2xl font-extrabold text-blue-600">{{ \App\Models\Setting::get('site_name', 'Skolah.com') }}</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Verifikasi Sertifikat</h1>
            <p class="text-gray-500 text-sm mt-1">Cek keaslian sertifikat yang diterbitkan oleh {{ \App\Models\Setting::get('site_name', 'Skolah.com') }}</p>
        </div>

        {{-- Result Card --}}
        @if($valid && $certificate)
            {{-- VALID --}}
            <div class="bg-white rounded-3xl shadow-xl ring-1 ring-gray-200 overflow-hidden">

                {{-- Header banner --}}
                <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-5 text-white text-center">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-white/20 rounded-full mb-3">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold">Sertifikat Asli & Valid</h2>
                    <p class="text-green-100 text-sm mt-1">Sertifikat ini diterbitkan oleh {{ \App\Models\Setting::get('site_name', 'Skolah.com') }} dan telah terverifikasi</p>
                </div>

                {{-- Detail --}}
                <div class="p-6 sm:p-8 space-y-5">

                    {{-- Certificate Number --}}
                    <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center shrink-0">
                            🏆
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-medium">Nomor Sertifikat</p>
                            <p class="text-base font-bold text-gray-900 tracking-wider font-mono">{{ $certificate->certificate_number }}</p>
                        </div>
                    </div>

                    {{-- Pemilik --}}
                    <div class="flex items-center gap-4">
                        <img src="{{ avatarUrl($certificate->user) }}"
                             alt="{{ $certificate->user->name }}"
                             class="w-14 h-14 rounded-full object-cover ring-2 ring-blue-100">
                        <div>
                            <p class="text-xs text-gray-500">Diterbitkan untuk</p>
                            <p class="text-lg font-bold text-gray-900">{{ $certificate->user->name }}</p>
                        </div>
                    </div>

                    <hr class="border-gray-100">

                    {{-- Kursus --}}
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Telah menyelesaikan kursus</p>
                        <p class="text-base font-semibold text-gray-800">{{ $certificate->course->title }}</p>
                    </div>

                    {{-- Tanggal --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-3 bg-gray-50 rounded-xl">
                            <p class="text-xs text-gray-500">Tanggal Terbit</p>
                            <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $certificate->issued_at->translatedFormat('d F Y') }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-xl">
                            <p class="text-xs text-gray-500">Penerbit</p>
                            <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ \App\Models\Setting::get('site_name', 'Skolah.com') }}</p>
                        </div>
                    </div>

                    {{-- CTA --}}
                    <div class="flex flex-col sm:flex-row gap-3 pt-2">
                        <a href="{{ route('home') }}"
                           class="flex-1 text-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition">
                            Kunjungi {{ \App\Models\Setting::get('site_name', 'Skolah.com') }}
                        </a>
                        <a href="{{ route('courses.index') }}"
                           class="flex-1 text-center px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition">
                            Lihat Kursus Lainnya
                        </a>
                    </div>
                </div>
            </div>

        @else
            {{-- INVALID --}}
            <div class="bg-white rounded-3xl shadow-xl ring-1 ring-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-red-500 to-rose-600 px-6 py-5 text-white text-center">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-white/20 rounded-full mb-3">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold">Sertifikat Tidak Ditemukan</h2>
                    <p class="text-red-100 text-sm mt-1">Nomor sertifikat yang Anda masukkan tidak terdapat dalam sistem kami</p>
                </div>
                <div class="p-6 sm:p-8 text-center">
                    <p class="text-gray-600 text-sm mb-6">
                        Pastikan nomor sertifikat yang dimasukkan sudah benar (contoh: <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs font-mono">SKOL-2025-000001</code>).
                        Jika masalah berlanjut, hubungi tim support {{ \App\Models\Setting::get('site_name', 'Skolah.com') }}.
                    </p>
                    <a href="{{ route('home') }}"
                       class="inline-block px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition">
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        @endif

        {{-- Search form --}}
        <div class="mt-8 bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Verifikasi Nomor Sertifikat Lain</h3>
            <form action="" method="GET" class="flex gap-2">
                <input type="text" name="_redirect"
                       placeholder="Contoh: SKOL-2025-000001"
                       class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none font-mono">
                <button type="submit"
                        class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                    Verifikasi
                </button>
            </form>
        </div>

    </div>
</div>

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
    // Handle the search form to redirect to /verify/{certNumber}
    document.querySelector('form').addEventListener('submit', function(e) {
        e.preventDefault();
        const input = this.querySelector('input[name="_redirect"]').value.trim();
        if (input) {
            window.location.href = '/verify/' + encodeURIComponent(input);
        }
    });
</script>
@endpush
@endsection
