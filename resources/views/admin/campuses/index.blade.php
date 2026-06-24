@extends('layouts.admin')

@section('title', 'Manajemen Kampus Offline')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kampus Offline</h1>
            <p class="text-sm text-gray-500">Kelola lokasi pelatihan tatap muka yang tampil di halaman utama.</p>
        </div>
        <a href="{{ route('admin.campuses.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            + Tambah Kampus
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Order</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Kampus</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Alamat</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($campuses as $campus)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $campus->order }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            @if($campus->image)
                                <img src="{{ storageUrl($campus->image) }}" class="w-12 h-12 rounded-lg object-cover bg-gray-100">
                            @else
                                <div class="w-12 h-12 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                </div>
                            @endif
                            <div>
                                <p class="text-sm font-bold text-gray-900">{{ $campus->name }}</p>
                                <p class="text-xs text-gray-500">{{ $campus->tagline }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500 truncate max-w-[200px]">
                        {{ $campus->address }}
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('admin.campuses.edit', $campus) }}" class="text-blue-600 hover:text-blue-800 text-sm font-bold">Edit</a>
                        <form action="{{ route('admin.campuses.destroy', $campus) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus kampus ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-bold">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
