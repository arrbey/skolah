@extends('layouts.admin')

@section('title', 'Manajemen Galeri Kegiatan')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Galeri Kegiatan</h1>
            <p class="text-sm text-gray-500">Kelola foto keseruan komunitas yang tampil di slider halaman utama.</p>
        </div>
        <a href="{{ route('admin.galleries.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            + Tambah Foto Gallery
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Urutan</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Foto & Judul</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($galleries as $gallery)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $gallery->order }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <img src="{{ storageUrl($gallery->image) }}" class="w-16 h-12 rounded-lg object-cover bg-gray-100 border">
                            <div>
                                <p class="text-sm font-bold text-gray-900">{{ $gallery->title }}</p>
                                <p class="text-xs text-gray-500 truncate max-w-xs">{{ $gallery->content }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $gallery->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ $gallery->is_active ? 'Aktif' : 'Non-aktif' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('admin.galleries.edit', $gallery) }}" class="text-blue-600 hover:text-blue-800 text-sm font-bold">Edit</a>
                        <form action="{{ route('admin.galleries.destroy', $gallery) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus foto ini dari galeri?')">
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
