@extends('layouts.admin')

@section('title', 'Manajemen Flash Sale')

@section('content')
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Flash Sale</h1>
        <p class="text-slate-500">Kelola promo kilat untuk meningkatkan penjualan.</p>
    </div>
    <a href="{{ route('admin.flash-sales.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Buat Flash Sale Baru
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500">Judul</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500">Periode</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500">Status</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500">Item</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($flashSales as $fs)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-semibold text-slate-900">{{ $fs->title }}</div>
                        <div class="text-xs text-slate-500">{{ $fs->slug }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600">
                        <div>{{ $fs->start_at->format('d M Y, H:i') }}</div>
                        <div class="text-xs text-slate-400">sampai</div>
                        <div>{{ $fs->end_at->format('d M Y, H:i') }}</div>
                    </td>
                    <td class="px-6 py-4">
                        @if($fs->is_active)
                            @if($fs->isRunning)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5 animate-pulse"></span>
                                    Berjalan
                                </span>
                            @elseif(now() < $fs->start_at)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Mendatang
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                                    Selesai
                                </span>
                            @endif
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-800">
                                Nonaktif
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 bg-slate-100 rounded text-xs font-bold text-slate-600">
                            {{ $fs->items_count }} Item
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('admin.flash-sales.edit', $fs) }}" class="inline-flex p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit & Kelola Item">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <form action="{{ route('admin.flash-sales.destroy', $fs) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus flash sale ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <h3 class="text-slate-900 font-bold">Belum Ada Flash Sale</h3>
                            <p class="text-slate-500 text-sm mt-1">Mulai buat promo kilat pertama Anda.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($flashSales->hasPages())
    <div class="px-6 py-4 border-t border-slate-200">
        {{ $flashSales->links() }}
    </div>
    @endif
</div>
@endsection
