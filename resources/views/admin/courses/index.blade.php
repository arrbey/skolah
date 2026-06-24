@extends('layouts.admin')

@section('title', 'Kelola Kursus')

@section('page-header')
    <div class="flex items-center justify-between w-full">
        <span class="text-base font-semibold text-gray-900">Kursus</span>
        <a href="{{ route('admin.courses.create') }}" class="px-4 py-2 rounded-xl bg-primary-600 text-white text-sm font-medium hover:bg-primary-700">+ Tambah Kursus</a>
    </div>
@endsection

@section('content')
    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 font-medium">Total</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 font-medium">Published</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['published'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 font-medium">Draft</p>
            <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $stats['draft'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 font-medium">Unggulan</p>
            <p class="text-2xl font-bold text-primary-600 mt-1">{{ $stats['featured'] }}</p>
        </div>
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-4 mb-6">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <input type="text" name="search" placeholder="Cari kursus..." value="{{ request('search') }}"
                   class="flex-1 min-w-[200px] rounded-xl border border-gray-300 px-4 py-2 text-sm focus:ring-2 focus:ring-primary-500">
            <select name="status" class="rounded-xl border border-gray-300 px-4 py-2 text-sm">
                <option value="">Semua Status</option>
                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
            </select>
            <button type="submit" class="px-4 py-2 rounded-xl bg-primary-600 text-white text-sm font-medium hover:bg-primary-700">Filter</button>
            @if(request()->hasAny(['search','status']))
                <a href="{{ route('admin.courses.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Reset</a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Kursus</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Lembaga</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Instructor</th>
                        <th class="text-center px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Siswa</th>
                        <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Harga</th>
                        <th class="text-center px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="text-center px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Unggulan</th>
                        <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($courses as $course)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $course->thumbnail_url }}" class="w-14 h-10 rounded-lg object-cover shrink-0" alt="">
                                    <div class="min-w-0">
                                        <p class="font-medium text-gray-900 truncate max-w-[200px]">{{ $course->title }}</p>
                                        <p class="text-xs text-gray-400">{{ $course->category?->name ?? '-' }} · {{ $course->level_label }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-3">
                                @if($course->institution)
                                    <div class="flex items-center gap-1.5">
                                        <img src="{{ $course->institution->logo_url }}" class="w-5 h-5 rounded object-contain border border-gray-100" alt="">
                                        <span class="text-gray-600">{{ $course->institution->name }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-400 italic">Umum</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-gray-600">{{ $course->instructor?->name ?? '-' }}</td>
                            <td class="px-6 py-3 text-center">
                                <a href="{{ route('admin.courses.enrollments.index', $course) }}"
                                   class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-primary-700 hover:bg-primary-50 font-medium"
                                   title="Kelola enrollment">
                                    {{ $course->enrollments_count }}
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                            <td class="px-6 py-3 text-right font-semibold text-gray-900">{{ rupiah($course->price) }}</td>
                            <td class="px-6 py-3 text-center">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold
                                    {{ $course->status === 'published' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}
                                ">{{ ucfirst($course->status) }}</span>
                            </td>
                            <td class="px-6 py-3 text-center">
                                <form action="{{ route('admin.courses.toggle-featured', $course) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <button class="transition-colors {{ $course->is_featured ? 'text-yellow-500 hover:text-yellow-600' : 'text-gray-300 hover:text-yellow-500' }}">
                                        <svg class="w-5 h-5" fill="{{ $course->is_featured ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                    </button>
                                </form>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.courses.blast', $course) }}"
                                       class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 text-xs font-medium hover:bg-blue-100" title="Blast Email">
                                        <svg class="w-3.5 h-3.5 inline -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        Blast
                                    </a>
                                    @if($course->status === 'draft')
                                        <form action="{{ route('admin.courses.approve', $course) }}" method="POST" class="inline">
                                            @csrf @method('PATCH')
                                            <button class="px-2.5 py-1 rounded-lg bg-green-50 text-green-700 text-xs font-medium hover:bg-green-100">Publish</button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.courses.reject', $course) }}" method="POST" class="inline">
                                            @csrf @method('PATCH')
                                            <button class="px-2.5 py-1 rounded-lg bg-yellow-50 text-yellow-700 text-xs font-medium hover:bg-yellow-100">Draft</button>
                                        </form>
                                    @endif
                                    <a href="{{ route('admin.courses.edit', $course) }}"
                                       class="px-2.5 py-1 rounded-lg bg-gray-100 text-gray-700 text-xs font-medium hover:bg-gray-200">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.courses.destroy', $course) }}" method="POST" class="inline" onsubmit="return confirm('Hapus kursus ini?')">
                                        @csrf @method('DELETE')
                                        <button class="px-2.5 py-1 rounded-lg bg-red-50 text-red-700 text-xs font-medium hover:bg-red-100">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-6 py-8 text-center text-gray-400">Tidak ada kursus ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $courses->withQueryString()->links() }}</div>
@endsection
