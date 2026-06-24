@extends('layouts.instructor')

@section('title', 'Hasil ' . ucfirst($quiz->type) . ' — ' . $course->title)

@section('page-header')
    <div class="flex items-center gap-3">
        <a href="{{ route('instructor.courses.quizzes.index', $course) }}" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-lg font-bold text-gray-900">Hasil {{ ucfirst($quiz->type) }}</h1>
            <p class="text-sm text-gray-500 truncate max-w-xs">{{ $quiz->title }}</p>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Statistik Ringkasan --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white border border-gray-200 rounded-xl p-5 text-center shadow-sm">
            <div class="text-3xl font-bold text-gray-900">{{ $stats['total_attempts'] }}</div>
            <div class="text-sm text-gray-500 mt-1">Total Pengerjaan</div>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5 text-center shadow-sm">
            <div class="text-3xl font-bold text-green-600">{{ $stats['passed'] }}</div>
            <div class="text-sm text-gray-500 mt-1">Lulus</div>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5 text-center shadow-sm">
            <div class="text-3xl font-bold text-red-500">{{ $stats['total_attempts'] - $stats['passed'] }}</div>
            <div class="text-sm text-gray-500 mt-1">Tidak Lulus</div>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5 text-center shadow-sm">
            <div class="text-3xl font-bold {{ $quiz->type === 'pretest' ? 'text-blue-600' : 'text-purple-600' }}">{{ $stats['avg_score'] }}%</div>
            <div class="text-sm text-gray-500 mt-1">Rata-rata Nilai</div>
        </div>
    </div>

    {{-- Tabel Hasil --}}
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Riwayat Pengerjaan Siswa</h3>
        </div>

        @if($attempts->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left">
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Siswa</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Nilai</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Poin</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Durasi</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Waktu Selesai</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($attempts as $attempt)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600 flex-shrink-0">
                                    {{ strtoupper(substr($attempt->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $attempt->user->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $attempt->user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span class="text-lg font-bold {{ $attempt->passed ? 'text-green-600' : 'text-red-500' }}">
                                    {{ $attempt->score }}%
                                </span>
                            </div>
                            <div class="w-24 bg-gray-100 rounded-full h-1.5 mt-1.5">
                                <div class="h-1.5 rounded-full {{ $attempt->passed ? 'bg-green-500' : 'bg-red-400' }}"
                                     style="width: {{ min($attempt->score, 100) }}%"></div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            {{ $attempt->earned_points }} / {{ $attempt->total_points }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold
                                {{ $attempt->passed ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $attempt->passed ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                {{ $attempt->passed ? 'Lulus' : 'Tidak Lulus' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            {{ $attempt->duration ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-gray-500">
                            {{ $attempt->completed_at ? $attempt->completed_at->translatedFormat('d M Y, H:i') : '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($attempts->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $attempts->links() }}
        </div>
        @endif

        @else
        <div class="text-center py-16">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 mb-1">Belum ada yang mengerjakan</h3>
            <p class="text-gray-500 text-sm">Siswa yang terdaftar belum mengerjakan quiz ini.</p>
        </div>
        @endif
    </div>

</div>
@endsection
