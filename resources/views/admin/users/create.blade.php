@extends('layouts.admin')

@section('title', 'Tambah Pengguna Baru')

@section('page-header')
    <span class="text-base font-semibold text-gray-900">Tambah Pengguna</span>
@endsection

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
        {{-- Single Invite --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-lg font-bold text-gray-900">Undang Satuan</h3>
                <p class="text-sm text-gray-500 mt-1">Sistem akan men-generate password otomatis dan mengirimkan detail akun via email.</p>
            </div>

            <form action="{{ route('admin.users.store') }}" method="POST" class="p-6 space-y-5">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-primary-500 focus:border-primary-500 text-sm py-2.5"
                           placeholder="Masukkan nama lengkap...">
                    @error('name') <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Alamat Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                           class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-primary-500 focus:border-primary-500 text-sm py-2.5"
                           placeholder="email@contoh.com">
                    @error('email') <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="role" class="block text-sm font-semibold text-gray-700 mb-1.5">Role / Peran</label>
                    <select name="role" id="role" required
                            class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-primary-500 focus:border-primary-500 text-sm py-2.5">
                        <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>User (Siswa)</option>
                        <option value="instructor" {{ old('role') === 'instructor' ? 'selected' : '' }}>Instructor (Pengajar)</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                    </select>
                    @error('role') <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full flex justify-center py-3 px-4 rounded-xl shadow-sm text-sm font-bold text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all">
                        Undang User
                    </button>
                    <p class="text-center text-[10px] text-gray-400 mt-4 leading-relaxed uppercase tracking-wider font-bold">
                        Hanya untuk penambahan satu akun saja.
                    </p>
                </div>
            </form>
        </div>

        {{-- Bulk Invite --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Undang Massal (Bulk)</h3>
                        <p class="text-sm text-gray-500 mt-1">Gunakan file CSV untuk mengundang banyak pengguna sekaligus.</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <!-- Template Link -->
                <div class="mb-6 p-4 rounded-xl bg-blue-50 border border-blue-100 flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div class="flex-1">
                        <p class="text-sm font-bold text-blue-900 leading-tight">Gunakan Format yang Benar</p>
                        <p class="text-xs text-blue-700 mt-1 mb-3">Mohon unduh template CSV di bawah ini agar sistem dapat membaca data Anda dengan benar.</p>
                        <a href="{{ route('admin.users.template') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-600 text-white text-xs font-bold hover:bg-blue-700 shadow-sm transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Unduh Template CSV
                        </a>
                    </div>
                </div>

                <form action="{{ route('admin.users.import') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    <div>
                        <label for="file" class="block text-sm font-semibold text-gray-700 mb-2">Pilih File CSV</label>
                        <div class="relative">
                            <input type="file" name="file" id="file" accept=".csv" required
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 border border-gray-200 rounded-xl">
                        </div>
                        <p class="mt-2 text-xs text-gray-400 font-medium">Hanya mendukung format .csv (Max 2MB)</p>
                        @error('file') <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full flex justify-center py-3 px-4 rounded-xl shadow-sm text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                            Unggah & Proses Undangan
                        </button>
                    </div>
                </form>

                <div class="mt-6 border-t border-gray-100 pt-6">
                    <p class="text-xs font-bold text-gray-900 mb-3">Aturan Impor:</p>
                    <ul class="space-y-2">
                        <li class="flex items-start gap-2 text-xs text-gray-600">
                            <svg class="w-3.5 h-3.5 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Email yang sudah terdaftar akan otomatis dilewati.
                        </li>
                        <li class="flex items-start gap-2 text-xs text-gray-600">
                            <svg class="w-3.5 h-3.5 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Password akan di-generate otomatis untuk setiap user.
                        </li>
                        <li class="flex items-start gap-2 text-xs text-gray-600">
                            <svg class="w-3.5 h-3.5 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Maksimal 100 baris per unggahan untuk performa stabil.
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
