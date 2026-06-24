@extends('layouts.instructor')

@section('title', 'Kursus Saya')

@section('page-header')
    <div class="flex items-center justify-between w-full">
        <div>
            <h1 class="text-lg font-bold text-gray-900">Kelola Kursus</h1>
            <p class="text-sm text-gray-500">Kelola konten dan pantau progres siswa Anda</p>
        </div>
        <a href="{{ route('instructor.courses.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary-600 text-white text-sm font-bold hover:bg-primary-700 transition-all shadow-md shadow-primary-100">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Kursus Baru
        </a>
    </div>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Total Kursus</p>
            <p class="text-2xl font-black text-gray-900 mt-1">{{ $totalCourses }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Siswa Aktif</p>
            @php $totalStudents = $courses->sum('enrollments_count'); @endphp
            <p class="text-2xl font-black text-primary-600 mt-1">{{ $totalStudents }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
        <form method="GET" action="{{ route('instructor.courses.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari kursus..."
                       class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
            </div>
            <button type="submit"
                    class="px-5 py-2.5 rounded-xl bg-primary-600 text-white text-sm font-bold hover:bg-primary-700 transition-colors">
                Cari
            </button>
        </form>
    </div>

    {{-- Course List --}}
    @if($courses->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center">
            <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-1">Belum ada kursus</h3>
            <p class="text-sm text-gray-500 mb-6">Mulai buat kursus pertama Anda dan bagikan ilmu Anda.</p>
            <a href="{{ route('instructor.courses.create') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-primary-600 text-white text-sm font-bold hover:bg-primary-700 transition-all">
                Buat Kursus Pertama
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($courses as $course)
                <div class="bg-white rounded-2xl border border-gray-200 p-5 hover:shadow-lg transition-all group">
                    <div class="flex gap-4">
                        {{-- Thumbnail --}}
                        <div class="relative w-32 h-20 shrink-0">
                            <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}"
                                 class="w-full h-full rounded-xl object-cover border border-gray-100">
                            @if($course->status === 'draft')
                                <div class="absolute top-1 right-1">
                                    <span class="px-2 py-0.5 rounded-lg bg-yellow-400 text-[8px] font-black uppercase text-white shadow-sm">Draft</span>
                                </div>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-gray-900 truncate group-hover:text-primary-600 transition-colors">{{ $course->title }}</h3>
                            <p class="text-xs text-gray-500 mt-1">{{ $course->category?->name ?? 'Uncategorized' }}</p>
                            
                            <div class="flex items-center gap-3 mt-3">
                                <span class="flex items-center gap-1 text-[10px] font-bold text-gray-400 uppercase">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1z"/></svg>
                                    {{ $course->enrollments_count }} Siswa
                                </span>
                                <span class="flex items-center gap-1 text-[10px] font-bold text-gray-400 uppercase">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                                    {{ $course->sections_count }} Materi
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-2 mt-5 pt-4 border-t border-gray-50">
                        <a href="{{ route('instructor.courses.edit', $course->id) }}"
                           class="flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-gray-50 text-gray-600 text-[10px] font-bold hover:bg-primary-50 hover:text-primary-600 transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Edit
                        </a>
                        <a href="{{ route('instructor.courses.lessons', $course->id) }}"
                           class="flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-gray-50 text-gray-600 text-[10px] font-bold hover:bg-secondary-50 hover:text-secondary-600 transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            Materi
                        </a>
                        <a href="{{ route('instructor.courses.quizzes.index', $course->id) }}"
                           class="flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-gray-50 text-gray-600 text-[10px] font-bold hover:bg-amber-50 hover:text-amber-600 transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            Kuis
                        </a>
                    </div>

                    <div class="flex items-center gap-2 mt-2">
                        <a href="{{ route('instructor.courses.students', $course->id) }}"
                           class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-primary-600 text-white text-xs font-bold hover:bg-primary-700 transition-all shadow-md shadow-primary-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            Progres Siswa
                        </a>
                        <form action="{{ route('instructor.courses.destroy', $course->id) }}" method="POST" onsubmit="return confirm('Hapus kursus ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-2.5 rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $courses->links() }}
        </div>
    @endif
</div>
@endsection
