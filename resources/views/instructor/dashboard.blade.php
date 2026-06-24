@extends('layouts.instructor')

@section('title', 'Dashboard Instruktur')

@section('page-header')
    <div>
        <h1 class="text-lg font-bold text-gray-900">Dashboard</h1>
        <p class="text-sm text-gray-500">Selamat datang, {{ auth()->user()->name }}!</p>
    </div>
@endsection

@section('content')
<div class="space-y-6">

    {{-- ── Stats Cards ──────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-primary-50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($totalStudents) }}</p>
            <p class="text-sm text-gray-500 mt-0.5">Total Siswa</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-secondary-50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-secondary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-0.5 rounded-full">{{ $publishedCourses }} live</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $totalCourses }}</p>
            <p class="text-sm text-gray-500 mt-0.5">Total Kursus</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ rupiah_short($totalEarnings) }}</p>
            <p class="text-sm text-gray-500 mt-0.5">Total Pendapatan</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ rupiah_short($monthlyEarnings) }}</p>
            <p class="text-sm text-gray-500 mt-0.5">Pendapatan Bulan Ini</p>
        </div>
    </div>

    {{-- ── Quick Actions ────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <a href="{{ route('instructor.courses.create') }}"
           class="flex items-center gap-3 bg-primary-600 text-white rounded-xl px-4 py-3 hover:bg-primary-700 transition-colors">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span class="text-sm font-medium">Kursus Baru</span>
        </a>
        <a href="{{ route('instructor.bootcamps.create') }}"
           class="flex items-center gap-3 bg-secondary-600 text-white rounded-xl px-4 py-3 hover:bg-secondary-700 transition-colors">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span class="text-sm font-medium">Bootcamp Baru</span>
        </a>
        <a href="{{ route('instructor.books.create') }}"
           class="flex items-center gap-3 bg-amber-600 text-white rounded-xl px-4 py-3 hover:bg-amber-700 transition-colors">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span class="text-sm font-medium">Buku Baru</span>
        </a>
        <a href="{{ route('instructor.earnings') }}"
           class="flex items-center gap-3 bg-green-600 text-white rounded-xl px-4 py-3 hover:bg-green-700 transition-colors">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span class="text-sm font-medium">Laporan</span>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- ── Earnings Chart ───────────────────────────────────────────────── --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-900">Grafik Pendapatan</h2>
                <span class="text-sm text-gray-500">6 bulan terakhir</span>
            </div>
            <div class="h-64">
                <canvas id="earningsChart"></canvas>
            </div>
        </div>

        {{-- ── Popular Courses ──────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Kursus Populer</h2>
            @if($popularCourses->isEmpty())
                <p class="text-sm text-gray-400 text-center py-8">Belum ada kursus.</p>
            @else
                <div class="space-y-3">
                    @foreach($popularCourses as $course)
                        <a href="{{ route('instructor.courses.edit', $course->id) }}"
                           class="flex items-center gap-3 p-2 rounded-xl hover:bg-gray-50 transition-colors group">
                            <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}"
                                 class="w-12 h-12 rounded-lg object-cover shrink-0">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900 truncate group-hover:text-primary-600">{{ $course->title }}</p>
                                <p class="text-xs text-gray-500">{{ $course->total_students }} siswa · {{ $course->rating ? number_format($course->rating, 1) . '★' : 'Belum ada rating' }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ── Recent Enrollments ───────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-900">Enrollment Terbaru</h2>
            <a href="{{ route('instructor.courses.index') }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">Lihat Semua →</a>
        </div>
        @if($recentEnrollments->isEmpty())
            <p class="text-sm text-gray-400 text-center py-8">Belum ada enrollment.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left py-3 px-2 font-medium text-gray-500">Siswa</th>
                            <th class="text-left py-3 px-2 font-medium text-gray-500">Kursus</th>
                            <th class="text-left py-3 px-2 font-medium text-gray-500">Progress</th>
                            <th class="text-left py-3 px-2 font-medium text-gray-500">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($recentEnrollments as $enrollment)
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-2">
                                    <div class="flex items-center gap-2">
                                        <img src="{{ avatarUrl($enrollment->user->avatar ?? null, $enrollment->user->name) }}"
                                             class="w-7 h-7 rounded-full object-cover">
                                        <span class="text-gray-900 font-medium truncate max-w-[120px]">{{ $enrollment->user->name }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-2">
                                    <span class="text-gray-700 truncate max-w-[200px] block">{{ $enrollment->course->title }}</span>
                                </td>
                                <td class="py-3 px-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-20 bg-gray-100 rounded-full h-1.5">
                                            <div class="bg-primary-600 h-1.5 rounded-full" style="width: {{ $enrollment->progress_percentage }}%"></div>
                                        </div>
                                        <span class="text-xs text-gray-500">{{ $enrollment->progress_percentage }}%</span>
                                    </div>
                                </td>
                                <td class="py-3 px-2 text-gray-500 whitespace-nowrap">
                                    {{ $enrollment->enrolled_at ? tanggal_singkat_indo($enrollment->enrolled_at) : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- ── Product Summary ──────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <a href="{{ route('instructor.bootcamps.index') }}" class="bg-white rounded-2xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalBootcamps }}</p>
                    <p class="text-sm text-gray-500">Bootcamp</p>
                </div>
            </div>
        </a>
        <a href="{{ route('instructor.books.index') }}" class="bg-white rounded-2xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalBooks }}</p>
                    <p class="text-sm text-gray-500">Buku</p>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script nonce="{{ $cspNonce ?? '' }}">
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('earningsChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($earningsChart['labels']) !!},
                datasets: [{
                    label: 'Pendapatan',
                    data: {!! json_encode($earningsChart['data']) !!},
                    backgroundColor: 'rgba(37, 99, 235, 0.15)',
                    borderColor: '#2563EB',
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Rp ' + context.raw.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) return 'Rp ' + (value/1000000).toFixed(1) + ' jt';
                                if (value >= 1000) return 'Rp ' + (value/1000).toFixed(0) + ' rb';
                                return 'Rp ' + value;
                            }
                        },
                        grid: { color: '#f3f4f6' }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    });
</script>
@endpush
