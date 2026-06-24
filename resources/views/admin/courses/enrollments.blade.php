@extends('layouts.admin')

@section('title', 'Enrollments — ' . $course->title)

@section('page-header')
    <div class="flex items-center justify-between w-full">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.courses.index') }}" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <p class="text-xs text-gray-500">Enrollments Manual</p>
                <p class="text-base font-semibold text-gray-900 line-clamp-1">{{ $course->title }}</p>
            </div>
        </div>
        <a href="{{ route('admin.courses.edit', $course) }}" class="px-3 py-1.5 rounded-lg bg-gray-100 text-gray-700 text-xs font-medium hover:bg-gray-200">
            Edit Kursus
        </a>
    </div>
@endsection

@section('content')
    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mb-4 p-3 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 font-medium">Total Enrolled</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 font-medium">Selesai</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['completed'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 font-medium">Sedang Belajar</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['in_progress'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 font-medium">Belum Dimulai</p>
            <p class="text-2xl font-bold text-gray-500 mt-1">{{ $stats['not_started'] }}</p>
        </div>
    </div>

    {{-- Form Tambah Enrollment --}}
    <div x-data="enrollForm()" class="bg-white rounded-2xl border border-gray-200 p-5 mb-6">
        <div class="flex items-start gap-3 mb-4">
            <div class="w-10 h-10 rounded-xl bg-primary-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900">Tambah Enrollment Manual</h3>
                <p class="text-xs text-gray-500 mt-0.5">Daftarkan user yang membeli offline/di luar website ke kursus ini.</p>
            </div>
        </div>

        <form action="{{ route('admin.courses.enrollments.store', $course) }}" method="POST" class="space-y-4">
            @csrf

            {{-- User Search --}}
            <div class="relative">
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">User <span class="text-red-500">*</span></label>

                <div x-show="!selectedUser" class="relative">
                    <input type="text"
                           x-model="query"
                           @input.debounce.300ms="searchUsers"
                           @focus="showDropdown = true"
                           placeholder="Ketik nama atau email user (min. 2 karakter)..."
                           class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">

                    <div x-show="showDropdown && results.length > 0"
                         @click.outside="showDropdown = false"
                         class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg max-h-64 overflow-y-auto">
                        <template x-for="user in results" :key="user.id">
                            <button type="button"
                                    @click="selectUser(user)"
                                    class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 text-left border-b border-gray-50 last:border-0">
                                <img :src="user.avatar" class="w-8 h-8 rounded-full object-cover" alt="">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900 truncate" x-text="user.name"></p>
                                    <p class="text-xs text-gray-500 truncate" x-text="user.email"></p>
                                </div>
                            </button>
                        </template>
                    </div>

                    <p x-show="searching" class="text-xs text-gray-400 mt-1">Mencari...</p>
                    <p x-show="!searching && query.length >= 2 && results.length === 0" class="text-xs text-gray-400 mt-1">Tidak ada user cocok (atau sudah terdaftar).</p>
                </div>

                <div x-show="selectedUser" class="flex items-center gap-3 px-4 py-2.5 bg-primary-50 border border-primary-200 rounded-xl">
                    <img :src="selectedUser?.avatar" class="w-9 h-9 rounded-full object-cover" alt="">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-gray-900 truncate" x-text="selectedUser?.name"></p>
                        <p class="text-xs text-gray-600 truncate" x-text="selectedUser?.email"></p>
                    </div>
                    <button type="button" @click="clearUser" class="p-1 rounded-lg hover:bg-white text-gray-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <input type="hidden" name="user_id" :value="selectedUser?.id || ''">
            </div>

            {{-- Variant (jika ada) --}}
            @if($course->variants->isNotEmpty())
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Varian (opsional)</label>
                    <select name="course_variant_id" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                        <option value="">— Tanpa varian —</option>
                        @foreach($course->variants as $variant)
                            <option value="{{ $variant->id }}">{{ $variant->name }} — {{ rupiah($variant->effective_price ?? $variant->price) }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            {{-- Notifikasi --}}
            <label class="flex items-start gap-2.5 cursor-pointer">
                <input type="checkbox" name="send_notification" value="1" checked
                       class="mt-0.5 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                <div>
                    <p class="text-sm font-medium text-gray-900">Kirim notifikasi ke user</p>
                    <p class="text-xs text-gray-500">Email konfirmasi enrollment + notifikasi in-app.</p>
                </div>
            </label>

            <div class="flex justify-end pt-2 border-t border-gray-100">
                <button type="submit"
                        :disabled="!selectedUser"
                        :class="!selectedUser ? 'opacity-50 cursor-not-allowed' : ''"
                        class="px-5 py-2 rounded-xl bg-primary-600 text-white text-sm font-medium hover:bg-primary-700">
                    + Daftarkan User
                </button>
            </div>
        </form>
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-4 mb-4">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <input type="text" name="search" placeholder="Cari nama atau email..." value="{{ request('search') }}"
                   class="flex-1 min-w-[200px] rounded-xl border border-gray-300 px-4 py-2 text-sm focus:ring-2 focus:ring-primary-500">
            <button type="submit" class="px-4 py-2 rounded-xl bg-primary-600 text-white text-sm font-medium hover:bg-primary-700">
                Cari
            </button>
            @if(request('search'))
                <a href="{{ route('admin.courses.enrollments.index', $course) }}" class="px-4 py-2 rounded-xl bg-gray-100 text-gray-700 text-sm hover:bg-gray-200">Reset</a>
            @endif
        </form>
    </div>

    {{-- List Enrollments --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">User</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Varian</th>
                        <th class="text-center px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Progres</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Terdaftar</th>
                        <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($enrollments as $enrollment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $enrollment->user->avatar_url }}" class="w-8 h-8 rounded-full object-cover" alt="">
                                    <div class="min-w-0">
                                        <p class="font-medium text-gray-900 truncate">{{ $enrollment->user->name }}</p>
                                        <p class="text-xs text-gray-500 truncate">{{ $enrollment->user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-3 text-gray-600">
                                {{ $enrollment->variant?->name ?? '—' }}
                            </td>
                            <td class="px-6 py-3 text-center">
                                <div class="inline-flex items-center gap-2">
                                    <div class="w-20 bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-primary-500 h-full" style="width: {{ $enrollment->progress_percentage }}%"></div>
                                    </div>
                                    <span class="text-xs font-medium text-gray-700">{{ $enrollment->progress_percentage }}%</span>
                                </div>
                                @if($enrollment->is_completed)
                                    <p class="text-[10px] text-green-600 mt-1">✓ Selesai</p>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-gray-600">
                                {{ $enrollment->enrolled_at?->format('d M Y H:i') ?? '—' }}
                            </td>
                            <td class="px-6 py-3 text-right">
                                <form action="{{ route('admin.courses.enrollments.destroy', [$course, $enrollment]) }}"
                                      method="POST"
                                      class="inline"
                                      onsubmit="return confirm('Hapus enrollment {{ addslashes($enrollment->user->name) }} dari kursus ini?\n\nProgres belajar user akan hilang.')">
                                    @csrf @method('DELETE')
                                    <button class="px-2.5 py-1 rounded-lg bg-red-50 text-red-700 text-xs font-medium hover:bg-red-100">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-400">
                                <p class="text-sm">Belum ada user terdaftar di kursus ini.</p>
                                <p class="text-xs mt-1">Gunakan form di atas untuk daftarkan user secara manual.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $enrollments->links() }}</div>

    @push('scripts')
    <script nonce="{{ $cspNonce ?? '' }}">
        function enrollForm() {
            return {
                query: '',
                results: [],
                selectedUser: null,
                searching: false,
                showDropdown: false,

                async searchUsers() {
                    if (this.query.length < 2) {
                        this.results = [];
                        return;
                    }
                    this.searching = true;
                    try {
                        const res = await fetch(`{{ route('admin.courses.enrollments.search-users', $course) }}?q=${encodeURIComponent(this.query)}`, {
                            headers: { 'Accept': 'application/json' },
                        });
                        this.results = await res.json();
                        this.showDropdown = true;
                    } catch (e) {
                        this.results = [];
                    } finally {
                        this.searching = false;
                    }
                },

                selectUser(user) {
                    this.selectedUser = user;
                    this.query = '';
                    this.results = [];
                    this.showDropdown = false;
                },

                clearUser() {
                    this.selectedUser = null;
                },
            };
        }
    </script>
    @endpush
@endsection
