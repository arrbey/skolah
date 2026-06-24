@extends('layouts.admin')

@section('title', 'Manajemen Program Unggulan')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Program Unggulan</h1>
            <p class="text-sm text-gray-500">Kelola blok penjelasan program (E-learning, Bootcamp, dll) di halaman utama.</p>
        </div>
        <a href="{{ route('admin.landing-programs.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            + Tambah Program
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Urutan</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Program</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($programs as $program)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $program->order }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            @if($program->image)
                                <img src="{{ storageUrl($program->image) }}" class="w-10 h-10 rounded-lg object-cover bg-gray-100">
                            @else
                                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            @endif
                            <div>
                                <p class="text-sm font-bold text-gray-900">{{ $program->title }}</p>
                                <p class="text-xs text-gray-500">{{ $program->alignment == 'left' ? 'Gambar Kiri' : 'Gambar Kanan' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $program->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ $program->is_active ? 'Aktif' : 'Non-aktif' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('admin.landing-programs.edit', $program) }}" class="text-blue-600 hover:text-blue-800 text-sm font-bold">Edit</a>
                        <form action="{{ route('admin.landing-programs.destroy', $program) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus program ini?')">
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
