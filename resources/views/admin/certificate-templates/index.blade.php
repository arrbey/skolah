@extends('layouts.admin')

@section('title', 'Desain Sertifikat')

@section('page-header')
    <div class="flex items-center justify-between">
        <div>
            <span class="text-base font-semibold text-gray-900">Desain Sertifikat</span>
            <p class="text-xs text-gray-400 mt-0.5">Upload background sertifikat dari Canva atau desainer, lalu atur posisi teks</p>
        </div>
        <a href="{{ route('admin.certificate-templates.create') }}"
           class="flex items-center gap-2 px-4 py-2 rounded-xl bg-primary-600 text-white text-sm font-medium hover:bg-primary-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Template Baru
        </a>
    </div>
@endsection

@section('content')

@if(session('success'))
    <div class="mb-5 flex items-center gap-3 p-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl text-sm">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="mb-5 flex items-center gap-3 p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-sm">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        {{ session('error') }}
    </div>
@endif

{{-- Info cara kerja --}}
<div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-2xl">
    <div class="flex gap-3 items-start">
        <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div class="text-sm text-blue-700 space-y-1">
            <p><strong>Cara pakai:</strong></p>
            <ol class="list-decimal list-inside space-y-0.5 text-blue-600">
                <li>Buat desain sertifikat di <strong>Canva</strong> atau tool lain — biarkan area nama & kursus kosong</li>
                <li>Export sebagai <strong>PNG atau JPG landscape</strong> (ukuran A4: 3508 x 2480px atau minimal 1748 x 1240px)</li>
                <li>Upload gambar tersebut di sini, lalu atur posisi teks (nama, kursus, tanggal)</li>
                <li>Klik <strong>"Gunakan Template"</strong> — sistem akan otomatis overlay nama & kursus saat generate sertifikat</li>
            </ol>
        </div>
    </div>
</div>

@if($templates->isEmpty())
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-16 text-center">
        <div class="w-20 h-20 bg-primary-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-10 h-10 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        <h3 class="text-gray-700 font-semibold mb-1">Belum Ada Template</h3>
        <p class="text-gray-400 text-sm mb-5">Upload background sertifikat pertama Anda dari Canva atau desainer.</p>
        <a href="{{ route('admin.certificate-templates.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary-600 text-white text-sm font-semibold hover:bg-primary-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            Upload Template Pertama
        </a>
    </div>
@else
    <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-5">
        @foreach($templates as $tpl)
            <div class="bg-white rounded-2xl border {{ $tpl->is_active ? 'border-primary-400 ring-2 ring-primary-100' : 'border-gray-200' }} shadow-sm overflow-hidden flex flex-col">

                {{-- Thumbnail Background --}}
                <div class="relative bg-gray-100 overflow-hidden" style="aspect-ratio: 297/210">
                    @if($tpl->background_image)
                        <img src="{{ $tpl->background_url }}"
                             alt="{{ $tpl->name }}"
                             class="w-full h-full object-cover">
                        {{-- Overlay teks dummy untuk preview --}}
                        <div class="absolute inset-0 pointer-events-none flex flex-col items-center justify-center gap-1" style="padding:8%">
                            <div class="text-center font-bold text-shadow" style="color:{{ $tpl->name_font_color ?? '#1E3A5F' }}; font-size:clamp(8px,2.5vw,14px)">
                                Nama Penerima Sertifikat
                            </div>
                            <div class="text-center font-semibold" style="color:{{ $tpl->course_font_color ?? '#2563EB' }}; font-size:clamp(6px,1.8vw,10px)">
                                Nama Kursus / Pelatihan
                            </div>
                        </div>
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center text-gray-400 gap-2">
                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span class="text-xs text-gray-400">Belum ada background</span>
                        </div>
                    @endif

                    {{-- Badge Aktif --}}
                    @if($tpl->is_active)
                        <div class="absolute top-2 left-2 flex items-center gap-1 px-2 py-1 rounded-full bg-green-500 text-white text-xs font-bold shadow">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            AKTIF
                        </div>
                    @endif

                    {{-- Badge Belum ada background --}}
                    @if(!$tpl->background_image)
                        <div class="absolute top-2 right-2 px-2 py-1 rounded-full bg-orange-100 text-orange-600 text-xs font-medium">
                            Belum ada gambar
                        </div>
                    @endif
                </div>

                {{-- Info & Aksi --}}
                <div class="p-4 flex flex-col gap-3 flex-1">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-semibold text-gray-900 text-sm">{{ $tpl->name }}</h3>
                            @if($tpl->is_active)
                                <span class="px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-xs font-medium">Aktif</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-400 mt-1">
                            Nama: {{ $tpl->name_font_size }}pt &bull;
                            Kursus: {{ $tpl->course_font_size }}pt
                            @if($tpl->show_date) &bull; Tampilkan tanggal @endif
                            @if($tpl->show_cert_number) &bull; No. sertifikat @endif
                        </p>
                    </div>

                    <div class="flex gap-2 mt-auto pt-2 border-t border-gray-100">
                        @if(!$tpl->is_active)
                            <form action="{{ route('admin.certificate-templates.set-active', $tpl) }}" method="POST" class="flex-1">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="w-full py-1.5 rounded-xl text-xs font-semibold transition-colors
                                               {{ $tpl->background_image ? 'bg-primary-600 text-white hover:bg-primary-700' : 'bg-gray-100 text-gray-400 cursor-not-allowed' }}"
                                        {{ !$tpl->background_image ? 'disabled title=Upload background dulu' : '' }}>
                                    Gunakan Template
                                </button>
                            </form>
                        @else
                            <span class="flex-1 py-1.5 text-center rounded-xl text-xs font-semibold bg-green-50 text-green-600 border border-green-200">
                                &#10003; Sedang Digunakan
                            </span>
                        @endif

                        <a href="{{ route('admin.certificate-templates.edit', $tpl) }}"
                           class="p-1.5 rounded-xl text-gray-400 hover:text-primary-600 hover:bg-primary-50 transition-colors"
                           title="Edit template">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>

                        @if(!$tpl->is_active)
                            <form action="{{ route('admin.certificate-templates.destroy', $tpl) }}" method="POST"
                                  onsubmit="return confirm('Hapus template \'{{ addslashes($tpl->name) }}\'?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-xl text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

@endsection
