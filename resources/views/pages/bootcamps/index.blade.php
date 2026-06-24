@extends('layouts.app')

@section('content')

{{-- ═══════════════════════════════════════════════════════════════════════════
     HERO HEADER
═══════════════════════════════════════════════════════════════════════════ --}}
<section class="relative bg-white pt-28 pb-16 overflow-hidden border-b border-slate-100">
    {{-- Decorative background --}}
    <div class="absolute inset-0 pointer-events-none overflow-hidden" aria-hidden="true">
        <div class="absolute -top-[10%] -right-[10%] w-[40%] h-[40%] bg-blue-100/50 rounded-full mix-blend-multiply filter blur-[80px] opacity-60"></div>
        <div class="absolute top-[30%] -left-[5%] w-[35%] h-[35%] bg-purple-100/50 rounded-full mix-blend-multiply filter blur-[80px] opacity-50"></div>
        <div class="absolute -bottom-[15%] right-[15%] w-[30%] h-[30%] bg-pink-100/40 rounded-full mix-blend-multiply filter blur-[80px] opacity-50"></div>
        <div class="absolute inset-0 opacity-[0.35]" style="background-image: radial-gradient(#cbd5e1 1px, transparent 1px); background-size: 40px 40px;"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 z-10">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-1.5 text-sm text-slate-400 mb-8">
            <a href="{{ route('home') }}" class="hover:text-blue-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </a>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-slate-700 font-medium">Bootcamp & Webinar</span>
        </nav>

        <div class="max-w-2xl">
            {{-- Pill badge --}}
            <div class="inline-flex items-center gap-2 bg-white border border-slate-200 shadow-sm text-slate-700 text-xs font-semibold px-4 py-1.5 rounded-full mb-5">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                {{ $stats['upcoming'] }} Bootcamp Segera Hadir
            </div>

            <h1 id="bootcamp-header" class="text-4xl lg:text-5xl font-extrabold text-slate-900 leading-tight mb-4 tracking-tight">
                Bootcamp &
                <span class="text-blue-600">Webinar</span>
            </h1>
            <p class="text-lg text-slate-500 leading-relaxed mb-8">
                Belajar intensif langsung dari mentor berpengalaman. Kuasai skill baru dalam waktu singkat melalui sesi online maupun tatap muka.
            </p>

            {{-- Stats Row --}}
            <div class="flex flex-wrap gap-4">
                @foreach ([
                    [$stats['total'],    'Total Bootcamp',   '🎓'],
                    [$stats['upcoming'], 'Segera Hadir',     '⏰'],
                    [$stats['online'],   'Online',           '🌐'],
                ] as [$num, $label, $icon])
                    <div class="flex items-center gap-3 bg-white px-5 py-3 rounded-xl shadow-sm border border-slate-200">
                        <span class="text-2xl">{{ $icon }}</span>
                        <div>
                            <p class="text-xl font-extrabold text-slate-900 leading-none">{{ $num }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $label }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>


{{-- ═══════════════════════════════════════════════════════════════════════════
     BOOTCAMP FILTER + GRID (Livewire)
═══════════════════════════════════════════════════════════════════════════ --}}
<section class="bg-white py-8 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" id="bootcamp-content">
        @livewire('bootcamp-filter')
    </div>
</section>

@endsection
