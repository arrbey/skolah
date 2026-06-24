@extends('layouts.admin')

@section('title', 'Detail Pengajuan Instruktur')

@section('page-header')
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.instructor-applications.index') }}"
           class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors">
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <span class="text-base font-semibold text-gray-900">Detail Pengajuan</span>
    </div>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Applicant Info --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-900">Data Pemohon</h2>
            <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold
                @if($application->status === 'pending') bg-yellow-50 text-yellow-700
                @elseif($application->status === 'approved') bg-green-50 text-green-700
                @elseif($application->status === 'rejected') bg-red-50 text-red-700
                @endif">
                {{ $application->status_label }}
            </span>
        </div>

        <div class="p-6 space-y-5">
            {{-- User profile --}}
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-bold text-xl flex-shrink-0">
                    {{ strtoupper(substr($application->user->name ?? '?', 0, 1)) }}
                </div>
                <div>
                    <p class="text-base font-semibold text-gray-900">{{ $application->user->name }}</p>
                    <p class="text-sm text-gray-500">{{ $application->user->email }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">Bergabung {{ $application->user->created_at->translatedFormat('d F Y') }}</p>
                </div>
            </div>

            {{-- Details grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-gray-100">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Bidang Keahlian</p>
                    <p class="text-sm font-medium text-gray-900">{{ $application->expertise }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Tanggal Pengajuan</p>
                    <p class="text-sm font-medium text-gray-900">{{ $application->created_at->translatedFormat('d F Y, H:i') }}</p>
                </div>
                @if($application->portfolio_url)
                <div>
                    <p class="text-xs text-gray-500 mb-1">Portofolio</p>
                    <a href="{{ $application->portfolio_url }}" target="_blank" rel="noopener"
                       class="text-sm text-primary-600 hover:underline break-all">
                        {{ $application->portfolio_url }}
                    </a>
                </div>
                @endif
                @if($application->phone)
                <div>
                    <p class="text-xs text-gray-500 mb-1">No. Telepon</p>
                    <p class="text-sm font-medium text-gray-900">{{ $application->phone }}</p>
                </div>
                @endif
            </div>

            {{-- Motivation --}}
            <div class="pt-4 border-t border-gray-100">
                <p class="text-xs text-gray-500 mb-2">Motivasi & Pengalaman</p>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $application->motivation }}</p>
                </div>
            </div>

            {{-- Review info (jika sudah diproses) --}}
            @if($application->reviewed_at)
                <div class="pt-4 border-t border-gray-100">
                    <p class="text-xs text-gray-500 mb-2">Review oleh</p>
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-gray-900">{{ $application->reviewer->name ?? 'Admin' }}</span>
                        <span class="text-xs text-gray-400">{{ $application->reviewed_at->translatedFormat('d F Y, H:i') }}</span>
                    </div>
                    @if($application->admin_notes)
                        <p class="mt-2 text-sm text-gray-600 italic">"{{ $application->admin_notes }}"</p>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- Action buttons (hanya untuk pending) --}}
    @if($application->status === 'pending')
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

            {{-- APPROVE --}}
            <div class="bg-white rounded-xl border border-green-200 p-5" x-data="{ showNotes: false }">
                <h3 class="text-sm font-semibold text-green-800 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Setujui Pengajuan
                </h3>
                <p class="text-xs text-gray-500 mb-3">User akan otomatis menjadi Instruktur.</p>

                <form action="{{ route('admin.instructor-applications.approve', $application) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <button type="button" @click="showNotes = !showNotes" class="text-xs text-green-600 hover:underline">
                            + Tambah catatan (opsional)
                        </button>
                        <textarea x-show="showNotes" x-cloak name="admin_notes" rows="2"
                            placeholder="Catatan untuk pemohon..."
                            class="mt-2 w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-green-500 focus:border-green-500"></textarea>
                    </div>
                    <button type="submit"
                        class="w-full py-2.5 px-4 rounded-lg text-sm font-semibold text-white bg-green-600 hover:bg-green-700 transition-colors"
                        onclick="return confirm('Yakin ingin menyetujui pengajuan ini? User akan menjadi Instruktur.')">
                        Setujui
                    </button>
                </form>
            </div>

            {{-- REJECT --}}
            <div class="bg-white rounded-xl border border-red-200 p-5">
                <h3 class="text-sm font-semibold text-red-800 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Tolak Pengajuan
                </h3>
                <p class="text-xs text-gray-500 mb-3">Berikan alasan agar pemohon bisa memperbaiki.</p>

                <form action="{{ route('admin.instructor-applications.reject', $application) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <textarea name="admin_notes" rows="2" required
                            placeholder="Alasan penolakan (wajib diisi)..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-red-500 focus:border-red-500
                                @error('admin_notes') border-red-400 @enderror"></textarea>
                        @error('admin_notes')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit"
                        class="w-full py-2.5 px-4 rounded-lg text-sm font-semibold text-white bg-red-600 hover:bg-red-700 transition-colors"
                        onclick="return confirm('Yakin ingin menolak pengajuan ini?')">
                        Tolak
                    </button>
                </form>
            </div>

        </div>
    @endif

</div>
@endsection
