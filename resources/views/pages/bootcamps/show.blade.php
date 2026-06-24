@extends('layouts.app')

@php
    $jsonLd = [
        '@context' => 'https://schema.org',
        '@type' => 'Event',
        'name' => $bootcamp->title,
        'description' => Str::limit(strip_tags($bootcamp->description), 200),
        'image' => $bootcamp->thumbnail_url,
        'startDate' => $bootcamp->start_date?->toIso8601String(),
        'endDate' => $bootcamp->end_date?->toIso8601String(),
        'eventStatus' => 'https://schema.org/EventScheduled',
        'eventAttendanceMode' => $bootcamp->type === 'online'
            ? 'https://schema.org/OnlineEventAttendanceMode'
            : 'https://schema.org/OfflineEventAttendanceMode',
        'location' => ($bootcamp->type === 'offline' && $bootcamp->location)
            ? ['@type' => 'Place', 'name' => $bootcamp->location]
            : ['@type' => 'VirtualLocation', 'url' => config('app.url')],
        'organizer' => [
            '@type' => 'Organization',
            'name' => \App\Models\Setting::get('site_name', 'Skolah.com'),
            'url' => config('app.url'),
        ],
        'offers' => [
            '@type' => 'Offer',
            'price' => (string) $bootcamp->effective_price,
            'priceCurrency' => 'IDR',
            'availability' => $bootcamp->is_full ? 'https://schema.org/SoldOut' : 'https://schema.org/InStock',
            'url' => route('bootcamps.show', $bootcamp->slug),
        ],
    ];
@endphp

