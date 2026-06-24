@extends('layouts.dashboard')

@section('title', 'Sertifikat Saya')

@section('page-header')
    <h1 class="text-lg font-bold text-gray-900">Sertifikat</h1>
@endsection

@section('content')
<div class="space-y-6">

    {{-- ═══ STATS ═════════════════════════════════════════════════════════════ --}}
    <div class="bg-gradient-to-r from-primary-50 to-secondary-50 rounded-xl border border-primary-100 p-5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-primary-100 flex items-center justify-center">
                @include('layouts.partials.icon', ['name' => 'badge-check', 'class' => 'w-5 h-5 text-primary-600'])
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-900">{{ $certificates->total() }} Sertifikat Diraih</p>
                <p class="text-xs text-gray-500">Setiap sertifikat membuktikan keahlianmu. Terus belajar! 🎓</p>
            </div>
        </div>
    </div>

    {{-- ═══ CERTIFICATE CARDS ═════════════════════════════════════════════════ --}}
    @if($certificates->isNotEmpty())
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($certificates as $cert)
                @php $course = $cert->course; @endphp
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-md transition-shadow group">
                    {{-- Thumbnail --}}
                    <div class="relative">
                        <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}"
                             class="w-full h-36 object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                        <div class="absolute bottom-3 left-3 right-3">
                            <p class="text-xs font-mono text-white/80">{{ $cert->certificate_number }}</p>
                        </div>
                    </div>

                    <div class="p-4">
                        <h3 class="text-sm font-bold text-gray-900 mb-1 line-clamp-2 group-hover:text-primary-600 transition-colors">
                            {{ $course->title }}
                        </h3>
                        <p class="text-xs text-gray-500 mb-1">{{ $course->instructor->name ?? '-' }}</p>
                        <p class="text-xs text-gray-400 mb-4">
                            Diterbitkan {{ tanggal_indo($cert->issued_at) }}
                        </p>

                        {{-- Download --}}
                        <a href="{{ $cert->download_url }}"
                           class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg border border-primary-200 text-primary-600 text-xs font-semibold hover:bg-primary-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17v3a2 2 0 002 2h14a2 2 0 002-2v-3"/>
                            </svg>
                            Download Sertifikat
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $certificates->links() }}
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                @include('layouts.partials.icon', ['name' => 'badge-check', 'class' => 'w-8 h-8 text-gray-400'])
            </div>
            <h3 class="text-base font-bold text-gray-900 mb-1">Belum Ada Sertifikat</h3>
            <p class="text-sm text-gray-500 mb-4">Selesaikan kursus untuk mendapatkan sertifikat digital.</p>
            <a href="{{ route('dashboard.my-courses') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary-600 text-white text-sm font-semibold hover:bg-primary-700 transition-colors">
                Lihat Kursus Saya
            </a>
        </div>
    @endif

</div>
@endsection
