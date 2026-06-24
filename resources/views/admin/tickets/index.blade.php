@extends('layouts.admin')

@section('title', 'Daftar Absensi Bootcamp')

@section('page-header')
    <span class="text-base font-semibold text-gray-900">Daftar Absensi Bootcamp</span>
@endsection

@section('content')
    <div class="space-y-6">
        {{-- Filter + Header --}}
        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-600">Pilih bootcamp untuk melihat daftar absensi peserta</p>
            <a href="{{ route('admin.tickets.scan') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">
                Scan & Absensi →
            </a>
        </div>

        {{-- Bootcamp Grid --}}
        @if($bootcamps->isEmpty())
            <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-gray-500 font-medium">Tidak ada bootcamp offline yang aktif</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($bootcamps as $bootcamp)
                    <a href="{{ route('admin.tickets.show-bootcamp', $bootcamp) }}" 
                       class="group bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg hover:border-primary-300 transition-all duration-300">
                        {{-- Header with status --}}
                        <div class="relative h-32 bg-gradient-to-br from-primary-500 to-secondary-500 flex items-end p-4">
                            <div class="absolute top-3 right-3">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold
                                           @if($bootcamp->status === 'ongoing')
                                               bg-green-100 text-green-700
                                           @elseif($bootcamp->status === 'upcoming')
                                               bg-blue-100 text-blue-700
                                           @else
                                               bg-gray-100 text-gray-700
                                           @endif">
                                    @if($bootcamp->status === 'ongoing')
                                        ⏱ Sedang Berlangsung
                                    @elseif($bootcamp->status === 'upcoming')
                                        📅 Akan Datang
                                    @else
                                        ✓ Selesai
                                    @endif
                                </span>
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="p-4">
                            <h3 class="font-bold text-gray-900 line-clamp-2 mb-2 group-hover:text-primary-600 transition-colors">
                                {{ $bootcamp->title }}
                            </h3>

                            {{-- Info --}}
                            <div class="space-y-2 mb-4 text-sm">
                                <div class="flex items-center gap-2 text-gray-600">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>{{ $bootcamp->start_date->translatedFormat('d M Y H:i') }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-gray-600">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span class="line-clamp-1">{{ $bootcamp->location }}</span>
                                </div>
                            </div>

                            {{-- Stats --}}
                            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                <div class="text-center flex-1">
                                    <p class="text-2xl font-bold text-blue-600">{{ $bootcamp->registrations->count() }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Total Peserta</p>
                                </div>
                                <div class="w-px h-8 bg-gray-200"></div>
                                <div class="text-center flex-1">
                                    <p class="text-2xl font-bold text-green-600">
                                        {{ $bootcamp->registrations->where('checked_in', true)->count() }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-0.5">Sudah Hadir</p>
                                </div>
                            </div>
                        </div>

                        {{-- CTA --}}
                        <div class="px-4 pb-4">
                            <button class="w-full px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">
                                Lihat Absensi →
                            </button>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
@endsection
