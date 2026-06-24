{{-- resources/views/dashboard/notifications.blade.php --}}
@extends('layouts.app')

@section('title', 'Notifikasi Saya')

@push('head')
<meta name="description" content="Semua notifikasi aktivitas akun {{ \App\Models\Setting::get('site_name', 'Skolah.com') }} Anda">
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Notifikasi</h1>
                <p class="text-sm text-gray-500 mt-1">Semua aktivitas dan informasi terbaru untuk Anda</p>
            </div>
            @if($notifications->total() > 0)
                <form method="POST" action="{{ route('dashboard.notifications.mark-all-read') }}">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Tandai semua dibaca
                    </button>
                </form>
            @endif
        </div>

        {{-- Flash message --}}
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        {{-- Notification list --}}
        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 overflow-hidden">

            @forelse($notifications as $notif)
                @php $data = $notif->data; @endphp
                <div class="group flex items-start gap-4 px-6 py-4 border-b border-gray-100 last:border-0 hover:bg-gray-50 transition {{ $notif->read_at ? '' : 'bg-blue-50/40' }}">

                    {{-- Icon --}}
                    <div class="shrink-0 w-10 h-10 rounded-xl flex items-center justify-center text-lg
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
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-900">
                                    {{ $data['title'] ?? 'Notifikasi' }}
                                    @if(!$notif->read_at)
                                        <span class="inline-block ml-2 w-2 h-2 bg-blue-500 rounded-full align-middle"></span>
                                    @endif
                                </p>
                                <p class="text-sm text-gray-600 mt-0.5">{{ $data['message'] ?? '' }}</p>
                                @if(!empty($data['url']))
                                    <a href="{{ route('dashboard.notifications.read', $notif->id) }}"
                                       class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-700 mt-1.5 font-medium">
                                        Lihat detail
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="text-xs text-gray-400 whitespace-nowrap">{{ $notif->created_at->diffForHumans() }}</span>
                                {{-- Delete button --}}
                                <form method="POST" action="{{ route('dashboard.notifications.destroy', $notif->id) }}">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        onclick="return confirm('Hapus notifikasi ini?')"
                                        class="opacity-0 group-hover:opacity-100 p-1 text-gray-400 hover:text-red-500 rounded transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                {{-- Empty state --}}
                <div class="py-16 text-center">
                    <div class="text-6xl mb-4">🔔</div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum ada notifikasi</h3>
                    <p class="text-sm text-gray-500">Notifikasi tentang kursus, pembayaran, dan sertifikat akan muncul di sini.</p>
                    <a href="{{ route('courses.index') }}"
                       class="inline-block mt-6 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                        Mulai Belajar
                    </a>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($notifications->hasPages())
            <div class="mt-6">
                {{ $notifications->links() }}
            </div>
        @endif

    </div>
</div>
@endsection
