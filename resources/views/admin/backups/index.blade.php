@extends('layouts.admin')

@section('title', 'Backup Manager')

@section('page-header')
    <div class="flex items-center justify-between w-full">
        <span class="text-base font-semibold text-gray-900">Backup Manager</span>
        <div class="flex items-center gap-2">
            <form method="POST" action="{{ route('admin.backups.store') }}" class="inline-flex">
                @csrf
                <input type="hidden" name="only_db" value="1">
                <button type="submit"
                        onclick="return confirm('Jalankan backup database saja sekarang? Proses jalan di background.')"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10a2 2 0 002 2h12a2 2 0 002-2V7M4 7l8 6 8-6M4 7h16"/>
                    </svg>
                    Backup DB Saja
                </button>
            </form>
            <form method="POST" action="{{ route('admin.backups.store') }}" class="inline-flex">
                @csrf
                <button type="submit"
                        onclick="return confirm('Jalankan backup lengkap (database + file upload) sekarang? Proses jalan di background, mungkin 1-5 menit.')"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary-600 text-white text-sm font-bold hover:bg-primary-700 shadow-sm focus:ring-2 focus:ring-primary-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4"/>
                    </svg>
                    Backup Sekarang
                </button>
            </form>
        </div>
    </div>
@endsection

@section('content')
    {{-- Alert --}}
    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 font-medium">Jumlah Backup</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['count']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 font-medium">Total Ukuran</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['total_size'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 font-medium">Backup Terbaru</p>
            <p class="text-sm font-semibold text-gray-900 mt-2">
                @if($stats['newest'])
                    {{ \Carbon\Carbon::createFromTimestamp($stats['newest'])->diffForHumans() }}
                @else
                    —
                @endif
            </p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 font-medium">Off-site MinIO</p>
            <p class="text-sm font-semibold mt-2 {{ $stats['remote_enabled'] ? 'text-green-600' : 'text-yellow-600' }}">
                {{ $stats['remote_enabled'] ? '✓ Aktif' : '⚠ Non-aktif' }}
            </p>
        </div>
    </div>

    {{-- Info Panel --}}
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 mb-6 text-sm text-blue-900">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <div>
                <p class="font-semibold">Backup otomatis jalan harian pukul 02:00.</p>
                <p class="mt-1">Retention: 7 hari terakhir + weekly/monthly/yearly. Backup dihapus otomatis sesuai retention policy. Gunakan tombol "Backup Sekarang" sebelum deploy besar.</p>
            </div>
        </div>
    </div>

    {{-- Backup List Table --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Nama File</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Ukuran</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                        <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($files as $file)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span class="font-mono text-xs text-gray-900">{{ $file['name'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-700">{{ $file['size_human'] }}</td>
                            <td class="px-6 py-4 text-gray-700">
                                {{ \Carbon\Carbon::createFromTimestamp($file['last_modified'])->format('d M Y, H:i') }}
                                <span class="text-xs text-gray-400">({{ \Carbon\Carbon::createFromTimestamp($file['last_modified'])->diffForHumans() }})</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.backups.download', ['file' => $file['name']]) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 text-xs font-medium hover:bg-blue-100">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4"/></svg>
                                        Download
                                    </a>
                                    <form method="POST" action="{{ route('admin.backups.destroy') }}" class="inline-flex">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="file" value="{{ $file['name'] }}">
                                        <button type="submit"
                                                onclick="return confirm('Hapus backup {{ $file['name'] }}? Tidak bisa dibatalkan.')"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-red-50 text-red-700 text-xs font-medium hover:bg-red-100">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <p class="font-medium">Belum ada backup</p>
                                <p class="text-xs mt-1">Klik tombol "Backup Sekarang" untuk bikin backup pertama.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- CLI Reference --}}
    <details class="mt-6 bg-gray-50 rounded-xl p-4 text-sm text-gray-700">
        <summary class="font-semibold cursor-pointer">Perintah CLI (untuk developer)</summary>
        <div class="mt-3 space-y-2 font-mono text-xs">
            <p><code class="bg-white px-2 py-0.5 rounded">php artisan backup:run</code> — backup lengkap (DB + file)</p>
            <p><code class="bg-white px-2 py-0.5 rounded">php artisan backup:run --only-db</code> — hanya database</p>
            <p><code class="bg-white px-2 py-0.5 rounded">php artisan backup:list</code> — lihat status semua backup</p>
            <p><code class="bg-white px-2 py-0.5 rounded">php artisan backup:clean</code> — hapus backup lama sesuai retention</p>
            <p><code class="bg-white px-2 py-0.5 rounded">php artisan backup:monitor</code> — cek kesehatan backup</p>
        </div>
    </details>
@endsection
