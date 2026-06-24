@extends('layouts.admin')

@section('title', 'Kelola Banner')

@section('page-header')
    <div class="flex items-center justify-between w-full">
        <span class="text-base font-semibold text-gray-900">Banner</span>
        <a href="{{ route('admin.banners.create') }}" class="px-4 py-2 rounded-xl bg-primary-600 text-white text-sm font-medium hover:bg-primary-700">+ Tambah Banner</a>
    </div>
@endsection

@section('content')
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase w-12">Urutan</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Banner</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Link</th>
                        <th class="text-center px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Posisi</th>
                        <th class="text-center px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Aktif</th>
                        <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($banners as $banner)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-center text-gray-500 font-mono text-xs">{{ $banner->order }}</td>
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $banner->image_url }}" class="w-24 h-12 rounded-lg object-cover shrink-0 border" alt="">
                                    <span class="font-medium text-gray-900">{{ $banner->title }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-3 text-gray-500 text-xs truncate max-w-[200px]">{{ $banner->link ?? '—' }}</td>
                            <td class="px-6 py-3 text-center">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">{{ $banner->position ?? 'default' }}</span>
                            </td>
                            <td class="px-6 py-3 text-center">
                                <form action="{{ route('admin.banners.toggle-active', $banner) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <button class="w-9 h-5 rounded-full transition-colors {{ $banner->is_active ? 'bg-green-500' : 'bg-gray-300' }} relative">
                                        <span class="block w-3.5 h-3.5 bg-white rounded-full absolute top-0.5 transition-transform {{ $banner->is_active ? 'translate-x-4' : 'translate-x-0.5' }}"></span>
                                    </button>
                                </form>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.banners.edit', $banner) }}" class="px-2.5 py-1 rounded-lg bg-gray-100 text-gray-700 text-xs font-medium hover:bg-gray-200">Edit</a>
                                    <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" class="inline" onsubmit="return confirm('Hapus banner ini?')">
                                        @csrf @method('DELETE')
                                        <button class="px-2.5 py-1 rounded-lg bg-red-50 text-red-700 text-xs font-medium hover:bg-red-100">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-8 text-center text-gray-400">Belum ada banner.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
