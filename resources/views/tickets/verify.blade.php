<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Tiket — {{ \App\Models\Setting::get('site_name', 'Skolah.com') }}</title>
    @vite(['resources/css/app.css'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-gray-50 flex flex-col">

    {{-- Navbar minimal --}}
    <header class="bg-white border-b border-gray-200 px-4 py-3">
        <div class="max-w-lg mx-auto flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <div class="w-7 h-7 bg-blue-600 rounded-lg flex items-center justify-center">
                    <span class="text-white text-xs font-bold">S</span>
                </div>
                <span class="text-sm font-bold text-gray-900">{{ \App\Models\Setting::get('site_name', 'Skolah.com') }}</span>
            </a>
            <span class="text-xs text-gray-400 font-medium">Sistem Verifikasi Tiket</span>
        </div>
    </header>

    <main class="flex-1 flex items-start justify-center px-4 py-10">
        <div class="w-full max-w-lg space-y-5">

            @php
                $config = match($status) {
                    'valid'            => ['color' => 'green',  'icon' => '✓', 'bg' => 'bg-green-500',  'ring' => 'ring-green-200',  'text' => 'text-green-800',  'badge_bg' => 'bg-green-50  border-green-200'],
                    'already_checked_in' => ['color' => 'blue', 'icon' => '✓', 'bg' => 'bg-blue-500',   'ring' => 'ring-blue-200',   'text' => 'text-blue-800',   'badge_bg' => 'bg-blue-50   border-blue-200'],
                    'invalid'          => ['color' => 'red',    'icon' => '✗', 'bg' => 'bg-red-500',    'ring' => 'ring-red-200',    'text' => 'text-red-800',    'badge_bg' => 'bg-red-50    border-red-200'],
                    'unpaid'           => ['color' => 'red',    'icon' => '✗', 'bg' => 'bg-red-500',    'ring' => 'ring-red-200',    'text' => 'text-red-800',    'badge_bg' => 'bg-red-50    border-red-200'],
                    default            => ['color' => 'gray',   'icon' => '?', 'bg' => 'bg-gray-500',   'ring' => 'ring-gray-200',   'text' => 'text-gray-700',   'badge_bg' => 'bg-gray-50   border-gray-200'],
                };
            @endphp

            {{-- Status Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">

                {{-- Top color strip --}}
                <div class="h-2 {{ $config['bg'] }}"></div>

                <div class="p-6 text-center">
                    {{-- Icon --}}
                    <div class="mx-auto mb-4 w-20 h-20 rounded-full {{ $config['bg'] }} ring-8 {{ $config['ring'] }}
                                flex items-center justify-center">
                        <span class="text-white text-4xl font-bold leading-none">{{ $config['icon'] }}</span>
                    </div>

                    {{-- Status label --}}
                    <div class="mb-2">
                        @if($status === 'valid')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-green-100 text-green-800 text-sm font-bold">
                                ✅ TIKET VALID
                            </span>
                        @elseif($status === 'already_checked_in')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-sm font-bold">
                                🔵 SUDAH CHECK-IN
                            </span>
                        @elseif($status === 'invalid')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-100 text-red-800 text-sm font-bold">
                                ❌ TIKET TIDAK VALID
                            </span>
                        @elseif($status === 'unpaid')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-100 text-red-800 text-sm font-bold">
                                ❌ BELUM LUNAS
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gray-100 text-gray-700 text-sm font-bold">
                                ℹ️ INFO
                            </span>
                        @endif
                    </div>

                    <p class="text-sm text-gray-600 mt-1">{{ $message }}</p>
                </div>
            </div>

            {{-- Session alerts --}}
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-start gap-3">
                    <span class="text-green-500 text-lg">✅</span>
                    <p class="text-sm font-semibold text-green-800">{{ session('success') }}</p>
                </div>
            @endif
            @if(session('warning'))
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 flex items-start gap-3">
                    <span class="text-yellow-500 text-lg">⚠️</span>
                    <p class="text-sm font-semibold text-yellow-800">{{ session('warning') }}</p>
                </div>
            @endif

            {{-- Detail tiket --}}
            @if($registration)
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    {{-- Thumbnail --}}
                    @if($registration->bootcamp?->thumbnail)
                        <x-picture
                            :src="$registration->bootcamp->thumbnail_url"
                            :alt="$registration->bootcamp->title"
                            class="w-full h-36 object-cover" />
                    @endif

                    <div class="p-5 space-y-4">
                        {{-- Event info --}}
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase font-semibold tracking-wider mb-0.5">Event</p>
                            <h2 class="text-base font-bold text-gray-900">{{ $registration->bootcamp?->title }}</h2>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="px-2 py-0.5 rounded-lg text-xs font-bold bg-amber-100 text-amber-800">
                                    📍 Offline
                                </span>
                                <span class="px-2 py-0.5 rounded-lg text-xs font-bold
                                    @if($registration->bootcamp?->status === 'upcoming') bg-blue-100 text-blue-800
                                    @elseif($registration->bootcamp?->status === 'ongoing') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-700 @endif">
                                    {{ $registration->bootcamp?->status_label }}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            {{-- Tanggal --}}
                            <div class="bg-gray-50 rounded-xl p-3">
                                <p class="text-[10px] text-gray-400 uppercase font-semibold tracking-wider mb-1">Tanggal</p>
                                <p class="text-xs font-semibold text-gray-800">
                                    {{ $registration->bootcamp?->start_date?->translatedFormat('d F Y') }}
                                </p>
                                <p class="text-[11px] text-gray-500">
                                    {{ $registration->bootcamp?->start_date?->format('H:i') }} WIB
                                </p>
                            </div>

                            {{-- Lokasi --}}
                            <div class="bg-gray-50 rounded-xl p-3">
                                <p class="text-[10px] text-gray-400 uppercase font-semibold tracking-wider mb-1">Lokasi</p>
                                <p class="text-xs font-semibold text-gray-800 line-clamp-2">
                                    {{ $registration->bootcamp?->location ?: 'Tatap Muka' }}
                                </p>
                            </div>
                        </div>

                        {{-- Divider dashed --}}
                        <div class="border-t border-dashed border-gray-200 relative">
                            <span class="absolute -left-5 -top-2.5 w-5 h-5 rounded-full bg-gray-50 border border-gray-200"></span>
                            <span class="absolute -right-5 -top-2.5 w-5 h-5 rounded-full bg-gray-50 border border-gray-200"></span>
                        </div>

                        {{-- Peserta --}}
                        <div class="flex items-center gap-3">
                            @if($registration->user?->avatar)
                                <x-picture
                                    :src="storageUrl($registration->user->avatar)"
                                    alt=""
                                    class="w-10 h-10 rounded-full object-cover border-2 border-gray-200" />
                            @else
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-sm">
                                    {{ strtoupper(substr($registration->user?->name ?? 'U', 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <p class="text-[10px] text-gray-400 uppercase font-semibold tracking-wider">Peserta</p>
                                <p class="text-sm font-bold text-gray-900">{{ $registration->user?->name }}</p>
                                <p class="text-xs text-gray-500">{{ $registration->user?->email }}</p>
                            </div>
                        </div>

                        {{-- Kode tiket --}}
                        <div class="bg-gray-900 rounded-xl px-4 py-3 text-center">
                            <p class="text-[10px] text-gray-400 uppercase tracking-widest mb-1">Kode Tiket</p>
                            <p class="text-base font-mono font-bold text-white tracking-widest">
                                {{ $registration->ticket_code }}
                            </p>
                        </div>

                        {{-- Check-in info jika sudah --}}
                        @if($registration->checked_in)
                            <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-blue-800">Check-in tercatat</p>
                                    <p class="text-[11px] text-blue-600">
                                        {{ $registration->checked_in_at?->translatedFormat('d F Y, H:i') }} WIB
                                    </p>
                                </div>
                            </div>
                        @endif

                        {{-- Tombol Check-In (hanya untuk admin, tiket valid, offline) --}}
                        @if($status === 'valid' && auth()->check() && auth()->user()->hasRole('admin'))
                            <form action="{{ route('tickets.checkin', $registration->ticket_code) }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="w-full py-3 rounded-xl bg-green-600 hover:bg-green-700 text-white font-bold text-sm transition-colors">
                                    ✅ Konfirmasi Check-In Peserta
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Footer --}}
            <p class="text-center text-xs text-gray-400">
                Dipowered oleh <span class="font-semibold text-gray-500">{{ \App\Models\Setting::get('site_name', 'Skolah.com') }}</span> &middot;
                Scan QR hanya untuk event offline resmi
            </p>

        </div>
    </main>

</body>
</html>
