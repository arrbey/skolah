@extends('layouts.dashboard')

@section('title', 'Bootcamp Saya')

@section('page-header')
    <h1 class="text-lg font-bold text-gray-900">Bootcamp Saya</h1>
@endsection

@section('content')
<div class="space-y-6">

    {{-- ═══ FILTER TABS ═══════════════════════════════════════════════════════ --}}
    <div class="flex items-center gap-2 flex-wrap">
        @foreach([
            ['key' => 'all',       'label' => 'Semua',     'count' => $stats['all']],
            ['key' => 'upcoming',  'label' => 'Mendatang', 'count' => $stats['upcoming']],
            ['key' => 'completed', 'label' => 'Selesai',   'count' => $stats['completed']],
        ] as $tab)
            <a href="{{ route('dashboard.my-bootcamps', ['filter' => $tab['key']]) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold transition-colors
                      {{ $filter === $tab['key']
                          ? 'bg-primary-600 text-white shadow-sm'
                          : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' }}">
                {{ $tab['label'] }}
                <span class="text-xs px-1.5 py-0.5 rounded-full
                      {{ $filter === $tab['key'] ? 'bg-white/20' : 'bg-gray-100 text-gray-500' }}">
                    {{ $tab['count'] }}
                </span>
            </a>
        @endforeach
    </div>

    {{-- ═══ BOOTCAMP CARDS ════════════════════════════════════════════════════ --}}
    @if($registrations->isNotEmpty())
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($registrations as $reg)
                @php $bootcamp = $reg->bootcamp; @endphp
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-md transition-shadow group">
                    {{-- Thumbnail --}}
                    <a href="{{ route('dashboard.my-bootcamp-detail', $reg->ticket_code) }}" class="block relative">
                        <img src="{{ $bootcamp->thumbnail_url }}" alt="{{ $bootcamp->title }}"
                             class="w-full h-40 object-cover group-hover:scale-105 transition-transform duration-300">
                        <div class="absolute top-2 left-2 flex items-center gap-1.5">
                            <span class="px-2 py-1 rounded-lg text-xs font-bold
                                {{ $bootcamp->type === 'online' ? 'bg-sky-500/90 text-white' : 'bg-amber-500/90 text-white' }}">
                                {{ $bootcamp->type === 'online' ? '🌐 Online' : '📍 Offline' }}
                            </span>
                            <span class="px-2 py-1 rounded-lg text-xs font-bold
                                @if($bootcamp->status === 'upcoming') bg-blue-500/90 text-white
                                @elseif($bootcamp->status === 'ongoing') bg-green-500/90 text-white
                                @else bg-gray-500/90 text-white @endif">
                                {{ $bootcamp->status_label }}
                            </span>
                        </div>
                    </a>

                    <div class="p-4">
                        {{-- Title --}}
                        <a href="{{ route('dashboard.my-bootcamp-detail', $reg->ticket_code) }}">
                            <h3 class="text-sm font-bold text-gray-900 mb-1 line-clamp-2 group-hover:text-primary-600 transition-colors">
                                {{ $bootcamp->title }}
                            </h3>
                        </a>
                        <p class="text-xs text-gray-500 mb-3">{{ $bootcamp->instructor->name ?? '-' }}</p>

                        {{-- Details --}}
                        <div class="space-y-1.5 mb-4">
                            <div class="flex items-center gap-2 text-xs text-gray-600">
                                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ tanggal_singkat_indo($bootcamp->start_date) }}
                                @if($bootcamp->end_date)
                                    — {{ tanggal_singkat_indo($bootcamp->end_date) }}
                                @endif
                            </div>
                            <div class="flex items-center gap-2 text-xs text-gray-600">
                                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                {{ $bootcamp->platform_label }}
                            </div>
                        </div>

                        {{-- Ticket info + CTA --}}
                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <p class="text-[10px] text-gray-400 uppercase font-semibold tracking-wider">Kode Tiket</p>
                                    <p class="text-sm font-mono font-bold text-gray-900">{{ $reg->ticket_code }}</p>
                                </div>
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold
                                    @if($reg->payment_status === 'paid') bg-green-50 text-green-700
                                    @elseif($reg->payment_status === 'pending') bg-yellow-50 text-yellow-700
                                    @else bg-red-50 text-red-700 @endif">
                                    {{ $reg->status_label }}
                                </span>
                            </div>
                            <a href="{{ route('dashboard.my-bootcamp-detail', $reg->ticket_code) }}"
                               class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg
                                      bg-primary-600 hover:bg-primary-700 text-white text-xs font-semibold transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                                </svg>
                                Lihat Tiket &amp; Detail
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $registrations->withQueryString()->links() }}
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                @include('layouts.partials.icon', ['name' => 'academic-cap', 'class' => 'w-8 h-8 text-gray-400'])
            </div>
            <h3 class="text-base font-bold text-gray-900 mb-1">Belum Ada Bootcamp</h3>
            <p class="text-sm text-gray-500 mb-4">Kamu belum terdaftar di bootcamp manapun.</p>
            <a href="{{ route('bootcamps.index') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary-600 text-white text-sm font-semibold hover:bg-primary-700 transition-colors">
                Cari Bootcamp
            </a>
        </div>
    @endif

</div>
@endsection
