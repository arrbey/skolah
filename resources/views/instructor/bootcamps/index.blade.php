@extends('layouts.instructor')

@section('title', 'Bootcamp Saya')

@section('page-header')
    <div class="flex items-center justify-between w-full">
        <div>
            <h1 class="text-lg font-bold text-gray-900">Kelola Bootcamp & Webinar</h1>
            <p class="text-sm text-gray-500">Kelola sesi live dan pantau pendaftaran peserta</p>
        </div>
        <a href="{{ route('instructor.bootcamps.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-secondary-600 text-white text-sm font-bold hover:bg-secondary-700 transition-all shadow-md shadow-secondary-100">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Bootcamp Baru
        </a>
    </div>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Total Bootcamp</p>
            <p class="text-2xl font-black text-gray-900 mt-1">{{ $bootcamps->total() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Total Pendaftar</p>
            <p class="text-2xl font-black text-secondary-600 mt-1">{{ $bootcamps->sum('total_registered') }}</p>
        </div>
    </div>

    {{-- List --}}
    @if($bootcamps->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center">
            <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-1">Belum ada bootcamp</h3>
            <p class="text-sm text-gray-500 mb-6">Buat sesi belajar interaktif atau webinar Anda.</p>
            <a href="{{ route('instructor.bootcamps.create') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-secondary-600 text-white text-sm font-bold hover:bg-secondary-700 transition-all">
                Buat Bootcamp Pertama
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($bootcamps as $bootcamp)
                <div class="bg-white rounded-2xl border border-gray-200 p-5 hover:shadow-lg transition-all group">
                    <div class="flex gap-4">
                        {{-- Thumbnail --}}
                        <div class="relative w-32 h-20 shrink-0">
                            <img src="{{ $bootcamp->thumbnail_url }}" alt="{{ $bootcamp->title }}"
                                 class="w-full h-full rounded-xl object-cover border border-gray-100">
                            <div class="absolute top-1 right-1">
                                <span class="px-2 py-0.5 rounded-lg bg-secondary-500 text-[8px] font-black uppercase text-white shadow-sm">{{ $bootcamp->type }}</span>
                            </div>
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-gray-900 truncate group-hover:text-secondary-600 transition-colors">{{ $bootcamp->title }}</h3>
                            <p class="text-[10px] text-gray-400 mt-1 font-bold uppercase tracking-wider">
                                {{ $bootcamp->start_date->locale('id')->translatedFormat('d F Y') }}
                            </p>
                            
                            <div class="flex items-center gap-3 mt-3">
                                <span class="flex items-center gap-1 text-[10px] font-bold text-gray-400 uppercase">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    {{ $bootcamp->total_registered }} Pendaftar
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 mt-5">
                        <a href="{{ route('instructor.bootcamps.edit', $bootcamp->id) }}"
                           class="p-2.5 rounded-xl bg-gray-50 text-gray-600 hover:bg-secondary-50 hover:text-secondary-600 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <a href="{{ route('instructor.bootcamps.registrations', $bootcamp->id) }}"
                           class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-secondary-600 text-white text-xs font-bold hover:bg-secondary-700 transition-all shadow-md shadow-secondary-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Pendaftar
                        </a>
                        <form action="{{ route('instructor.bootcamps.destroy', $bootcamp->id) }}" method="POST" onsubmit="return confirm('Hapus bootcamp ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-2.5 rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $bootcamps->links() }}
        </div>
    @endif
</div>
@endsection
