@extends('layouts.admin')

@section('title', 'Pengajuan Instruktur')

@section('page-header')
    <span class="text-base font-semibold text-gray-900">Pengajuan Instruktur</span>
@endsection

@section('content')
    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 font-medium">Menunggu Review</p>
            <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $stats['pending'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 font-medium">Disetujui</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['approved'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 font-medium">Ditolak</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['rejected'] }}</p>
        </div>
    </div>

    {{-- Filter Tabs --}}
    <div class="flex items-center gap-2 flex-wrap mb-6">
        @foreach([
            ['key' => 'pending',  'label' => 'Menunggu', 'count' => $stats['pending']],
            ['key' => 'approved', 'label' => 'Disetujui', 'count' => $stats['approved']],
            ['key' => 'rejected', 'label' => 'Ditolak',   'count' => $stats['rejected']],
            ['key' => 'all',      'label' => 'Semua',     'count' => $stats['pending'] + $stats['approved'] + $stats['rejected']],
        ] as $tab)
            <a href="{{ route('admin.instructor-applications.index', ['filter' => $tab['key']]) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold transition-colors
                      {{ $filter === $tab['key']
                          ? 'bg-primary-600 text-white shadow-sm'
                          : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' }}">
                {{ $tab['label'] }}
                <span class="text-xs px-1.5 py-0.5 rounded-full
                      {{ $filter === $tab['key'] ? 'bg-white/20' : 'bg-gray-100 text-gray-500' }}">
                    {{ $tab['count'] }}
                </span>
            </a>
        @endforeach
    </div>

    {{-- Table --}}
    @if($applications->isNotEmpty())
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left">
                            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Pemohon</th>
                            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Keahlian</th>
                            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($applications as $app)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-bold text-sm flex-shrink-0">
                                            {{ strtoupper(substr($app->user->name ?? '?', 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $app->user->name ?? '-' }}</p>
                                            <p class="text-xs text-gray-500">{{ $app->user->email ?? '-' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-sm text-gray-700">{{ $app->expertise }}</td>
                                <td class="px-5 py-3.5 text-xs text-gray-500">{{ $app->created_at->translatedFormat('d M Y') }}</td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold
                                        @if($app->status === 'pending') bg-yellow-50 text-yellow-700
                                        @elseif($app->status === 'approved') bg-green-50 text-green-700
                                        @elseif($app->status === 'rejected') bg-red-50 text-red-700
                                        @endif">
                                        {{ $app->status_label }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <a href="{{ route('admin.instructor-applications.show', $app) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            {{ $applications->withQueryString()->links() }}
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                </svg>
            </div>
            <h3 class="text-base font-bold text-gray-900 mb-1">Tidak Ada Pengajuan</h3>
            <p class="text-sm text-gray-500">Belum ada pengajuan instruktur untuk ditampilkan.</p>
        </div>
    @endif
@endsection
