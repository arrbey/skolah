@extends('layouts.admin')

@section('title', 'Kelola Bootcamp')

@section('page-header')
    <div class="flex items-center justify-between w-full">
        <span class="text-base font-semibold text-gray-900">Bootcamp & Webinar</span>
        <a href="{{ route('admin.bootcamps.create') }}" class="px-4 py-2 rounded-xl bg-primary-600 text-white text-sm font-medium hover:bg-primary-700">+ Tambah Bootcamp</a>
    </div>
@endsection

@section('content')
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Bootcamp</th>
                        <th class="text-left px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Lembaga</th>
                        <th class="text-left px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Instructor</th>
                        <th class="text-center px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Tipe</th>
                        <th class="text-center px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Status</th>
                        <th class="text-right px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Harga</th>
                        <th class="text-center px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Peserta</th>
                        <th class="text-right px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($bootcamps as $bootcamp)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $bootcamp->thumbnail_url }}" class="w-12 h-8 rounded object-cover border border-gray-100" alt="">
                                    <span class="font-bold text-gray-900 truncate max-w-[200px]">{{ $bootcamp->title }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($bootcamp->institution)
                                    <div class="flex items-center gap-1.5">
                                        <img src="{{ $bootcamp->institution->logo_url }}" class="w-5 h-5 rounded object-contain border border-gray-100" alt="">
                                        <span class="text-gray-600 text-xs">{{ $bootcamp->institution->name }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-400 italic text-xs">Umum</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-600 text-xs">{{ $bootcamp->instructor->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $bootcamp->type === 'online' ? 'bg-blue-50 text-blue-700' : 'bg-green-50 text-green-700' }}">
                                    {{ $bootcamp->type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $statusColors = [
                                        'upcoming' => 'bg-yellow-50 text-yellow-700',
                                        'ongoing' => 'bg-green-50 text-green-700',
                                        'completed' => 'bg-gray-50 text-gray-500',
                                    ];
                                @endphp
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $statusColors[$bootcamp->status] ?? 'bg-gray-50 text-gray-600' }}">
                                    {{ $bootcamp->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-gray-900 font-bold">{{ rupiah($bootcamp->price) }}</td>
                            <td class="px-6 py-4 text-center text-gray-600 font-medium">{{ $bootcamp->total_registered }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.bootcamps.blast', $bootcamp) }}"
                                       class="p-1.5 rounded-lg bg-purple-50 text-purple-700 hover:bg-purple-100 transition-colors" title="Blast Email">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    </a>
                                    <a href="{{ route('admin.bootcamps.edit', $bootcamp) }}"
                                       class="px-2.5 py-1.5 rounded-lg bg-gray-100 text-gray-700 text-xs font-bold hover:bg-gray-200 transition-colors">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.bootcamps.destroy', $bootcamp) }}" method="POST" class="inline" onsubmit="return confirm('Hapus bootcamp ini?')">
                                        @csrf @method('DELETE')
                                        <button class="px-2.5 py-1.5 rounded-lg bg-red-50 text-red-700 text-xs font-bold hover:bg-red-100 transition-colors">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-400 font-medium">Belum ada bootcamp.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">
        {{ $bootcamps->links() }}
    </div>
@endsection
