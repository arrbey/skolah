@extends('layouts.admin')

@section('title', 'Kelola Pengguna')

@section('page-header')
    <div class="flex items-center justify-between w-full">
        <span class="text-base font-semibold text-gray-900">Pengguna</span>
        <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary-600 text-white text-sm font-bold hover:bg-primary-700 shadow-sm transition-all focus:ring-2 focus:ring-primary-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Pengguna
        </a>
    </div>
@endsection

@section('content')
    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 font-medium">Total</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 font-medium">User</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($stats['users']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 font-medium">Instructor</p>
            <p class="text-2xl font-bold text-purple-600 mt-1">{{ number_format($stats['instructors']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 font-medium">Admin</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['admins'] }}</p>
        </div>
    </div>

    {{-- Filter & Export --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-4 mb-6">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <input type="text" name="search" placeholder="Cari nama / email..." value="{{ request('search') }}"
                   class="flex-1 min-w-[200px] rounded-xl border border-gray-300 px-4 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            <select name="role" class="rounded-xl border border-gray-300 px-4 py-2 text-sm">
                <option value="">Semua Role</option>
                <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User</option>
                <option value="instructor" {{ request('role') === 'instructor' ? 'selected' : '' }}>Instructor</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
            <select name="status" class="rounded-xl border border-gray-300 px-4 py-2 text-sm">
                <option value="">Semua Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
            </select>
            <select name="verification" class="rounded-xl border border-gray-300 px-4 py-2 text-sm">
                <option value="">Semua Verifikasi</option>
                <option value="verified" {{ request('verification') === 'verified' ? 'selected' : '' }}>Terverifikasi</option>
                <option value="unverified" {{ request('verification') === 'unverified' ? 'selected' : '' }}>Belum Verifikasi</option>
            </select>
            <button type="submit" class="px-4 py-2 rounded-xl bg-primary-600 text-white text-sm font-medium hover:bg-primary-700">Filter</button>
            @if(request()->hasAny(['search','role','status','verification']))
                <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Reset</a>
            @endif
        </form>
        <div class="flex items-center gap-2 mt-3 pt-3 border-t border-gray-100">
            <span class="text-xs text-gray-500 font-medium">Export:</span>
            <a href="{{ route('admin.users.export', array_merge(request()->query(), ['format' => 'excel'])) }}"
               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-green-50 text-green-700 text-xs font-medium hover:bg-green-100">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Excel
            </a>
            <a href="{{ route('admin.users.export', array_merge(request()->query(), ['format' => 'pdf'])) }}"
               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-red-50 text-red-700 text-xs font-medium hover:bg-red-100">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                PDF
            </a>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">User</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Role</th>
                        <th class="text-center px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Orders</th>
                        <th class="text-center px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Enrollments</th>
                        <th class="text-center px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="text-center px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Verifikasi</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Terdaftar</th>
                        <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $user->avatar_url }}" class="w-8 h-8 rounded-full object-cover" alt="">
                                    <div class="min-w-0">
                                        <p class="font-medium text-gray-900 truncate">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-500 truncate">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-3">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold
                                    {{ $user->role === 'admin' ? 'bg-red-100 text-red-700' : '' }}
                                    {{ $user->role === 'instructor' ? 'bg-purple-100 text-purple-700' : '' }}
                                    {{ $user->role === 'user' ? 'bg-blue-100 text-blue-700' : '' }}
                                ">{{ ucfirst($user->role) }}</span>
                            </td>
                            <td class="px-6 py-3 text-center text-gray-600">{{ $user->orders_count }}</td>
                            <td class="px-6 py-3 text-center text-gray-600">{{ $user->enrollments_count }}</td>
                            <td class="px-6 py-3 text-center">
                                @if($user->is_suspended)
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">Suspended</span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">Aktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-center">
                                @if($user->email_verified_at)
                                    <div class="flex flex-col items-center">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            Terverifikasi
                                        </span>
                                        <span class="text-[10px] text-gray-400 mt-0.5">{{ $user->email_verified_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                @else
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">Belum Verifikasi</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-gray-500 text-xs">{{ $user->created_at->format('d/m/Y') }}</td>
                            <td class="px-6 py-3 text-right">
                                    @if($user->role !== 'admin')
                                        @if(!$user->email_verified_at)
                                            <form action="{{ route('admin.users.verify', $user) }}" method="POST" class="inline" onsubmit="return confirm('Verifikasi manual user {{ $user->name }}?')">
                                                @csrf @method('PATCH')
                                                <button class="px-3 py-1 rounded-lg bg-blue-50 text-blue-700 text-xs font-medium hover:bg-blue-100 inline-flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    Acc Verif
                                                </button>
                                            </form>
                                        @endif

                                        @if($user->is_suspended)
                                            <form action="{{ route('admin.users.activate', $user) }}" method="POST" class="inline">
                                                @csrf @method('PATCH')
                                                <button class="px-3 py-1 rounded-lg bg-green-50 text-green-700 text-xs font-medium hover:bg-green-100">Aktifkan</button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.users.suspend', $user) }}" method="POST" class="inline" onsubmit="return confirm('Suspend user ini?')">
                                                @csrf @method('PATCH')
                                                <button class="px-3 py-1 rounded-lg bg-amber-50 text-amber-700 text-xs font-medium hover:bg-amber-100">Suspend</button>
                                            </form>
                                        @endif

                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Hapus user ini? Ini akan menyembunyikan user dari sistem (Soft Delete).')">
                                            @csrf @method('DELETE')
                                            <button class="px-3 py-1 rounded-lg bg-red-50 text-red-700 text-xs font-medium hover:bg-red-100">Hapus</button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-400">—</span>
                                    @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-6 py-8 text-center text-gray-400">Tidak ada pengguna ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $users->withQueryString()->links() }}</div>
@endsection
