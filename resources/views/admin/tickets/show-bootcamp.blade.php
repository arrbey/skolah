@extends('layouts.admin')

@section('title', 'Absensi - ' . $bootcamp->title)

@section('page-header')
    <a href="{{ route('admin.tickets.scan', ['bootcamp_id' => $bootcamp->id]) }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
        ← Kembali ke Scan
    </a>
    <span class="text-gray-300 mx-2">|</span>
    <span class="text-base font-semibold text-gray-900">Absensi Peserta</span>
@endsection

@section('content')
    <div class="space-y-6">
        {{-- Header Card --}}
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl p-6 text-white shadow-lg">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <p class="text-sm opacity-90 mb-1">Program Bootcamp</p>
                    <p class="text-lg font-bold">{{ $bootcamp->title }}</p>
                </div>
                <div>
                    <p class="text-sm opacity-90 mb-1">Tanggal</p>
                    <p class="text-lg font-bold">{{ $bootcamp->start_date->translatedFormat('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-sm opacity-90 mb-1">Total Peserta</p>
                    <p class="text-lg font-bold">{{ $totalCount }} orang</p>
                </div>
                <div>
                    <p class="text-sm opacity-90 mb-1">Sudah Hadir</p>
                    <div class="flex items-center gap-2">
                        <p class="text-lg font-bold">{{ $checkedInCount }}</p>
                        <div class="text-xs bg-white/20 px-2 py-1 rounded">
                            {{ $totalCount > 0 ? round(($checkedInCount / $totalCount) * 100) : 0 }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Progress Bar --}}
        @if($totalCount > 0)
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-sm font-medium text-gray-700">Progress Kehadiran</p>
                    <p class="text-sm font-bold text-blue-600">{{ $checkedInCount }}/{{ $totalCount }}</p>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-full transition-all duration-300"
                         style="width: {{ $totalCount > 0 ? ($checkedInCount / $totalCount) * 100 : 0 }}%"></div>
                </div>
            </div>
        @endif

        {{-- Peserta Table --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Daftar Absensi Peserta</h3>
                    <span class="text-xs font-semibold text-gray-500">{{ $checkedInCount }} hadir dari {{ $totalCount }} peserta</span>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.tickets.export-pdf', $bootcamp) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-2 bg-red-50 hover:bg-red-100 text-red-700 text-xs font-semibold rounded-lg transition-colors border border-red-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Export PDF
                    </a>
                    <a href="{{ route('admin.tickets.export-excel', $bootcamp) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-2 bg-green-50 hover:bg-green-100 text-green-700 text-xs font-semibold rounded-lg transition-colors border border-green-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Export Excel
                    </a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Peserta</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Kode Tiket</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status Pembayaran</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Kehadiran</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Jam Hadir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($registrations as $registration)
                            <tr class="hover:bg-gray-50/50 transition-colors {{ $registration->checked_in ? 'bg-green-50/30' : '' }}">
                                {{-- Peserta --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ avatarUrl($registration->user) }}" 
                                             alt="{{ $registration->user->name }}"
                                             class="w-10 h-10 rounded-full object-cover">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">{{ $registration->user->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $registration->user->email }}</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Kode Tiket --}}
                                <td class="px-6 py-4">
                                    <code class="px-2.5 py-1.5 bg-gray-100 rounded text-xs font-mono text-gray-700">
                                        {{ $registration->ticket_code }}
                                    </code>
                                </td>

                                {{-- Status Pembayaran --}}
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold
                                               @if($registration->payment_status === 'paid')
                                                   bg-green-100 text-green-700
                                               @elseif($registration->payment_status === 'pending')
                                                   bg-yellow-100 text-yellow-700
                                               @else
                                                   bg-red-100 text-red-700
                                               @endif">
                                        @if($registration->payment_status === 'paid')
                                            ✓ Lunas
                                        @elseif($registration->payment_status === 'pending')
                                            ⏳ Pending
                                        @else
                                            ✕ Gagal
                                        @endif
                                    </span>
                                </td>

                                {{-- Check-in Status --}}
                                <td class="px-6 py-4">
                                    @if($registration->checked_in)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                            Hadir
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-600">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                            </svg>
                                            Belum
                                        </span>
                                    @endif
                                </td>

                                {{-- Waktu Check-in --}}
                                <td class="px-6 py-4 text-sm text-center text-gray-600">
                                    @if($registration->checked_in)
                                        <span class="font-semibold text-gray-900">{{ $registration->checked_in_at->format('H:i') }}</span>
                                        <p class="text-xs text-gray-500">{{ $registration->checked_in_at->translatedFormat('d M Y') }}</p>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <p class="text-gray-500">Tidak ada peserta terdaftar</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Quick Access --}}
        <div class="flex gap-3">
            <a href="{{ route('admin.tickets.scan', ['bootcamp_id' => $bootcamp->id]) }}" class="flex-1 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors text-center">
                ← Kembali Scan & Absensi
            </a>
            <a href="{{ route('admin.tickets.index') }}" class="px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition-colors text-center">
                Semua Bootcamp
            </a>
        </div>
    </div>
@endsection
