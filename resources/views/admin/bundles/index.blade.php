@extends('layouts.admin')

@section('title', 'Manajemen Bundle Kursus')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Manajemen Bundle Kursus</h1>
            <p class="text-slate-500 text-sm mt-1">Kelola paket kursus hemat untuk meningkatkan penjualan.</p>
        </div>
        <a href="{{ route('admin.bundles.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-all shadow-md shadow-blue-600/20">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Bundle
        </a>
    </div>

    {{-- Filters --}}
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <form action="{{ route('admin.bundles.index') }}" method="GET" class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[200px] relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="block w-full pl-10 pr-3 py-2 border border-slate-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm"
                       placeholder="Cari nama bundle...">
            </div>
            <button type="submit" class="px-5 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl hover:bg-slate-200 transition-all">
                Filter
            </button>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Bundle</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Kursus</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Harga</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($bundles as $bundle)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $bundle->thumbnail_url }}" alt="{{ $bundle->title }}" class="w-12 h-12 rounded-lg object-cover bg-slate-100">
                                    <div>
                                        <div class="text-sm font-bold text-slate-900">{{ $bundle->title }}</div>
                                        <div class="text-xs text-slate-500">Oleh: {{ $bundle->instructor->name ?? 'Admin' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 font-medium">
                                {{ $bundle->courses_count }} Kursus
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-slate-900">{{ $bundle->final_price_formatted }}</span>
                                    @if($bundle->has_discount)
                                        <span class="text-xs text-slate-400 line-through">{{ $bundle->original_price_formatted }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($bundle->status === 'published')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                        Published
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-800">
                                        Draft
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.bundles.edit', $bundle) }}" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.bundles.destroy', $bundle) }}" method="POST" onsubmit="return confirm('Hapus bundle ini?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Hapus">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-slate-500">Belum ada bundle kursus.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($bundles->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                {{ $bundles->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