@push('head')
{{-- ── JSON-LD: Event Schema ──────────────────────────────────────────────── --}}
<script type="application/ld+json" nonce="{{ $cspNonce ?? '' }}">
{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endpush

@section('content')

{{-- ═══════════════════════════════════════════════════════════════════════════
     HERO SECTION — Cover + Title + Countdown
═══════════════════════════════════════════════════════════════════════════ --}}
<section class="relative bg-gray-950 pt-24 pb-0 overflow-hidden">

    {{-- BG decorations --}}
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-0 left-0 right-0 h-2/3">
            <img src="{{ $bootcamp->thumbnail_url }}" alt="" class="w-full h-full object-cover opacity-10 blur-sm scale-105">
            <div class="absolute inset-0 bg-gradient-to-b from-gray-950/60 via-gray-950/80 to-gray-950"></div>
        </div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <x-breadcrumb :items="[
            ['label' => 'Bootcamp & Webinar', 'url' => route('bootcamps.index')],
            ['label' => Str::limit($bootcamp->title, 40)],
        ]" class="mb-6" />

        <div class="grid lg:grid-cols-3 gap-8 pb-12">
            {{-- Left: Info --}}
            <div class="lg:col-span-2">

                {{-- Badges --}}
                <div class="flex flex-wrap gap-2 mb-4">
                    <x-badge color="{{ $bootcamp->status === 'upcoming' ? 'primary' : ($bootcamp->status === 'ongoing' ? 'success' : 'gray') }}">
                        {{ $bootcamp->status_label }}
                    </x-badge>
                    <x-badge color="{{ $bootcamp->type === 'online' ? 'info' : 'warning' }}">
                        {{ $bootcamp->type === 'online' ? '🌐 Online' : '📍 Offline' }}
                    </x-badge>
                    @if ($bootcamp->price === 0)
                        <x-badge color="success">🎁 Gratis</x-badge>
                    @endif
                    @if ($bootcamp->is_full)
                        <x-badge color="danger">Penuh</x-badge>
                    @endif
                </div>

                <h1 class="text-3xl lg:text-4xl xl:text-5xl font-bold text-white leading-tight mb-4">
                    {{ $bootcamp->title }}
                </h1>

                <p class="text-lg text-gray-400 leading-relaxed mb-6 max-w-2xl">
                    {{ Str::limit(strip_tags($bootcamp->description), 200) }}
                </p>

                {{-- Stats Strip --}}
                <div class="flex flex-wrap gap-5 text-sm mb-8">
                    @if ($bootcamp->instructor)
                        <div class="flex items-center gap-2">
                            <x-picture
                                :src="$bootcamp->instructor->avatar ? storageUrl($bootcamp->instructor->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($bootcamp->instructor->name) . '&background=6C63FF&color=fff&size=60'"
                                :alt="$bootcamp->instructor->name"
                                class="w-6 h-6 rounded-full" />
                            <span class="text-gray-400">oleh <span class="text-white font-semibold">{{ $bootcamp->instructor->name }}</span></span>
                        </div>
                    @endif
                    <div class="flex items-center gap-1.5 text-gray-400">
                        <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        {{ number_format($bootcamp->total_registered) }} peserta
                    </div>
                    @if ($bootcamp->start_date)
                        <div class="flex items-center gap-1.5 text-gray-400">
                            <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            {{ $bootcamp->start_date->translatedFormat('d F Y') }}
                        </div>
                    @endif
                </div>

                {{-- ── COUNTDOWN TIMER ──────────────────────────────────────── --}}
                @if ($countdownTarget && $bootcamp->status === 'upcoming')
                    <div
                        class="inline-flex flex-wrap gap-4 bg-gray-900/60 backdrop-blur border border-gray-800 rounded-2xl px-6 py-4 mb-8"
                        x-data="countdown('{{ $countdownTarget }}')"
                    >
                        <div class="text-center min-w-[52px]">
                            <p class="text-3xl font-black text-white tabular-nums" x-text="String(days).padStart(2, '0')">00</p>
                            <p class="text-xs text-gray-500 uppercase tracking-wider mt-0.5">Hari</p>
                        </div>
                        <div class="text-2xl text-gray-600 self-start pt-1">:</div>
                        <div class="text-center min-w-[52px]">
                            <p class="text-3xl font-black text-primary-400 tabular-nums" x-text="String(hours).padStart(2, '0')">00</p>
                            <p class="text-xs text-gray-500 uppercase tracking-wider mt-0.5">Jam</p>
                        </div>
                        <div class="text-2xl text-gray-600 self-start pt-1">:</div>
                        <div class="text-center min-w-[52px]">
                            <p class="text-3xl font-black text-secondary-400 tabular-nums" x-text="String(minutes).padStart(2, '0')">00</p>
                            <p class="text-xs text-gray-500 uppercase tracking-wider mt-0.5">Menit</p>
                        </div>
                        <div class="text-2xl text-gray-600 self-start pt-1">:</div>
                        <div class="text-center min-w-[52px]">
                            <p class="text-3xl font-black text-amber-400 tabular-nums" x-text="String(seconds).padStart(2, '0')">00</p>
                            <p class="text-xs text-gray-500 uppercase tracking-wider mt-0.5">Detik</p>
                        </div>
                        <div class="self-center ml-2 text-sm text-gray-400">hingga mulai</div>
                    </div>
                @endif

            </div>

            {{-- Right: Register Card (Desktop sticky — show inline here, sticky via CSS) --}}
            <div class="hidden lg:block">
                <div class="sticky top-24">
                    @include('pages.bootcamps.partials.register-card')
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════════════
     MAIN CONTENT
