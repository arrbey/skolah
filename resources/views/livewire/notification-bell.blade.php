{{-- resources/views/livewire/notification-bell.blade.php --}}
<div wire:poll.30s="refresh" class="relative" x-data="{ open: false }" @click.away="open = false">

    {{-- Bell Button --}}
    <button @click="open = !open"
        class="relative p-2 text-gray-500 hover:text-gray-900 rounded-lg hover:bg-gray-100 transition-colors focus:outline-none">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        @if($unreadCount > 0)
            <span class="absolute -top-0.5 -right-0.5 inline-flex items-center justify-center w-4 h-4 text-[10px] font-bold text-white bg-red-500 rounded-full">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown Panel --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-1"
         class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl ring-1 ring-gray-200 z-50 overflow-hidden"
         style="display: none;">

        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900">Notifikasi</h3>
            @if($unreadCount > 0)
                <form method="POST" action="{{ route('dashboard.notifications.mark-all-read') }}">
                    @csrf
                    <button type="submit" class="text-xs text-blue-600 hover:text-blue-700 font-medium transition">
                        Tandai semua dibaca
                    </button>
                </form>
            @endif
        </div>

        {{-- List 5 terbaru --}}
        @php
            $latestNotifications = auth()->user()?->notifications()->latest()->take(5)->get() ?? collect();
        @endphp

        <div class="max-h-80 overflow-y-auto divide-y divide-gray-50">
            @forelse($latestNotifications as $notif)
                @php $data = $notif->data; @endphp
                <a href="{{ route('dashboard.notifications.read', $notif->id) }}"
                   class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition {{ $notif->read_at ? '' : 'bg-blue-50/50' }}">

                    {{-- Icon --}}
                    <div class="shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-sm
                        {{ match($data['type'] ?? 'info') {
                            'success'   => 'bg-green-100',
                            'warning'   => 'bg-amber-100',
                            'error'     => 'bg-red-100',
                            'course'    => 'bg-blue-100',
                            'bootcamp'  => 'bg-purple-100',
                            'order'     => 'bg-emerald-100',
                            'cert'      => 'bg-yellow-100',
                            default     => 'bg-gray-100',
                        } }}">
                        {{ match($data['type'] ?? 'info') {
                            'success'   => '✅',
                            'warning'   => '⚠️',
                            'error'     => '❌',
                            'course'    => '📚',
                            'bootcamp'  => '🎓',
                            'order'     => '🛒',
                            'cert'      => '🏆',
                            default     => '🔔',
                        } }}
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-gray-900 truncate">{{ $data['title'] ?? 'Notifikasi' }}</p>
                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-2">{{ $data['message'] ?? '' }}</p>
                        <p class="text-[10px] text-gray-400 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                    </div>

                    {{-- Unread dot --}}
                    @if(!$notif->read_at)
                        <div class="shrink-0 w-2 h-2 mt-1 bg-blue-500 rounded-full"></div>
                    @endif
                </a>
            @empty
                <div class="py-8 text-center">
                    <div class="text-3xl mb-2">🔔</div>
                    <p class="text-sm text-gray-500">Belum ada notifikasi</p>
                </div>
            @endforelse
        </div>

        {{-- Footer --}}
        <div class="border-t border-gray-100 px-4 py-2.5">
            <a href="{{ route('dashboard.notifications') }}"
               class="block text-center text-xs text-blue-600 hover:text-blue-700 font-medium transition">
                Lihat semua notifikasi →
            </a>
        </div>
    </div>
</div>
