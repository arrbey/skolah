@extends('layouts.dashboard')

@section('title', 'Pengaturan')

@section('page-header')
    <h1 class="text-lg font-bold text-gray-900">Pengaturan</h1>
@endsection

@section('content')
<div class="max-w-2xl space-y-6">

    {{-- ═══ AVATAR SECTION ════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-sm font-bold text-gray-900 mb-4">Foto Profil</h2>

        <div class="flex items-center gap-5">
            <img src="{{ avatarUrl($user->avatar, $user->name) }}"
                 alt="{{ $user->name }}"
                 class="w-20 h-20 rounded-full object-cover ring-4 ring-gray-100">

            <div class="flex-1">
                <form action="{{ route('dashboard.settings.avatar') }}" method="POST" enctype="multipart/form-data"
                      class="flex items-center gap-3 flex-wrap"
                      x-data="{ fileName: '' }">
                    @csrf
                    <label class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50 cursor-pointer transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Pilih Foto
                        <input type="file" name="avatar" accept="image/jpeg,image/png,image/webp" class="hidden"
                               @change="fileName = $event.target.files[0]?.name || ''">
                    </label>
                    <span x-show="fileName" x-text="fileName" class="text-xs text-gray-500 truncate max-w-[150px]"></span>
                    <button x-show="fileName" type="submit"
                            class="px-4 py-2 rounded-lg bg-primary-600 text-white text-sm font-semibold hover:bg-primary-700 transition-colors">
                        Upload
                    </button>
                </form>

                @if($user->avatar)
                    <form action="{{ route('dashboard.settings.avatar.delete') }}" method="POST" class="mt-2">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">
                            Hapus foto profil
                        </button>
                    </form>
                @endif

                <p class="text-[10px] text-gray-400 mt-1.5">JPG, PNG, atau WebP. Maks 2 MB.</p>
                @error('avatar')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- ═══ PROFILE INFO ══════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-sm font-bold text-gray-900 mb-4">Informasi Profil</h2>

        <form action="{{ route('dashboard.settings.profile') }}" method="POST" class="space-y-4">
            @csrf

            {{-- Name --}}
            <div>
                <label for="name" class="block text-xs font-semibold text-gray-700 mb-1.5">Nama Lengkap</label>
                <input type="text" id="name" name="name"
                       value="{{ old('name', $user->name) }}"
                       class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                       required>
                @error('name')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email (read-only) --}}
            <div>
                <label for="email" class="block text-xs font-semibold text-gray-700 mb-1.5">Email</label>
                <input type="email" id="email"
                       value="{{ $user->email }}"
                       class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm bg-gray-50 text-gray-500 cursor-not-allowed"
                       disabled>
                <p class="text-[10px] text-gray-400 mt-1">Email tidak bisa diubah.</p>
            </div>

            {{-- Bio --}}
            <div>
                <label for="bio" class="block text-xs font-semibold text-gray-700 mb-1.5">Bio</label>
                <textarea id="bio" name="bio" rows="3"
                          class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors resize-none"
                          placeholder="Ceritakan sedikit tentang dirimu..."
                          maxlength="500">{{ old('bio', $user->bio) }}</textarea>
                <p class="text-[10px] text-gray-400 mt-1">Maks 500 karakter.</p>
                @error('bio')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-2">
                <button type="submit"
                        class="px-6 py-2.5 rounded-lg bg-primary-600 text-white text-sm font-semibold hover:bg-primary-700 transition-colors">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    {{-- ═══ CHANGE PASSWORD ═══════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-sm font-bold text-gray-900 mb-1">Ubah Password</h2>
        <p class="text-xs text-gray-500 mb-4">Pastikan menggunakan password yang kuat dan unik.</p>

        <form action="{{ route('dashboard.settings.password') }}" method="POST" class="space-y-4">
            @csrf

            {{-- Current Password --}}
            <div>
                <label for="current_password" class="block text-xs font-semibold text-gray-700 mb-1.5">Password Saat Ini</label>
                <input type="password" id="current_password" name="current_password"
                       class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                       required autocomplete="current-password">
                @error('current_password')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- New Password --}}
            <div>
                <label for="password" class="block text-xs font-semibold text-gray-700 mb-1.5">Password Baru</label>
                <input type="password" id="password" name="password"
                       class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                       required autocomplete="new-password">
                <p class="text-[10px] text-gray-400 mt-1">Min 8 karakter, huruf besar & kecil, angka.</p>
                @error('password')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Confirm Password --}}
            <div>
                <label for="password_confirmation" class="block text-xs font-semibold text-gray-700 mb-1.5">Konfirmasi Password Baru</label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                       class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                       required autocomplete="new-password">
            </div>

            <div class="pt-2">
                <button type="submit"
                        class="px-6 py-2.5 rounded-lg bg-primary-600 text-white text-sm font-semibold hover:bg-primary-700 transition-colors">
                    Ubah Password
                </button>
            </div>
        </form>
    </div>

    {{-- ═══ ACCOUNT INFO ══════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-sm font-bold text-gray-900 mb-4">Informasi Akun</h2>
        <div class="space-y-3">
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-500">Role</span>
                <span class="font-semibold text-gray-900 capitalize">{{ $user->role }}</span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-500">Email Terverifikasi</span>
                <span class="font-semibold {{ $user->email_verified_at ? 'text-green-600' : 'text-red-500' }}">
                    {{ $user->email_verified_at ? '✅ Terverifikasi' : '❌ Belum' }}
                </span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-500">Bergabung Sejak</span>
                <span class="font-semibold text-gray-900">{{ tanggal_indo($user->created_at) }}</span>
            </div>
        </div>
    </div>

</div>
@endsection
