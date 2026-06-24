@extends('layouts.instructor')

@section('title', 'Progres Siswa - ' . $course->title)

@section('page-header')
    <div class="flex items-center gap-3">
        <a href="{{ route('instructor.courses.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-lg font-bold text-gray-900">Progres Siswa</h1>
            <p class="text-sm text-gray-500">{{ $course->title }}</p>
        </div>
    </div>
@endsection

@section('content')
<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Siswa</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Tgl Daftar</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Progres Belajar</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($enrollments as $enrollment)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ avatarUrl($enrollment->user) }}" class="w-8 h-8 rounded-full object-cover ring-2 ring-gray-100" alt="">
                                <span class="font-bold text-gray-900">{{ $enrollment->user->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ $enrollment->user->email }}</td>
                        <td class="px-6 py-4 text-center text-gray-500 text-xs">
                            {{ $enrollment->enrolled_at->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4">
                            @php 
                                $progress = $enrollment->progress_percent ?? 0; 
                                $color = $progress >= 100 ? 'bg-green-500' : ($progress > 0 ? 'bg-primary-500' : 'bg-gray-200');
                            @endphp
                            <div class="flex flex-col gap-1.5 min-w-[120px]">
                                <div class="flex items-center justify-between text-[10px] font-bold text-gray-400 uppercase">
                                    <span>{{ $progress }}% Selesai</span>
                                </div>
                                <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full {{ $color }} transition-all duration-500" style="width: {{ $progress }}%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button class="px-3 py-1.5 rounded-lg bg-gray-100 text-gray-600 text-[10px] font-bold uppercase hover:bg-gray-200 transition-colors">
                                Hubungi Siswa
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400 font-medium">Belum ada siswa yang mendaftar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($enrollments->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            {{ $enrollments->links() }}
        </div>
    @endif
</div>
@endsection
