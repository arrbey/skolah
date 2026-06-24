{{--
    Register Card Partial
    Variables: $bootcamp, $isRegistered, $userTicket, $pendingRegistration, $slotsLeft, $slotPercent
--}}
<div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden">

    {{-- Thumbnail Preview --}}
    <div class="relative aspect-video overflow-hidden">
        <img
            src="{{ $bootcamp->thumbnail_url }}"
            alt="{{ $bootcamp->title }}"
            class="w-full h-full object-cover"
        >
        <div class="absolute inset-0 bg-gradient-to-t from-gray-900/80 to-transparent"></div>

        {{-- Status Badge --}}
        <div class="absolute top-3 left-3">
            @php
                $statusColor = match($bootcamp->status) {
                    'upcoming' => 'bg-primary-600',
                    'ongoing'  => 'bg-green-600',
                    default    => 'bg-gray-600',
                };
            @endphp
            <span class="inline-flex items-center gap-1.5 text-xs font-bold text-white {{ $statusColor }} px-2.5 py-1 rounded-full">
                <span class="w-1.5 h-1.5 rounded-full bg-white {{ $bootcamp->status === 'ongoing' ? 'animate-pulse' : '' }}"></span>
                {{ $bootcamp->status_label }}
            </span>
        </div>
    </div>

    <div class="p-5">

        {{-- ── JIKA SUDAH TERDAFTAR ──────────────────────────────────────── --}}
        @if ($isRegistered && $userTicket)
            <div class="text-center py-2 mb-4">
                <div class="w-14 h-14 rounded-full bg-green-900/30 border-2 border-green-600 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-green-400 font-bold text-lg">Kamu Sudah Terdaftar!</p>
                <p class="text-gray-400 text-sm mt-1">Tiket kamu sudah siap.</p>
            </div>

            {{-- Ticket Box --}}
            <div class="bg-gray-800 border border-dashed border-gray-600 rounded-xl p-4 mb-4">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-1">Kode Tiket</p>
                <div class="flex items-center justify-between">
                    <p class="font-mono font-bold text-white text-lg tracking-widest">{{ $userTicket->ticket_code }}</p>
                    <button
                        onclick="navigator.clipboard.writeText('{{ $userTicket->ticket_code }}').then(() => { this.textContent = '✓'; setTimeout(() => this.textContent = 'Salin', 1500) })"
                        class="text-xs font-semibold text-primary-400 hover:text-primary-300 border border-primary-800/50 px-3 py-1.5 rounded-lg transition"
                    >Salin</button>
                </div>
            </div>

            @if ($bootcamp->meeting_link && $bootcamp->status === 'ongoing')
                <a
                    href="{{ $bootcamp->meeting_link }}"
                    target="_blank"
                    rel="noopener"
                    class="w-full inline-flex items-center justify-center gap-2 text-sm font-bold text-white bg-green-600 hover:bg-green-700 transition py-3.5 rounded-xl"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.868v6.264a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    Bergabung Sekarang
                </a>
            @else
                <div class="text-center text-sm text-gray-500 py-2">
                    {{ $bootcamp->status === 'upcoming'
                        ? 'Link meeting akan tersedia saat bootcamp dimulai.'
                        : 'Bootcamp ini telah selesai.' }}
                </div>
            @endif

        {{-- ── PENDING PAYMENT ──────────────────────────────────────────── --}}
        @elseif ($pendingRegistration)
            <div class="bg-yellow-900/20 border border-yellow-700/40 rounded-xl p-4 mb-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-yellow-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>
                        <p class="text-yellow-400 font-semibold text-sm">Menunggu Pembayaran</p>
                        <p class="text-gray-400 text-xs mt-0.5">Selesaikan pembayaran untuk mengkonfirmasi pendaftaranmu.</p>
                    </div>
                </div>
            </div>
            <a
                href="{{ route('dashboard.orders') }}"
                class="w-full inline-flex items-center justify-center gap-2 text-sm font-bold text-white bg-yellow-600 hover:bg-yellow-700 transition py-3.5 rounded-xl"
            >
                Selesaikan Pembayaran
            </a>

        {{-- ── BELUM TERDAFTAR ──────────────────────────────────────────── --}}
        @else
            {{-- Price --}}
            <div class="mb-4">
                @if ($bootcamp->has_discount)
                    <div class="flex items-baseline gap-2 mb-0.5">
                        <span class="text-3xl font-black text-white">{{ $bootcamp->effective_price_formatted }}</span>
                        <span class="text-sm text-gray-500 line-through">{{ $bootcamp->price_formatted }}</span>
                        <x-badge color="danger" size="sm">
                            -{{ round((($bootcamp->price - $bootcamp->effective_price) / $bootcamp->price) * 100) }}%
                        </x-badge>
                    </div>
                @elseif ($bootcamp->price === 0)
                    <span class="text-3xl font-black text-green-400">Gratis</span>
                @else
                    <span class="text-3xl font-black text-white">{{ $bootcamp->price_formatted }}</span>
                @endif
            </div>

            {{-- Slot Progress --}}
            @if ($bootcamp->max_participants > 0)
                <div class="mb-5">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-xs text-gray-400">Slot tersisa</span>
                        <span class="text-xs font-bold {{ $slotsLeft <= 5 ? 'text-red-400' : 'text-white' }}">
                            {{ $slotsLeft <= 0 ? 'PENUH' : $slotsLeft . ' dari ' . $bootcamp->max_participants }}
                        </span>
                    </div>
                    <div class="h-2 bg-gray-700 rounded-full overflow-hidden">
                        <div
                            class="h-full rounded-full transition-all {{ $slotPercent >= 90 ? 'bg-red-500' : ($slotPercent >= 70 ? 'bg-yellow-500' : 'bg-primary-500') }}"
                            style="width: {{ $slotPercent }}%"
                        ></div>
                    </div>
                    @if ($slotsLeft <= 5 && $slotsLeft > 0)
                        <p class="text-xs text-red-400 font-semibold mt-1.5 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            Hanya tersisa {{ $slotsLeft }} slot!
                        </span>
                    @endif
                </div>
            @endif

            {{-- CTA Button --}}
            @if ($bootcamp->is_full)
                <button disabled class="w-full inline-flex items-center justify-center gap-2 text-sm font-bold text-gray-400 bg-gray-700 cursor-not-allowed py-3.5 rounded-xl mb-4">
                    Pendaftaran Penuh
                </button>
            @elseif ($bootcamp->status === 'completed')
                <button disabled class="w-full inline-flex items-center justify-center gap-2 text-sm font-bold text-gray-400 bg-gray-700 cursor-not-allowed py-3.5 rounded-xl mb-4">
                    Bootcamp Sudah Selesai
                </button>
            @elseif (auth()->check())
                <form action="{{ route('bootcamp.checkout.process') }}" method="POST">
                    @csrf
                    <input type="hidden" name="bootcamp_id" value="{{ $bootcamp->id }}">
                    <button
                        type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 text-sm font-bold text-white bg-gradient-to-r from-primary-600 to-secondary-600 hover:from-primary-500 hover:to-secondary-500 transition py-3.5 rounded-xl shadow-lg shadow-primary-900/30 mb-4"
                    >
                        @if ($bootcamp->price === 0)
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Daftar Gratis Sekarang
                        @else
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Daftar Sekarang
                        @endif
                    </button>
                </form>
            @else
                <a
                    href="{{ route('login') }}?redirect={{ urlencode(request()->fullUrl()) }}"
                    class="w-full inline-flex items-center justify-center gap-2 text-sm font-bold text-white bg-gradient-to-r from-primary-600 to-secondary-600 hover:from-primary-500 hover:to-secondary-500 transition py-3.5 rounded-xl shadow-lg shadow-primary-900/30 mb-4"
                >
                    Login untuk Mendaftar
                </a>
            @endif
        @endif

        {{-- ── Info Details ──────────────────────────────────────────────── --}}
        <div class="space-y-2.5 pt-4 border-t border-gray-800">
            @if ($bootcamp->start_date)
                <div class="flex items-center gap-3 text-sm">
                    <svg class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span class="text-gray-400">{{ $bootcamp->start_date_formatted }}</span>
                </div>
            @endif
            @if ($bootcamp->end_date)
                <div class="flex items-center gap-3 text-sm">
                    <svg class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-gray-400">Selesai: {{ $bootcamp->end_date_formatted }}</span>
                </div>
            @endif
            <div class="flex items-center gap-3 text-sm">
                <svg class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <span class="text-gray-400">{{ $bootcamp->platform_label }}</span>
            </div>
            @if ($bootcamp->type === 'offline' && $bootcamp->location)
                <div class="flex items-start gap-3 text-sm">
                    <svg class="w-4 h-4 text-gray-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span class="text-gray-400">{{ $bootcamp->location }}</span>
                </div>
            @endif
            <div class="flex items-center gap-3 text-sm">
                <svg class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span class="text-gray-400">
                    {{ $bootcamp->total_registered }} peserta terdaftar
                    @if ($bootcamp->max_participants > 0)
                        / max {{ $bootcamp->max_participants }}
                    @endif
                </span>
            </div>
        </div>

    </div>
</div>
