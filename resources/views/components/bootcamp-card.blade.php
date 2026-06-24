@props(['bootcamp'])

@php
    $typeMap   = [
        'online'  => ['Online',  'bg-sky-100 text-sky-700'],
        'offline' => ['Offline', 'bg-emerald-100 text-emerald-700'],
        'hybrid'  => ['Hybrid',  'bg-violet-100 text-violet-700'],
    ];
    [$typeLabel, $typeClass] = $typeMap[$bootcamp->type ?? 'online'] ?? ['Online', 'bg-sky-100 text-sky-700'];

    $statusMap = [
        'upcoming'  => ['Segera',      'bg-amber-100 text-amber-700'],
        'ongoing'   => ['Berlangsung', 'bg-green-100 text-green-700'],
        'completed' => ['Selesai',     'bg-gray-100 text-gray-500'],
    ];
    [$statusLabel, $statusClass] = $statusMap[$bootcamp->status ?? 'upcoming'] ?? ['Segera', 'bg-amber-100 text-amber-700'];

    $slotsLeft = ($bootcamp->max_participants ?? 0) - ($bootcamp->total_registered ?? 0);
    $slotPct   = $bootcamp->max_participants ? min(100, round(($bootcamp->total_registered / $bootcamp->max_participants) * 100)) : 0;
    $isFull    = $bootcamp->max_participants && $slotsLeft <= 0;
@endphp

<div class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 border border-gray-100 flex flex-col"
     x-data="{
         endDate: '{{ optional($bootcamp->start_date)->toIso8601String() ?? '' }}',
         days: 0, hours: 0, minutes: 0, seconds: 0,
         interval: null,
         init() {
             if (!this.endDate) return;
             this.interval = setInterval(() => {
                 const diff = Math.max(0, new Date(this.endDate) - new Date());
                 this.days    = Math.floor(diff / 86400000);
                 this.hours   = Math.floor((diff % 86400000) / 3600000);
                 this.minutes = Math.floor((diff % 3600000) / 60000);
                 this.seconds = Math.floor((diff % 60000) / 1000);
             }, 1000);
         }
     }">

    {{-- Cover --}}
    <a href="{{ route('bootcamps.show', $bootcamp->slug) }}" class="relative overflow-hidden block">
        @if($bootcamp->thumbnail)
            <x-picture
                :src="storageUrl($bootcamp->thumbnail)"
                :alt="$bootcamp->title"
                class="w-full aspect-[2/1] object-cover group-hover:scale-105 transition-transform duration-500" />
        @else
            <div class="w-full aspect-[2/1] bg-gradient-to-br from-primary-50 to-secondary-50 flex flex-col items-center justify-center gap-1.5">
                <span class="text-4xl">🎓</span>
                <span class="text-xs text-gray-400 font-medium">Bootcamp & Webinar</span>
            </div>
        @endif

        {{-- Overlay --}}
        <div class="absolute inset-0 bg-gradient-to-t from-black/10 to-transparent pointer-events-none"></div>

        <span class="absolute top-3 left-3 text-xs font-semibold px-2.5 py-1 rounded-full {{ $typeClass }} shadow-sm">
            {{ $typeLabel }}
        </span>
        <span class="absolute top-3 right-3 text-xs font-semibold px-2.5 py-1 rounded-full {{ $statusClass }} shadow-sm">
            {{ $statusLabel }}
        </span>

        {{-- Platform badge --}}
        @if($bootcamp->platform ?? false)
            <div class="absolute bottom-3 left-3 bg-black/60 text-white text-xs px-2.5 py-1 rounded-full backdrop-blur-sm">
                📍 {{ $bootcamp->platform }}
            </div>
        @endif
    </a>

    <div class="p-4 flex flex-col flex-1">
        {{-- Title --}}
        <a href="{{ route('bootcamps.show', $bootcamp->slug) }}"
           class="font-bold text-gray-900 text-sm leading-snug line-clamp-2 hover:text-primary-600 transition-colors">
            {{ $bootcamp->title }}
        </a>

        {{-- Instructor --}}
        @if(isset($bootcamp->instructor))
            <p class="text-xs text-gray-500 mt-1.5 truncate">
                oleh <span class="font-medium text-gray-700">{{ $bootcamp->instructor->name }}</span>
            </p>
        @endif

        {{-- Date --}}
        @if($bootcamp->start_date)
            <div class="flex items-center gap-1.5 mt-3 text-xs text-gray-500">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                {{ \Carbon\Carbon::parse($bootcamp->start_date)->locale('id')->translatedFormat('d F Y, H:i') }} WIB
            </div>
        @endif

        {{-- Countdown timer --}}
        @if(($bootcamp->status ?? '') === 'upcoming' && $bootcamp->start_date)
            <div class="mt-3 grid grid-cols-4 gap-1.5 text-center">
                @foreach([['days','Hari'],['hours','Jam'],['minutes','Menit'],['seconds','Detik']] as [$unit,$label])
                <div class="bg-primary-50 rounded-lg py-1.5">
                    <span class="block text-base font-extrabold text-primary-600" x-text="String({{ $unit }}).padStart(2,'0')">00</span>
                    <span class="block text-[10px] text-gray-400 mt-0.5">{{ $label }}</span>
                </div>
                @endforeach
            </div>
        @endif

        <div class="flex-1"></div>

        {{-- Slot remaining --}}
        @if($bootcamp->max_participants)
            <div class="mt-3">
                <div class="flex justify-between text-xs text-gray-500 mb-1">
                    <span>{{ $bootcamp->total_registered ?? 0 }} / {{ $bootcamp->max_participants }} peserta</span>
                    <span class="{{ $slotsLeft <= 10 ? 'text-amber-600 font-semibold' : '' }}">
                        {{ max(0,$slotsLeft) }} slot tersisa
                    </span>
                </div>
                <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-500 {{ $slotPct >= 80 ? 'bg-amber-500' : 'bg-primary-500' }}"
                         style="width:{{ $slotPct }}%"></div>
                </div>
            </div>
        @endif

        {{-- Price --}}
        <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100">
            <div>
                @if(($bootcamp->discount_price ?? 0) > 0)
                    <span class="text-base font-extrabold text-primary-600">{{ rupiah((int)$bootcamp->discount_price) }}</span>
                    <span class="ml-1.5 text-xs text-gray-400 line-through">{{ rupiah((int)$bootcamp->price) }}</span>
                @elseif(($bootcamp->price ?? 0) == 0)
                    <span class="text-base font-extrabold text-emerald-600">Gratis</span>
                @else
                    <span class="text-base font-extrabold text-primary-600">{{ rupiah((int)$bootcamp->price) }}</span>
                @endif
            </div>
            <a href="{{ route('bootcamps.show', $bootcamp->slug) }}"
               class="text-xs font-semibold text-white px-3 py-1.5 rounded-lg bg-primary-600 hover:bg-primary-700 transition-colors">
                {{ $isFull ? 'Lihat Detail' : 'Daftar' }}
            </a>
        </div>
    </div>
</div>
