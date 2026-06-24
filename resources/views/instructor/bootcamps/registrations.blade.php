@extends('layouts.instructor')

@section('title', 'Daftar Peserta - ' . $bootcamp->title)

@section('page-header')
    <div class="flex items-center gap-3">
        <a href="{{ route('instructor.bootcamps.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-lg font-bold text-gray-900">Daftar Peserta</h1>
            <p class="text-sm text-gray-500">{{ $bootcamp->title }}</p>
        </div>
    </div>
@endsection

@section('content')
<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Lengkap</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Tgl Daftar</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($registrations as $registration)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ avatarUrl($registration->user) }}" class="w-8 h-8 rounded-full object-cover border border-gray-100" alt="">
                                <span class="font-bold text-gray-900">{{ $registration->user->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ $registration->user->email }}</td>
                        <td class="px-6 py-4 text-center text-gray-500 text-xs">
                            {{ $registration->created_at->format('d M Y H:i') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button class="px-3 py-1.5 rounded-lg bg-gray-50 text-gray-500 text-[10px] font-bold uppercase hover:bg-gray-100 transition-colors">
                                Detail
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-400 font-medium">Belum ada peserta yang mendaftar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($registrations->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            {{ $registrations->links() }}
        </div>
    @endif
</div>
@endsection