═══════════════════════════════════════════════════════════════════════════ --}}
<section class="bg-gray-950 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-3 gap-10">

            {{-- ── LEFT COLUMN: Description, What You'll Learn, Schedule ── --}}
            <div class="lg:col-span-2 space-y-10">

                {{-- Flash Messages --}}
                @if (session('warning'))
                    <x-alert type="warning" :message="session('warning')" dismissible />
                @endif
                @if (session('success'))
                    <x-alert type="success" :message="session('success')" dismissible />
                @endif
                @if (session('error'))
                    <x-alert type="error" :message="session('error')" dismissible />
                @endif

                {{-- ── Deskripsi ────────────────────────────────────────── --}}
                <div x-data="{ expanded: false }">
                    <h2 class="text-2xl font-bold text-white mb-4">Tentang Bootcamp Ini</h2>
                    <div
                        :class="expanded ? '' : 'max-h-48 overflow-hidden'"
                        class="relative"
                    >
                        <div class="prose prose-invert prose-sm max-w-none text-gray-300 leading-relaxed">
                            {!! nl2br(e($bootcamp->description)) !!}
                        </div>
                        <div
                            x-show="!expanded"
                            class="absolute bottom-0 left-0 right-0 h-16 bg-gradient-to-t from-gray-950 to-transparent"
                        ></div>
                    </div>
                    <button
                        @click="expanded = !expanded"
                        class="mt-3 text-sm font-semibold text-primary-400 hover:text-primary-300 transition flex items-center gap-1"
                    >
                        <span x-text="expanded ? 'Tampilkan Lebih Sedikit' : 'Baca Selengkapnya'">Baca Selengkapnya</span>
                        <svg :class="expanded ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                </div>

                {{-- ── Jadwal & Detail ─────────────────────────────────── --}}
                <div>
                    <h2 class="text-2xl font-bold text-white mb-5">Jadwal & Detail</h2>
                    <div class="grid sm:grid-cols-2 gap-4">

                        @php
                            $scheduleItems = array_filter([
                                $bootcamp->start_date ? [
                                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
                                    'label' => 'Tanggal Mulai',
                                    'value' => $bootcamp->start_date_formatted,
                                    'color' => 'text-primary-400',
                                ] : null,
                                $bootcamp->end_date ? [
                                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                                    'label' => 'Tanggal Selesai',
                                    'value' => $bootcamp->end_date_formatted,
                                    'color' => 'text-secondary-400',
                                ] : null,
                                [
                                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>',
                                    'label' => 'Platform',
                                    'value' => $bootcamp->platform_label,
                                    'color' => 'text-green-400',
                                ],
                                [
                                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-5 5a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>',
                                    'label' => 'Tipe',
                                    'value' => $bootcamp->type === 'online' ? 'Online' : 'Offline',
                                    'color' => 'text-amber-400',
                                ],
                                $bootcamp->max_participants > 0 ? [
                                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>',
                                    'label' => 'Kapasitas',
                                    'value' => $bootcamp->total_registered . ' / ' . $bootcamp->max_participants . ' peserta',
                                    'color' => 'text-pink-400',
                                ] : null,
                                ($bootcamp->type === 'offline' && $bootcamp->location) ? [
                                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>',
                                    'label' => 'Lokasi',
                                    'value' => $bootcamp->location,
                                    'color' => 'text-orange-400',
                                ] : null,
                            ]);
                        @endphp

                        @foreach ($scheduleItems as $item)
                            <div class="flex items-start gap-4 bg-gray-900 border border-gray-800 rounded-xl p-4">
                                <div class="w-10 h-10 rounded-xl bg-gray-800 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 {{ $item['color'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        {!! $item['icon'] !!}
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide">{{ $item['label'] }}</p>
                                    <p class="text-sm font-semibold text-white mt-0.5">{{ $item['value'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- ── Instruktur ────────────────────────────────────────── --}}
                @if ($bootcamp->instructor)
                    <div>
                        <h2 class="text-2xl font-bold text-white mb-5">Instruktur</h2>
                        <div class="flex items-start gap-5 bg-gray-900 border border-gray-800 rounded-2xl p-5">
                            <x-picture
                                :src="$bootcamp->instructor->avatar ? storageUrl($bootcamp->instructor->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($bootcamp->instructor->name) . '&background=6C63FF&color=fff&size=160'"
                                :alt="$bootcamp->instructor->name"
                                class="w-20 h-20 rounded-2xl object-cover flex-shrink-0" />
                            <div class="min-w-0">
                                <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-0.5">Instruktur</p>
                                <h3 class="text-lg font-bold text-white">{{ $bootcamp->instructor->name }}</h3>
                                @if ($bootcamp->instructor->bio)
                                    <p class="text-gray-400 text-sm mt-2 leading-relaxed line-clamp-4">
                                        {{ $bootcamp->instructor->bio }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                {{-- ── Related Bootcamps ─────────────────────────────────── --}}
                @if ($relatedBootcamps->isNotEmpty())
                    <div>
                        <h2 class="text-2xl font-bold text-white mb-5">Bootcamp Lainnya</h2>
                        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach ($relatedBootcamps as $related)
                                <x-bootcamp-card :bootcamp="$related" />
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>

            {{-- ── RIGHT COLUMN: Register Card (Desktop only, sticky) ─── --}}
            <div class="hidden lg:block">
                {{-- This is the second sticky card — hidden on mobile (shown inline above) --}}
            </div>

        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════════════
     MOBILE STICKY BOTTOM BAR
═══════════════════════════════════════════════════════════════════════════ --}}
<div
    class="fixed bottom-0 left-0 right-0 z-40 lg:hidden bg-gray-900/95 backdrop-blur-sm border-t border-gray-800 px-4 py-3"
    x-data="{ showCard: false }"
>
    <div x-show="!showCard" class="flex items-center justify-between gap-3">
        <div>
            @if ($bootcamp->has_discount)
                <div class="flex items-baseline gap-1.5">
                    <span class="text-xl font-black text-white">{{ $bootcamp->effective_price_formatted }}</span>
                    <span class="text-xs text-gray-500 line-through">{{ $bootcamp->price_formatted }}</span>
                </div>
            @else
                <span class="text-xl font-black {{ $bootcamp->price === 0 ? 'text-green-400' : 'text-white' }}">
                    {{ $bootcamp->price === 0 ? 'Gratis' : $bootcamp->price_formatted }}
                </span>
            @endif
            @if ($slotsLeft !== null && $slotsLeft <= 10 && $slotsLeft > 0)
                <p class="text-xs text-red-400 font-semibold">Sisa {{ $slotsLeft }} slot!</p>
            @endif
        </div>

        @if ($isRegistered)
            <span class="inline-flex items-center gap-2 text-sm font-bold text-green-400 border border-green-700 px-5 py-2.5 rounded-xl">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                Sudah Terdaftar
            </span>
        @elseif ($bootcamp->is_full || $bootcamp->status === 'completed')
            <button disabled class="text-sm font-bold text-gray-400 bg-gray-700 cursor-not-allowed px-5 py-2.5 rounded-xl">
                {{ $bootcamp->is_full ? 'Penuh' : 'Selesai' }}
            </button>
        @else
            <button @click="showCard = true" class="text-sm font-bold text-white bg-gradient-to-r from-primary-600 to-secondary-600 px-6 py-2.5 rounded-xl shadow-lg">
                Daftar Sekarang
            </button>
        @endif
    </div>

    {{-- Full Card Slide-up --}}
    <div
        x-show="showCard"
        x-transition:enter="transition transform duration-300"
        x-transition:enter-start="translate-y-full"
        x-transition:enter-end="translate-y-0"
        style="display: none;"
        class="fixed inset-0 z-50 bg-black/60 flex items-end"
    >
        <div class="w-full bg-gray-900 rounded-t-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between p-4 border-b border-gray-800">
                <h3 class="font-bold text-white">Detail Pendaftaran</h3>
                <button @click="showCard = false" class="text-gray-400 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-4">
                @include('pages.bootcamps.partials.register-card')
            </div>
        </div>
    </div>
</div>

{{-- Alpine countdown component --}}
@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
document.addEventListener('alpine:init', () => {
    Alpine.data('countdown', (targetDate) => ({
        days: 0, hours: 0, minutes: 0, seconds: 0,
        interval: null,
        init() {
            this.tick();
            this.interval = setInterval(() => this.tick(), 1000);
        },
        tick() {
            const diff = new Date(targetDate) - new Date();
            if (diff <= 0) {
                clearInterval(this.interval);
                this.days = this.hours = this.minutes = this.seconds = 0;
                return;
            }
            this.days    = Math.floor(diff / 86400000);
            this.hours   = Math.floor((diff % 86400000) / 3600000);
            this.minutes = Math.floor((diff % 3600000) / 60000);
            this.seconds = Math.floor((diff % 60000) / 1000);
        },
        destroy() { clearInterval(this.interval); }
    }));
});
</script>
@endpush

@endsection
