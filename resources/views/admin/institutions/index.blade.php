@extends('layouts.admin')

@section('title', 'Kelola Lembaga')

@section('page-header')
    <div class="flex items-center justify-between w-full">
        <span class="text-base font-semibold text-gray-900">Lembaga</span>
        <a href="{{ route('admin.institutions.create') }}" class="px-4 py-2 rounded-xl bg-primary-600 text-white text-sm font-medium hover:bg-primary-700">+ Tambah Lembaga</a>
    </div>
@endsection

@section('content')
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Lembaga</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Kursus</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Bootcamp</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($institutions as $institution)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg border border-gray-200 p-1 bg-white shrink-0">
                                        <img src="{{ $institution->logo_url }}" class="w-full h-full object-contain rounded-md" alt="{{ $institution->name }}">
                                    </div>
                                    <div>
                                        <span class="font-bold text-gray-900 block">{{ $institution->name }}</span>
                                        <span class="text-[10px] text-gray-400 font-mono">{{ $institution->slug }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded-md text-xs font-bold">
                                    {{ $institution->courses_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 bg-purple-50 text-purple-700 rounded-md text-xs font-bold">
                                    {{ $institution->bootcamps_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($institution->is_active)
                                    <span class="px-2 py-1 bg-green-50 text-green-700 rounded-full text-[10px] font-bold uppercase">Aktif</span>
                                @else
                                    <span class="px-2 py-1 bg-gray-50 text-gray-500 rounded-full text-[10px] font-bold uppercase">Non-aktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.institutions.edit', $institution) }}" class="px-3 py-1.5 rounded-lg bg-gray-100 text-gray-700 text-xs font-bold hover:bg-gray-200 transition-colors">Edit</a>
                                    <form action="{{ route('admin.institutions.destroy', $institution) }}" method="POST" class="inline" onsubmit="return confirm('Hapus lembaga ini? Seluruh kursus yang terkait akan kehilangan referensi lembaga.')">
                                        @csrf @method('DELETE')
                                        <button class="px-3 py-1.5 rounded-lg bg-red-50 text-red-700 text-xs font-bold hover:bg-red-100 transition-colors">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-8 h-8 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    <span class="text-sm font-medium">Belum ada lembaga.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($institutions->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                {{ $institutions->links() }}
            </div>
        @endif
    </div>
@endsection
