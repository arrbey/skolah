@extends('layouts.dashboard')

@section('title', 'Detail Tiket — ' . $bootcamp->title)

@section('page-header')
    <div class="flex items-center gap-3">
        <a href="{{ route('dashboard.my-bootcamps') }}"
           class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-lg font-bold text-gray-900">Detail Tiket Bootcamp</h1>
            <p class="text-xs text-gray-500 mt-0.5">{{ $bootcamp->title }}</p>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- ═══ STATUS BANNER ══════════════════════════════════════════════════ --}}
    @php
        $isOffline = $bootcamp->type === 'offline';
        $statusColor = match($bootcamp->status) {
            'upcoming'  => ['bg' => 'bg-blue-50',  'border' => 'border-blue-200',  'text' => 'text-blue-800',  'dot' => 'bg-blue-500'],
            'ongoing'   => ['bg' => 'bg-green-50', 'border' => 'border-green-200', 'text' => 'text-green-800', 'dot' => 'bg-green-500 animate-pulse'],
            'completed' => ['bg' => 'bg-gray-50',  'border' => 'border-gray-200',  'text' => 'text-gray-700',  'dot' => 'bg-gray-400'],
            default     => ['bg' => 'bg-gray-50',  'border' => 'border-gray-200',  'text' => 'text-gray-700',  'dot' => 'bg-gray-400'],
        };
    @endphp

    <div class="rounded-xl border {{ $statusColor['border'] }} {{ $statusColor['bg'] }} p-4 flex items-center gap-3">
        <span class="w-2.5 h-2.5 rounded-full {{ $statusColor['dot'] }} shrink-0"></span>
        <div>
            <span class="text-sm font-semibold {{ $statusColor['text'] }}">
                @if($bootcamp->status === 'upcoming') 🗓️ Bootcamp akan segera dimulai
                @elseif($bootcamp->status === 'ongoing') 🟢 Bootcamp sedang berlangsung
                @else ✅ Bootcamp telah selesai
                @endif
            </span>
            <div class="flex items-center gap-2 mt-1">
                <span class="text-xs px-2 py-0.5 rounded-md font-semibold
                    {{ $isOffline ? 'bg-amber-100 text-amber-800' : 'bg-sky-100 text-sky-800' }}">
                    {{ $isOffline ? '📍 Offline' : '🌐 Online' }}
                </span>
                <span class="text-xs text-gray-400">{{ $bootcamp->platform_label }}</span>
            </div>
        </div>
        <span class="ml-auto text-xs font-medium {{ $statusColor['text'] }}">{{ $bootcamp->status_label }}</span>
    </div>

    {{-- ═══ MAIN GRID ══════════════════════════════════════════════════════ --}}
    <div class="grid lg:grid-cols-5 gap-6">

        {{-- ── LEFT COL (3/5) ──────────────────────────────────────────── --}}
        <div class="lg:col-span-3 space-y-5">

            {{-- Info Card --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="relative h-48 bg-gray-100">
                    <img src="{{ $bootcamp->thumbnail_url }}" alt="{{ $bootcamp->title }}"
                         class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    <div class="absolute bottom-3 left-3 flex items-center gap-2">
                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold
                            {{ $isOffline ? 'bg-amber-500 text-white' : 'bg-sky-500 text-white' }}">
                            {{ $isOffline ? '📍 Offline' : '🌐 Online' }}
                        </span>
                    </div>
                </div>

                <div class="p-5">
                    <h2 class="text-base font-bold text-gray-900 mb-1">{{ $bootcamp->title }}</h2>
                    <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
                        <div class="w-5 h-5 rounded-full bg-gray-200 overflow-hidden shrink-0 flex items-center justify-center text-[10px] font-bold text-blue-600">
                            {{ strtoupper(substr($bootcamp->instructor?->name ?? 'I', 0, 1)) }}
                        </div>
                        <span>{{ $bootcamp->instructor?->name ?? '-' }}</span>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-medium">Tanggal Mulai</p>
                                <p class="text-sm font-semibold text-gray-800">{{ $bootcamp->start_date_formatted }}</p>
                            </div>
                        </div>

                        @if($bootcamp->end_date)
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-medium">Tanggal Selesai</p>
                                <p class="text-sm font-semibold text-gray-800">{{ $bootcamp->end_date_formatted }}</p>
                            </div>
                        </div>
                        @endif

                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-sky-50 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-medium">Platform</p>
                                <p class="text-sm font-semibold text-gray-800">{{ $bootcamp->platform_label }}</p>
                            </div>
                        </div>

                        @if($isOffline && $bootcamp->location)
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-medium">Lokasi</p>
                                <p class="text-sm font-semibold text-gray-800">{{ $bootcamp->location }}</p>
                            </div>
                        </div>
                        @endif

                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-medium">Peserta Terdaftar</p>
                                <p class="text-sm font-semibold text-gray-800">
                                    {{ number_format($bootcamp->total_registered) }} peserta
                                    @if($bootcamp->max_participants > 0)
                                        <span class="text-gray-400">/ {{ number_format($bootcamp->max_participants) }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── MEETING LINK (online only) ──────────────────────────── --}}
            @if(!$isOffline)
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-sky-100 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                        </svg>
                    </div>
                    Link Meeting
                </h3>

                @if($bootcamp->meeting_link && in_array($bootcamp->status, ['upcoming', 'ongoing']))
                    <div class="bg-sky-50 border border-sky-200 rounded-xl p-4" x-data="{ copied: false }">
                        <div class="flex items-center gap-2 mb-3">
                            @if(Str::contains($bootcamp->meeting_link ?? '', 'zoom'))
                                <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center shrink-0"><span class="text-white text-xs font-bold">Z</span></div>
                                <span class="text-sm font-semibold text-gray-700">Zoom Meeting</span>
                            @elseif(Str::contains($bootcamp->meeting_link ?? '', 'meet.google'))
                                <div class="w-8 h-8 rounded-lg bg-green-500 flex items-center justify-center shrink-0"><span class="text-white text-xs font-bold">M</span></div>
                                <span class="text-sm font-semibold text-gray-700">Google Meet</span>
                            @else
                                <div class="w-8 h-8 rounded-lg bg-sky-500 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                </div>
                                <span class="text-sm font-semibold text-gray-700">{{ $bootcamp->platform_label }}</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="text" value="{{ $bootcamp->meeting_link }}" readonly
                                   class="flex-1 text-xs text-gray-700 bg-white border border-sky-200 rounded-lg px-3 py-2 font-mono truncate focus:outline-none">
                            <button @click="navigator.clipboard.writeText('{{ $bootcamp->meeting_link }}'); copied = true; setTimeout(() => copied = false, 2500)"
                                    class="shrink-0 inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold transition-all"
                                    :class="copied ? 'bg-green-500 text-white' : 'bg-sky-600 text-white hover:bg-sky-700'">
                                <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                <svg x-show="copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span x-text="copied ? 'Tersalin!' : 'Salin'"></span>
                            </button>
                        </div>
                        <a href="{{ $bootcamp->meeting_link }}" target="_blank" rel="noopener noreferrer"
                           class="mt-3 w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            Buka Link Meeting
                        </a>
                    </div>
                @elseif(!$bootcamp->meeting_link)
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 flex items-start gap-3">
                        <svg class="w-5 h-5 text-yellow-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <div>
                            <p class="text-sm font-semibold text-yellow-800">Link belum tersedia</p>
                            <p class="text-xs text-yellow-700 mt-0.5">Link meeting akan diberikan oleh instruktur menjelang acara dimulai.</p>
                        </div>
                    </div>
                @else
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 flex items-start gap-3">
                        <svg class="w-5 h-5 text-gray-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <p class="text-sm font-semibold text-gray-600">Bootcamp Telah Selesai</p>
                            <p class="text-xs text-gray-500 mt-0.5">Sesi ini sudah berakhir. Terima kasih sudah hadir!</p>
                        </div>
                    </div>
                @endif
            </div>
            @endif

            {{-- ── Instruksi check-in (offline only) ───────────────────── --}}
            @if($isOffline)
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="text-sm">
                    <p class="font-bold text-amber-800 mb-1">Petunjuk Kehadiran Offline</p>
                    <ul class="text-amber-700 space-y-0.5 text-xs list-disc list-inside">
                        <li>Tunjukkan QR code kepada panitia saat check-in di lokasi</li>
                        <li>Atau unduh tiket PDF dan tunjukkan kepada panitia</li>
                        <li>Pastikan nama di tiket sesuai dengan identitas Anda</li>
                        <li>Hadir tepat waktu: <strong>{{ $bootcamp->start_date_formatted }}</strong></li>
                        @if($bootcamp->location)
                            <li>Lokasi: <strong>{{ $bootcamp->location }}</strong></li>
                        @endif
                    </ul>
                </div>
            </div>
            @endif

        </div>

        {{-- ── RIGHT COL (2/5) ─────────────────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- E-Tiket Card --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="h-2 bg-gradient-to-r {{ $isOffline ? 'from-amber-400 via-orange-500 to-red-400' : 'from-primary-600 via-violet-500 to-sky-500' }}"></div>

                <div class="p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-gray-900">
                            {{ $isOffline ? '🎫 Tiket Offline' : '🌐 Akses Online' }}
                        </h3>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold
                            {{ $registration->payment_status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $registration->payment_status === 'paid' ? 'bg-green-500' : 'bg-yellow-500' }}"></span>
                            {{ $registration->status_label }}
                        </span>
                    </div>

                    <div class="border-t border-dashed border-gray-200 my-4 relative">
                        <span class="absolute -left-5 -top-2.5 w-5 h-5 rounded-full bg-gray-50 border border-gray-200"></span>
                        <span class="absolute -right-5 -top-2.5 w-5 h-5 rounded-full bg-gray-50 border border-gray-200"></span>
                    </div>

                    {{-- Kode tiket --}}
                    <div class="text-center mb-4" x-data="{ copied: false }">
                        <p class="text-[10px] text-gray-400 uppercase font-semibold tracking-widest mb-1.5">Kode Tiket</p>
                        <div class="bg-gray-900 rounded-xl px-4 py-3 inline-block w-full">
                            <p class="text-lg font-mono font-bold text-white tracking-widest">
                                {{ $registration->ticket_code }}
                            </p>
                        </div>
                        <button @click="navigator.clipboard.writeText('{{ $registration->ticket_code }}'); copied = true; setTimeout(() => copied = false, 2500)"
                                class="mt-2 inline-flex items-center gap-1.5 text-xs font-medium transition-colors"
                                :class="copied ? 'text-green-600' : 'text-gray-400 hover:text-gray-600'">
                            <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            <svg x-show="copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span x-text="copied ? 'Kode disalin!' : 'Salin kode'"></span>
                        </button>
                    </div>

                    <div class="border-t border-dashed border-gray-200 my-4 relative">
                        <span class="absolute -left-5 -top-2.5 w-5 h-5 rounded-full bg-gray-50 border border-gray-200"></span>
                        <span class="absolute -right-5 -top-2.5 w-5 h-5 rounded-full bg-gray-50 border border-gray-200"></span>
                    </div>

                    <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                        <span>Terdaftar pada</span>
                        <span class="font-semibold text-gray-700">
                            {{ $registration->registered_at?->translatedFormat('d M Y, H:i') ?? '-' }} WIB
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-xs text-gray-500">
                        <span>Pemegang tiket</span>
                        <span class="font-semibold text-gray-700">{{ auth()->user()->name }}</span>
                    </div>

                    {{-- Check-in badge (offline only) --}}
                    @if($isOffline)
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            @if($registration->checked_in)
                                <div class="flex items-center gap-2 bg-blue-50 border border-blue-200 rounded-lg px-3 py-2">
                                    <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <div>
                                        <p class="text-xs font-bold text-blue-800">Sudah Check-In</p>
                                        <p class="text-[10px] text-blue-600">{{ $registration->checked_in_at?->translatedFormat('d M Y, H:i') }} WIB</p>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-lg px-3 py-2">
                                    <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <p class="text-xs text-gray-500">Belum check-in</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- QR Code (offline only) --}}
            @if($isOffline)
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="text-sm font-bold text-gray-900 mb-1 text-center">QR Code Kehadiran</h3>
                <p class="text-xs text-gray-400 text-center mb-4">
                    Tunjukkan QR ini kepada panitia saat check-in di lokasi acara
                </p>

                <div class="flex items-center justify-center">
                    <div class="bg-white border-2 border-gray-200 rounded-2xl p-3 shadow-sm inline-block">
                        {{-- QR mengarah ke halaman verifikasi internal --}}
                        <img src="{{ $registration->qr_image_url }}"
                             alt="QR Code {{ $registration->ticket_code }}"
                             class="w-48 h-48 rounded-lg"
                             loading="lazy">
                    </div>
                </div>

                <p class="text-[10px] text-gray-400 text-center mt-3 font-mono">
                    {{ $registration->ticket_code }}
                </p>

                <div class="mt-4 space-y-2">
                    {{-- Download PDF Tiket --}}
                    <a href="{{ route('tickets.download-pdf', $registration->ticket_code) }}"
                       class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl
                              bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        Unduh Tiket PDF
                    </a>

                    {{-- Download QR PNG --}}
                    <a href="{{ route('tickets.download-qr', $registration->ticket_code) }}"
                       class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl
                              bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Unduh QR Code
                    </a>
                </div>
            </div>
            @endif

            {{-- Link ke halaman publik bootcamp --}}
            <a href="{{ route('bootcamps.show', $bootcamp->slug) }}"
               class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl
                      border border-gray-200 bg-white hover:bg-gray-50 text-gray-600 text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                Lihat Halaman Bootcamp
            </a>

        </div>
    </div>

</div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- ═══ STATUS BANNER ═══════════════════════════════════════════════════ --}}
    @php
        $statusColor = match($bootcamp->status) {
            'upcoming'  => ['bg' => 'bg-blue-50',   'border' => 'border-blue-200',  'text' => 'text-blue-800',  'icon' => 'text-blue-500', 'dot' => 'bg-blue-500'],
            'ongoing'   => ['bg' => 'bg-green-50',  'border' => 'border-green-200', 'text' => 'text-green-800', 'icon' => 'text-green-500','dot' => 'bg-green-500 animate-pulse'],
            'completed' => ['bg' => 'bg-gray-50',   'border' => 'border-gray-200',  'text' => 'text-gray-700',  'icon' => 'text-gray-400', 'dot' => 'bg-gray-400'],
            default     => ['bg' => 'bg-gray-50',   'border' => 'border-gray-200',  'text' => 'text-gray-700',  'icon' => 'text-gray-400', 'dot' => 'bg-gray-400'],
        };
    @endphp
    <div class="rounded-xl border {{ $statusColor['border'] }} {{ $statusColor['bg'] }} p-4 flex items-center gap-3">
        <span class="w-2.5 h-2.5 rounded-full {{ $statusColor['dot'] }} shrink-0"></span>
        <span class="text-sm font-semibold {{ $statusColor['text'] }}">
            @if($bootcamp->status === 'upcoming') 🗓️ Bootcamp akan segera dimulai
            @elseif($bootcamp->status === 'ongoing') 🟢 Bootcamp sedang berlangsung
            @else ✅ Bootcamp telah selesai
            @endif
        </span>
        <span class="ml-auto text-xs font-medium {{ $statusColor['text'] }}">{{ $bootcamp->status_label }}</span>
    </div>

    {{-- ═══ MAIN GRID ════════════════════════════════════════════════════════ --}}
    <div class="grid lg:grid-cols-5 gap-6">

        {{-- ── LEFT COL (3/5): Info + Link ────────────────────────────────── --}}
        <div class="lg:col-span-3 space-y-5">

            {{-- Bootcamp Card Header --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="relative h-48 bg-gray-100">
                    <img src="{{ $bootcamp->thumbnail_url }}"
                         alt="{{ $bootcamp->title }}"
                         class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    <div class="absolute bottom-3 left-3 flex items-center gap-2">
                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold
                            {{ $bootcamp->type === 'online' ? 'bg-sky-500 text-white' : 'bg-amber-500 text-white' }}">
                            {{ $bootcamp->type === 'online' ? '🌐 Online' : '📍 Offline' }}
                        </span>
                    </div>
                </div>

                <div class="p-5">
                    <h2 class="text-base font-bold text-gray-900 mb-1">{{ $bootcamp->title }}</h2>
                    <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
                        <img src="{{ $bootcamp->instructor?->avatar_url ?? asset('images/placeholder-avatar.jpg') }}"
                             alt="{{ $bootcamp->instructor?->name }}"
                             class="w-5 h-5 rounded-full object-cover">
                        <span>{{ $bootcamp->instructor?->name ?? '-' }}</span>
                    </div>

                    {{-- Info Rows --}}
                    <div class="space-y-3">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-medium">Tanggal Mulai</p>
                                <p class="text-sm font-semibold text-gray-800">{{ $bootcamp->start_date_formatted }}</p>
                            </div>
                        </div>

                        @if($bootcamp->end_date)
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-medium">Tanggal Selesai</p>
                                <p class="text-sm font-semibold text-gray-800">{{ $bootcamp->end_date_formatted }}</p>
                            </div>
                        </div>
                        @endif

                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-sky-50 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-medium">Platform</p>
                                <p class="text-sm font-semibold text-gray-800">{{ $bootcamp->platform_label }}</p>
                            </div>
                        </div>

                        @if($bootcamp->type === 'offline' && $bootcamp->location)
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-medium">Lokasi</p>
                                <p class="text-sm font-semibold text-gray-800">{{ $bootcamp->location }}</p>
                            </div>
                        </div>
                        @endif

                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-medium">Peserta Terdaftar</p>
                                <p class="text-sm font-semibold text-gray-800">
                                    {{ number_format($bootcamp->total_registered) }} peserta
                                    @if($bootcamp->max_participants > 0)
                                        <span class="text-gray-400">/ {{ number_format($bootcamp->max_participants) }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══ MEETING LINK (only for online) ═══════════════════════════ --}}
            @if($bootcamp->type === 'online')
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-sky-100 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                        </svg>
                    </div>
                    Link Meeting
                </h3>

                @if($bootcamp->meeting_link && in_array($bootcamp->status, ['upcoming', 'ongoing']))
                    <div class="bg-sky-50 border border-sky-200 rounded-xl p-4" x-data="{ copied: false }">
                        <div class="flex items-center gap-2 mb-3">
                            {{-- Platform icon --}}
                            @if(Str::contains($bootcamp->meeting_link ?? '', 'zoom'))
                                <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center shrink-0">
                                    <span class="text-white text-xs font-bold">Z</span>
                                </div>
                                <span class="text-sm font-semibold text-gray-700">Zoom Meeting</span>
                            @elseif(Str::contains($bootcamp->meeting_link ?? '', 'meet.google'))
                                <div class="w-8 h-8 rounded-lg bg-green-500 flex items-center justify-center shrink-0">
                                    <span class="text-white text-xs font-bold">M</span>
                                </div>
                                <span class="text-sm font-semibold text-gray-700">Google Meet</span>
                            @else
                                <div class="w-8 h-8 rounded-lg bg-sky-500 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-semibold text-gray-700">{{ $bootcamp->platform_label }}</span>
                            @endif
                        </div>

                        <div class="flex items-center gap-2">
                            <input type="text"
                                   id="meeting-link-input"
                                   value="{{ $bootcamp->meeting_link }}"
                                   readonly
                                   class="flex-1 text-xs text-gray-700 bg-white border border-sky-200 rounded-lg px-3 py-2 font-mono truncate focus:outline-none cursor-text">
                            <button @click="navigator.clipboard.writeText('{{ $bootcamp->meeting_link }}'); copied = true; setTimeout(() => copied = false, 2500)"
                                    class="shrink-0 inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold transition-all duration-200"
                                    :class="copied ? 'bg-green-500 text-white' : 'bg-sky-600 text-white hover:bg-sky-700'">
                                <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                <svg x-show="copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span x-text="copied ? 'Tersalin!' : 'Salin'"></span>
                            </button>
                        </div>

                        <a href="{{ $bootcamp->meeting_link }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="mt-3 w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl
                                  bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            Buka Link Meeting
                        </a>
                    </div>

                @elseif(!$bootcamp->meeting_link)
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 flex items-start gap-3">
                        <svg class="w-5 h-5 text-yellow-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-yellow-800">Link belum tersedia</p>
                            <p class="text-xs text-yellow-700 mt-0.5">Link meeting akan diberikan oleh instruktur saat bootcamp akan dimulai.</p>
                        </div>
                    </div>

                @elseif($bootcamp->status === 'completed')
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 flex items-start gap-3">
                        <svg class="w-5 h-5 text-gray-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-gray-600">Bootcamp Telah Selesai</p>
                            <p class="text-xs text-gray-500 mt-0.5">Sesi ini sudah berakhir. Terima kasih sudah hadir!</p>
                        </div>
                    </div>
                @endif
            </div>
            @endif

        </div>

        {{-- ── RIGHT COL (2/5): Tiket + QR ────────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- ══ TIKET CARD ════════════════════════════════════════════════ --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                {{-- Header strip --}}
                <div class="h-2 bg-gradient-to-r from-primary-600 via-violet-500 to-sky-500"></div>

                <div class="p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-gray-900">E-Tiket Anda</h3>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold
                            {{ $registration->payment_status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $registration->payment_status === 'paid' ? 'bg-green-500' : 'bg-yellow-500' }}"></span>
                            {{ $registration->status_label }}
                        </span>
                    </div>

                    {{-- Dotted divider --}}
                    <div class="border-t border-dashed border-gray-200 my-4 relative">
                        <span class="absolute -left-5 -top-2.5 w-5 h-5 rounded-full bg-gray-50 border border-gray-200"></span>
                        <span class="absolute -right-5 -top-2.5 w-5 h-5 rounded-full bg-gray-50 border border-gray-200"></span>
                    </div>

                    {{-- Ticket code --}}
                    <div class="text-center mb-4" x-data="{ copied: false }">
                        <p class="text-[10px] text-gray-400 uppercase font-semibold tracking-widest mb-1.5">Kode Tiket</p>
                        <div class="bg-gray-900 rounded-xl px-4 py-3 inline-block w-full">
                            <p class="text-lg font-mono font-bold text-white tracking-widest">
                                {{ $registration->ticket_code }}
                            </p>
                        </div>
                        <button @click="navigator.clipboard.writeText('{{ $registration->ticket_code }}'); copied = true; setTimeout(() => copied = false, 2500)"
                                class="mt-2 inline-flex items-center gap-1.5 text-xs font-medium transition-colors"
                                :class="copied ? 'text-green-600' : 'text-gray-400 hover:text-gray-600'">
                            <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            <svg x-show="copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span x-text="copied ? 'Kode disalin!' : 'Salin kode'"></span>
                        </button>
                    </div>

                    {{-- Dotted divider --}}
                    <div class="border-t border-dashed border-gray-200 my-4 relative">
                        <span class="absolute -left-5 -top-2.5 w-5 h-5 rounded-full bg-gray-50 border border-gray-200"></span>
                        <span class="absolute -right-5 -top-2.5 w-5 h-5 rounded-full bg-gray-50 border border-gray-200"></span>
                    </div>

                    {{-- Registered at --}}
                    <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                        <span>Terdaftar pada</span>
                        <span class="font-semibold text-gray-700">
                            {{ $registration->registered_at?->translatedFormat('d M Y, H:i') ?? '-' }} WIB
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-xs text-gray-500">
                        <span>Pemegang tiket</span>
                        <span class="font-semibold text-gray-700">{{ auth()->user()->name }}</span>
                    </div>
                </div>
            </div>

            {{-- ══ QR CODE CARD ═══════════════════════════════════════════════ --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="text-sm font-bold text-gray-900 mb-1 text-center">QR Code Kehadiran</h3>
                <p class="text-xs text-gray-400 text-center mb-4">
                    {{ $bootcamp->type === 'offline' ? 'Tunjukkan QR ini kepada panitia saat check-in' : 'QR unik sebagai identitas peserta' }}
                </p>

                <div class="flex items-center justify-center">
                    <div class="bg-white border-2 border-gray-200 rounded-2xl p-3 shadow-sm inline-block">
                        <img src="{{ $qrUrl }}"
                             alt="QR Code {{ $registration->ticket_code }}"
                             class="w-48 h-48 rounded-lg"
                             loading="lazy">
                    </div>
                </div>

                <p class="text-[10px] text-gray-400 text-center mt-3 font-mono">
                    {{ $registration->ticket_code }}
                </p>

                {{-- Download QR Button --}}
                <a href="{{ $qrUrl }}&format=png&size=500x500"
                   download="QR-{{ $registration->ticket_code }}.png"
                   target="_blank"
                   class="mt-3 w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl
                          bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Unduh QR Code
                </a>
            </div>

            {{-- ══ BACK TO BOOTCAMP PAGE ═══════════════════════════════════════ --}}
            <a href="{{ route('bootcamps.show', $bootcamp->slug) }}"
               class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl
                      border border-gray-200 bg-white hover:bg-gray-50 text-gray-600 text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                Lihat Halaman Bootcamp
            </a>

        </div>
    </div>

</div>
@endsection
