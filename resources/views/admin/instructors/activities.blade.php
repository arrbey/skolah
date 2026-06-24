@extends('layouts.admin')

@section('title', 'Log Aktivitas Instruktur')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.instructors.index') }}" class="p-2 rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-blue-600 transition-all shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Log Aktivitas Instruktur</h1>
                    <p class="text-slate-500 text-sm mt-0.5">Pantau setiap aksi yang dilakukan oleh instruktur di dashboard mereka.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <form action="{{ route('admin.instructors.activities') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Filter Instruktur</label>
                <select name="user_id" class="block w-full py-2 pl-3 pr-10 border border-slate-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm appearance-none">
                    <option value="">Semua Instruktur</option>
                    @foreach($instructors as $instructor)
                        <option value="{{ $instructor->id }}" {{ request('user_id') == $instructor->id ? 'selected' : '' }}>
                            {{ $instructor->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2 space-y-1.5">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Cari Aksi / URL</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="block w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm"
                       placeholder="Cari rute, URL, atau IP address...">
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-all shadow-md shadow-blue-600/20">
                    Terapkan Filter
                </button>
                @if(request()->anyFilled(['user_id', 'search']))
                    <a href="{{ route('admin.instructors.activities') }}" class="p-2 text-slate-400 hover:text-red-500 transition-colors" title="Reset Filter">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Activity Feed --}}
    <div class="space-y-4">
        @forelse($activities as $log)
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:border-blue-200 transition-all group">
                <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                    <div class="flex items-start gap-4">
                        <div class="shrink-0 mt-1">
                            <img src="{{ $log->user->avatar_url }}" alt="{{ $log->user->name }}" class="w-10 h-10 rounded-full border-2 border-white shadow-sm">
                        </div>
                        <div class="space-y-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-sm font-bold text-slate-900">{{ $log->user->name }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-widest {{ $log->method_badge }}">
                                    {{ $log->method }}
                                </span>
                                <span class="text-xs text-slate-400">•</span>
                                <span class="text-xs font-medium text-slate-500">{{ $log->created_at->diffForHumans() }}</span>
                                <span class="text-xs text-slate-300">({{ $log->created_at->format('d M Y, H:i') }})</span>
                            </div>
                            <div class="text-sm font-medium text-slate-700 break-all">
                                <span class="text-slate-400">URL:</span> {{ $log->url }}
                            </div>
                            @if($log->route_name)
                                <div class="text-xs font-bold text-blue-600 uppercase tracking-tight">
                                    Route: {{ $log->route_name }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-col items-end gap-2 shrink-0">
                        <span class="px-2 py-1 rounded-lg text-xs font-bold {{ $log->status_badge }}">
                            Status: {{ $log->status_code }}
                        </span>
                        <div class="text-[10px] font-medium text-slate-400 text-right">
                            IP: {{ $log->ip_address }}
                        </div>
                    </div>
                </div>

                {{-- Payload Preview (Click to Expand) --}}
                @if($log->payload)
                    <div x-data="{ open: false }" class="mt-4 border-t border-slate-50 pt-4">
                        <button @click="open = !open" class="flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-blue-600 transition-colors">
                            <svg class="w-4 h-4 transform transition-transform" :class="open ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                            LIHAT PAYLOAD DATA
                        </button>
                        <div x-show="open" x-collapse x-cloak class="mt-3">
                            <div class="bg-slate-900 rounded-xl p-4 overflow-x-auto max-h-[300px]">
                                <pre class="text-[10px] text-emerald-400 font-mono">{{ json_encode($log->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white py-16 rounded-2xl border border-slate-200 text-center text-slate-500">
                <div class="flex flex-col items-center gap-4">
                    <div class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center">
                        <svg class="w-8 h-8 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-lg font-bold text-slate-900">Belum ada log aktivitas.</p>
                        <p class="text-sm">Aksi penting (simpan/hapus) dari instruktur akan muncul di sini.</p>
                    </div>
                </div>
            </div>
        @endforelse

        @if($activities->hasPages())
            <div class="mt-6">
                {{ $activities->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
