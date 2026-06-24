{{--
    Reusable notification bell dropdown.
    Usage: <x-notification-dropdown />
--}}
@php
    $unreadCount = auth()->user()->unreadNotifications()->count();
    $recentNotifications = auth()->user()->notifications()->latest()->take(8)->get();
@endphp
<div x-data="{ open: false }" class="relative">
    <button @click="open = !open"
            class="relative p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        <span id="notification-badge" 
              class="absolute -top-0.5 -right-0.5 flex items-center justify-center min-w-[18px] h-[18px] text-[10px] font-bold text-white bg-red-500 rounded-full ring-2 ring-white px-1 {{ $unreadCount > 0 ? '' : 'hidden' }}">
            {{ $unreadCount > 9 ? '9+' : ($unreadCount > 0 ? $unreadCount : '0') }}
        </span>
    </button>

    {{-- Dropdown panel --}}
    <div x-show="open" x-cloak @click.outside="open = false"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 mt-2 w-80 sm:w-96 rounded-xl bg-white shadow-xl ring-1 ring-gray-200 z-50 overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-sm font-semibold text-gray-800">Notifikasi</h3>
            @if($unreadCount > 0)
                <form method="POST" action="{{ route('dashboard.notifications.mark-all-read') }}">
                    @csrf
                    <button type="submit" class="text-xs text-blue-600 hover:text-blue-700 font-medium">
                        Tandai semua dibaca
                    </button>
                </form>
            @endif
        </div>

        {{-- Notification list --}}
        <div class="max-h-80 overflow-y-auto divide-y divide-gray-50">
            @forelse($recentNotifications as $notif)
                <a href="{{ route('dashboard.notifications.read', $notif->id) }}"
                   class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition-colors {{ is_null($notif->read_at) ? 'bg-blue-50/40' : '' }}">
                    {{-- Icon by type --}}
                    <div class="shrink-0 mt-0.5">
                        @switch($notif->data['type'] ?? 'info')
                            @case('success')
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-green-100 text-green-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </span>
                                @break
                            @case('warning')
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-yellow-100 text-yellow-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M12 3l9.09 16H2.91L12 3z"/></svg>
                                </span>
                                @break
                            @case('error')
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-red-100 text-red-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </span>
                                @break
                            @case('order')
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-purple-100 text-purple-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                </span>
                                @break
                            @case('course')
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                </span>
                                @break
                            @case('cert')
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-amber-100 text-amber-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                                </span>
                                @break
                            @case('bootcamp')
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 text-indigo-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </span>
                                @break
                            @default
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </span>
                        @endswitch
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ $notif->data['title'] ?? 'Notifikasi' }}</p>
                        <p class="text-xs text-gray-500 line-clamp-2 mt-0.5">{{ $notif->data['message'] ?? '' }}</p>
                        <p class="text-[11px] text-gray-400 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                    </div>
                    @if(is_null($notif->read_at))
                        <span class="shrink-0 mt-2 w-2 h-2 bg-blue-500 rounded-full"></span>
                    @endif
                </a>
            @empty
                <div class="px-4 py-8 text-center">
                    <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <p class="text-sm text-gray-400">Belum ada notifikasi</p>
                </div>
            @endforelse
        </div>

        {{-- Footer --}}
        <div class="border-t border-gray-100 bg-gray-50/50">
            <a href="{{ route('dashboard.notifications') }}"
               class="block text-center text-sm text-blue-600 hover:text-blue-700 font-medium py-2.5 hover:bg-gray-100 transition-colors">
                Lihat Semua Notifikasi
            </a>
        </div>
    </div>
</div>
