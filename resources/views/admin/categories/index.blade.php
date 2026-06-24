@extends('layouts.admin')

@section('title', 'Kelola Kategori')

@section('page-header')
    <div class="flex items-center justify-between w-full">
        <span class="text-base font-semibold text-gray-900">Kategori</span>
        <a href="{{ route('admin.categories.create') }}" class="px-4 py-2 rounded-xl bg-primary-600 text-white text-sm font-medium hover:bg-primary-700">+ Tambah Kategori</a>
    </div>
@endsection

@section('content')
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Kategori</th>
                        <th class="text-center px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Sub-Kategori</th>
                        <th class="text-center px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Kursus</th>
                        <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($categories as $category)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-2">
                                    @if($category->icon)
                                        <span class="text-lg">{{ $category->icon }}</span>
                                    @endif
                                    <span class="font-medium text-gray-900">{{ $category->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-3 text-center text-gray-600">{{ $category->children->count() }}</td>
                            <td class="px-6 py-3 text-center text-gray-600">{{ $category->courses_count }}</td>
                            <td class="px-6 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="px-2.5 py-1 rounded-lg bg-gray-100 text-gray-700 text-xs font-medium hover:bg-gray-200">Edit</a>
                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Hapus kategori ini?')">
                                        @csrf @method('DELETE')
                                        <button class="px-2.5 py-1 rounded-lg bg-red-50 text-red-700 text-xs font-medium hover:bg-red-100">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        {{-- Children --}}
                        @foreach($category->children as $child)
                            <tr class="bg-gray-50/50 hover:bg-gray-100/50">
                                <td class="px-6 py-2 pl-12">
                                    <div class="flex items-center gap-2">
                                        <span class="text-gray-300">└</span>
                                        @if($child->icon)
                                            <span class="text-base">{{ $child->icon }}</span>
                                        @endif
                                        <span class="text-gray-700">{{ $child->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-2 text-center text-gray-400">—</td>
                                <td class="px-6 py-2 text-center text-gray-600">{{ $child->courses_count ?? 0 }}</td>
                                <td class="px-6 py-2 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.categories.edit', $child) }}" class="px-2.5 py-1 rounded-lg bg-gray-100 text-gray-600 text-xs font-medium hover:bg-gray-200">Edit</a>
                                        <form action="{{ route('admin.categories.destroy', $child) }}" method="POST" class="inline" onsubmit="return confirm('Hapus sub-kategori ini?')">
                                            @csrf @method('DELETE')
                                            <button class="px-2.5 py-1 rounded-lg bg-red-50 text-red-700 text-xs font-medium hover:bg-red-100">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr><td colspan="4" class="px-6 py-8 text-center text-gray-400">Belum ada kategori.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
