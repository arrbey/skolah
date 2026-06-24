@extends('layouts.admin')

@php
    $isEdit = isset($template->id);
    $action = $isEdit ? route('admin.certificate-templates.update', $template) : route('admin.certificate-templates.store');
    $v = fn($field, $default='') => old($field, $template->{$field} ?? $default);
    $bgUrl = ($isEdit && $template->background_image) ? $template->background_url : null;
@endphp

@section('title', $isEdit ? 'Edit Template Sertifikat' : 'Template Sertifikat Baru')

@section('page-header')
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.certificate-templates.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <span class="text-base font-semibold text-gray-900">
            {{ $isEdit ? 'Edit Template: ' . $template->name : 'Template Sertifikat Baru' }}
        </span>
    </div>
@endsection

@push('styles')
<style>
    .field-row { display:grid; grid-template-columns:1fr 1fr; gap:0.5rem; }
    .field-row3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:0.5rem; }
    .lbl { display:block; font-size:0.7rem; font-weight:600; color:#6b7280; margin-bottom:0.25rem; }
    input[type=number].coord { width:100%; padding:0.375rem 0.5rem; border:1px solid #d1d5db; border-radius:0.5rem; font-size:0.8rem; }
    input[type=number].coord:focus { outline:none; border-color:#2563EB; box-shadow:0 0 0 2px rgba(37,99,235,0.15); }
    .sect-title { font-size:0.8rem; font-weight:700; color:#374151; padding-bottom:0.5rem; border-bottom:1px solid #f3f4f6; margin-bottom:0.75rem; display:flex; align-items:center; gap:0.5rem; }
    .active-field-badge { display:inline-block; padding:1px 8px; border-radius:99px; font-size:0.65rem; font-weight:700; background:#dbeafe; color:#1d4ed8; cursor:pointer; }

    /* ── Live Preview ─────────────────────────────────────────────── */
    .live-preview {
        position: relative;
        width: 100%;
        aspect-ratio: 297 / 210;
        background: #e5e7eb;
        border-radius: 0.75rem;
        overflow: hidden;
        cursor: crosshair;
    }
    .live-preview img.bg-img {
        position: absolute; inset: 0;
        width: 100%; height: 100%;
        object-fit: cover;
    }
    .live-preview .no-bg {
        position: absolute; inset: 0;
        display: flex; align-items: center; justify-content: center;
        background: #fff; border: 2px dashed #d1d5db;
        border-radius: 0.75rem;
    }
    .live-preview .overlay-text {
        position: absolute;
        white-space: nowrap;
        pointer-events: none;
        line-height: 1.2;
    }
    .crosshair-hint {
        position: absolute; inset: 0;
        display: flex; align-items: flex-end; justify-content: center;
        padding-bottom: 6px; pointer-events: none; z-index: 10;
    }
    .crosshair-hint span {
        background: rgba(0,0,0,0.6); color: #fff;
        font-size: 0.6rem; padding: 2px 8px; border-radius: 99px;
    }
</style>
@endpush

@section('content')

<form id="cert-form" action="{{ $action }}" method="POST" enctype="multipart/form-data"
      x-data="certDesigner()" x-ref="form">
    @csrf
    @if($isEdit) @method('PUT') @endif

<div class="grid xl:grid-cols-[1fr_1.1fr] gap-6 items-start">

{{-- ═══════════════════════════════════════════════════════════════════════
     KOLOM KIRI: SEMUA FORM SETTING (scrollable)
     ═══════════════════════════════════════════════════════════════════════ --}}
<div class="space-y-4">

    {{-- 1. Info Template --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
        <div class="sect-title">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            Informasi Template
        </div>
        <div class="space-y-3">
            <div>
                <label class="lbl">Nama Template <span class="text-red-500">*</span></label>
                <input type="text" name="name" required value="{{ $v('name') }}"
                       placeholder="Contoh: Klasik Navy, Modern Minimal…"
                       class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 @error('name') border-red-400 @enderror">
                @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
            <label class="flex items-center gap-2.5 cursor-pointer text-sm text-gray-700">
                <input type="checkbox" name="set_active" value="1"
                       {{ old('set_active', $v('is_active', false)) ? 'checked' : '' }}
                       class="rounded border-gray-300 text-primary-600">
                Jadikan template aktif setelah disimpan
            </label>
        </div>
    </div>

    {{-- 2. Upload Background --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
        <div class="sect-title">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Background Sertifikat
        </div>

        @if($bgUrl)
            <div class="mb-3">
                <p class="lbl mb-1">Background saat ini:</p>
                <img src="{{ $bgUrl }}" class="w-full rounded-xl object-cover border border-gray-200" style="aspect-ratio:297/210">
                <p class="text-xs text-gray-400 mt-1">Upload gambar baru di bawah untuk mengganti.</p>
            </div>
        @endif

        <div class="border-2 border-dashed border-gray-300 rounded-xl p-5 text-center hover:border-primary-400 hover:bg-primary-50 transition-colors cursor-pointer"
             onclick="document.getElementById('bg-file-input').click()">
            <input id="bg-file-input" type="file" name="background_image"
                   accept=".jpg,.jpeg,.png" class="hidden"
                   @change="handleFileSelect($event)">
            <div x-show="!localPreview">
                <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                <p class="text-sm text-gray-500">Klik atau drag & drop gambar di sini</p>
                <p class="text-xs text-gray-400 mt-1">PNG / JPG — Maks 10MB — <strong>Landscape A4</strong> (3508 × 2480px)</p>
            </div>
            <div x-show="localPreview" x-cloak>
                <img :src="localPreview" class="w-full rounded-lg object-cover mx-auto mb-2" style="aspect-ratio:297/210; max-height:120px">
                <p class="text-xs text-gray-400">Klik untuk ganti gambar</p>
            </div>
        </div>

        @error('background_image')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror

        <div class="mt-3 p-3 bg-amber-50 rounded-xl border border-amber-200">
            <p class="text-xs text-amber-700 font-semibold mb-1">💡 Tips export dari Canva:</p>
            <ul class="text-xs text-amber-600 space-y-0.5 list-disc list-inside">
                <li>Ukuran <strong>A4 Landscape</strong> (297 × 210 mm)</li>
                <li>Biarkan area nama & kursus <strong>kosong</strong></li>
                <li>Export → <strong>PNG</strong> (kualitas terbaik)</li>
            </ul>
        </div>
    </div>

    {{-- 3. Nama Penerima --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
        <div class="sect-title">
            <div class="w-2.5 h-2.5 rounded-full bg-blue-500"></div>
            Nama Penerima
            <span class="active-field-badge ml-auto" @click="setActiveField('name')">📍 Klik posisi</span>
        </div>
        <div class="space-y-3">
            <div class="field-row">
                <div>
                    <label class="lbl">Posisi X (%)</label>
                    <input type="number" name="name_x" id="f-name_x" min="0" max="100" step="0.5"
                           x-model.number="cfg.name_x" class="coord">
                </div>
                <div>
                    <label class="lbl">Posisi Y (%)</label>
                    <input type="number" name="name_y" id="f-name_y" min="0" max="100" step="0.5"
                           x-model.number="cfg.name_y" class="coord">
                </div>
            </div>
            <div class="field-row3">
                <div>
                    <label class="lbl">Font (pt)</label>
                    <input type="number" name="name_font_size" min="8" max="120" step="1"
                           x-model.number="cfg.name_font_size" class="coord">
                </div>
                <div>
                    <label class="lbl">Align</label>
                    <select name="name_align" x-model="cfg.name_align"
                            class="w-full rounded-xl border border-gray-300 px-2 py-1.5 text-sm focus:ring-2 focus:ring-primary-500">
                        <option value="center">Center</option>
                        <option value="left">Left</option>
                        <option value="right">Right</option>
                    </select>
                </div>
                <div>
                    <label class="lbl">Warna</label>
                    <div class="flex gap-1">
                        <input type="color" name="name_font_color" x-model="cfg.name_font_color"
                               class="w-9 h-9 rounded-lg border border-gray-300 cursor-pointer p-0.5">
                        <input type="text" x-model="cfg.name_font_color" class="coord flex-1">
                    </div>
                </div>
            </div>
            <label class="flex items-center gap-2 text-xs text-gray-600 cursor-pointer">
                <input type="checkbox" name="name_bold" value="1" x-model="cfg.name_bold"
                       class="rounded border-gray-300 text-primary-600"> Tebal (Bold)
            </label>
        </div>
    </div>

    {{-- 4. Nama Kursus --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
        <div class="sect-title">
            <div class="w-2.5 h-2.5 rounded-full bg-purple-500"></div>
            Nama Kursus / Pelatihan
            <span class="active-field-badge ml-auto" @click="setActiveField('course')">📍 Klik posisi</span>
        </div>
        <div class="space-y-3">
            <div class="field-row">
                <div>
                    <label class="lbl">Posisi X (%)</label>
                    <input type="number" name="course_x" id="f-course_x" min="0" max="100" step="0.5"
                           x-model.number="cfg.course_x" class="coord">
                </div>
                <div>
                    <label class="lbl">Posisi Y (%)</label>
                    <input type="number" name="course_y" id="f-course_y" min="0" max="100" step="0.5"
                           x-model.number="cfg.course_y" class="coord">
                </div>
            </div>
            <div class="field-row3">
                <div>
                    <label class="lbl">Font (pt)</label>
                    <input type="number" name="course_font_size" min="6" max="80" step="1"
                           x-model.number="cfg.course_font_size" class="coord">
                </div>
                <div>
                    <label class="lbl">Align</label>
                    <select name="course_align" x-model="cfg.course_align"
                            class="w-full rounded-xl border border-gray-300 px-2 py-1.5 text-sm focus:ring-2 focus:ring-primary-500">
                        <option value="center">Center</option>
                        <option value="left">Left</option>
                        <option value="right">Right</option>
                    </select>
                </div>
                <div>
                    <label class="lbl">Warna</label>
                    <div class="flex gap-1">
                        <input type="color" name="course_font_color" x-model="cfg.course_font_color"
                               class="w-9 h-9 rounded-lg border border-gray-300 cursor-pointer p-0.5">
                        <input type="text" x-model="cfg.course_font_color" class="coord flex-1">
                    </div>
                </div>
            </div>
            <label class="flex items-center gap-2 text-xs text-gray-600 cursor-pointer">
                <input type="checkbox" name="course_bold" value="1" x-model="cfg.course_bold"
                       class="rounded border-gray-300 text-primary-600"> Tebal (Bold)
            </label>
        </div>
    </div>

    {{-- 5. Nomor Sertifikat --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="sect-title mb-0 !border-0 !pb-0">
                <div class="w-2.5 h-2.5 rounded-full bg-green-500"></div>
                Nomor Sertifikat
                <span class="active-field-badge ml-2" @click="setActiveField('cert_num')">📍 Klik posisi</span>
            </div>
            <label class="flex items-center gap-2 text-xs text-gray-600 cursor-pointer">
                <input type="checkbox" name="show_cert_number" value="1" x-model="cfg.show_cert_number"
                       class="rounded border-gray-300 text-primary-600"> Tampilkan
            </label>
        </div>
        <div class="space-y-3">
            <div class="field-row">
                <div>
                    <label class="lbl">Posisi X (%)</label>
                    <input type="number" name="cert_num_x" id="f-cert_num_x" min="0" max="100" step="0.5"
                           x-model.number="cfg.cert_num_x" class="coord">
                </div>
                <div>
                    <label class="lbl">Posisi Y (%)</label>
                    <input type="number" name="cert_num_y" id="f-cert_num_y" min="0" max="100" step="0.5"
                           x-model.number="cfg.cert_num_y" class="coord">
                </div>
            </div>
            <div class="field-row">
                <div>
                    <label class="lbl">Font (pt)</label>
                    <input type="number" name="cert_num_font_size" min="6" max="60" step="1"
                           x-model.number="cfg.cert_num_font_size" class="coord">
                </div>
                <div>
                    <label class="lbl">Warna</label>
                    <div class="flex gap-1">
                        <input type="color" name="cert_num_font_color" x-model="cfg.cert_num_font_color"
                               class="w-9 h-9 rounded-lg border border-gray-300 cursor-pointer p-0.5">
                        <input type="text" x-model="cfg.cert_num_font_color" class="coord flex-1">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 6. Tanggal --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="sect-title mb-0 !border-0 !pb-0">
                <div class="w-2.5 h-2.5 rounded-full bg-orange-500"></div>
                Tanggal Kelulusan
                <span class="active-field-badge ml-2" @click="setActiveField('date')">📍 Klik posisi</span>
            </div>
            <label class="flex items-center gap-2 text-xs text-gray-600 cursor-pointer">
                <input type="checkbox" name="show_date" value="1" x-model="cfg.show_date"
                       class="rounded border-gray-300 text-primary-600"> Tampilkan
            </label>
        </div>
        <div class="space-y-3">
            <div class="field-row">
                <div>
                    <label class="lbl">Posisi X (%)</label>
                    <input type="number" name="date_x" id="f-date_x" min="0" max="100" step="0.5"
                           x-model.number="cfg.date_x" class="coord">
                </div>
                <div>
                    <label class="lbl">Posisi Y (%)</label>
                    <input type="number" name="date_y" id="f-date_y" min="0" max="100" step="0.5"
                           x-model.number="cfg.date_y" class="coord">
                </div>
            </div>
            <div class="field-row">
                <div>
                    <label class="lbl">Font (pt)</label>
                    <input type="number" name="date_font_size" min="6" max="60" step="1"
                           x-model.number="cfg.date_font_size" class="coord">
                </div>
                <div>
                    <label class="lbl">Warna</label>
                    <div class="flex gap-1">
                        <input type="color" name="date_font_color" x-model="cfg.date_font_color"
                               class="w-9 h-9 rounded-lg border border-gray-300 cursor-pointer p-0.5">
                        <input type="text" x-model="cfg.date_font_color" class="coord flex-1">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tombol simpan --}}
    <div class="flex gap-3">
        <button type="submit" class="flex-1 py-2.5 rounded-xl bg-primary-600 text-white font-semibold text-sm hover:bg-primary-700 transition-colors">
            {{ $isEdit ? 'Simpan Perubahan' : 'Buat Template' }}
        </button>
        <a href="{{ route('admin.certificate-templates.index') }}"
           class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-600 text-sm font-semibold hover:bg-gray-50 transition-colors">
            Batal
        </a>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════
     KOLOM KANAN: LIVE PREVIEW (sticky)
     ═══════════════════════════════════════════════════════════════════════ --}}
<div class="xl:sticky xl:top-20 space-y-3">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4">
        <div class="flex items-center justify-between mb-3">
            <div>
                <h3 class="font-semibold text-gray-900 text-sm">Live Preview</h3>
                <p class="text-xs text-gray-400" x-text="activeField ? '🎯 Klik di preview untuk posisi: ' + activeFieldLabel : 'Ubah form di kiri → preview update otomatis'"></p>
            </div>
            <div x-show="activeField" class="flex items-center gap-1.5">
                <div class="w-2 h-2 rounded-full animate-pulse"
                     :class="fieldColors[activeField]"></div>
                <span class="text-xs font-bold" x-text="activeFieldLabel"></span>
                <button type="button" @click="activeField=null" class="text-gray-400 hover:text-gray-600 ml-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        {{-- ── Live Preview Area ──────────────────────────────────── --}}
        <div class="live-preview" x-ref="previewArea" @click="handlePreviewClick($event)">

            {{-- Background image --}}
            <template x-if="previewBg">
                <img :src="previewBg" class="bg-img" alt="background">
            </template>
            <template x-if="!previewBg">
                <div class="no-bg">
                    <span class="text-xs text-gray-400">Upload background di form kiri</span>
                </div>
            </template>

            {{-- Overlay: Nama Penerima --}}
            <div class="overlay-text"
                 :style="nameStyle">
                Nama Penerima Sertifikat
            </div>

            {{-- Overlay: Nama Kursus --}}
            <div class="overlay-text"
                 :style="courseStyle">
                Nama Kursus / Pelatihan
            </div>

            {{-- Overlay: Nomor Sertifikat --}}
            <div class="overlay-text" x-show="cfg.show_cert_number"
                 :style="certNumStyle">
                SKOL-2025-000001
            </div>

            {{-- Overlay: Tanggal --}}
            <div class="overlay-text" x-show="cfg.show_date"
                 :style="dateStyle">
                {{ now()->locale('id')->translatedFormat('d F Y') }}
            </div>

            {{-- Crosshair hint --}}
            <div class="crosshair-hint" x-show="activeField">
                <span>🎯 Klik untuk posisi <span x-text="activeFieldLabel"></span></span>
            </div>
        </div>

        {{-- Info & Legenda --}}
        <div class="mt-3 flex items-center justify-between">
            <div class="flex gap-3 text-xs text-gray-400 flex-wrap">
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-blue-500 inline-block"></span>Nama</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-purple-500 inline-block"></span>Kursus</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>No. Sertifikat</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-orange-500 inline-block"></span>Tanggal</span>
            </div>
            <span class="text-xs text-gray-300">297×210mm</span>
        </div>
    </div>
</div>

</div><!-- /grid -->
</form>

@endsection

@push('head')
<script nonce="{{ $cspNonce ?? '' }}">
/**
 * Defined in push('head') so it loads BEFORE Alpine.js (defer).
 * Alpine sees x-data="certDesigner()" — calls this function.
 * Using Alpine getter properties (get nameStyle) for true reactivity.
 */
function certDesigner() {
    const CERT_PX_W = 1123; // 297mm @96dpi

    return {
        activeField: null,
        localPreview: null,
        previewBg: {!! json_encode($bgUrl) !!},
        _previewW: 500,

        cfg: {
            name_x: {{ $v('name_x', 50) }},
            name_y: {{ $v('name_y', 52) }},
            name_font_size: {{ $v('name_font_size', 36) }},
            name_font_color: '{{ $v('name_font_color', '#1E3A5F') }}',
            name_align: '{{ $v('name_align', 'center') }}',
            name_bold: {{ $v('name_bold', true) ? 'true' : 'false' }},

            course_x: {{ $v('course_x', 50) }},
            course_y: {{ $v('course_y', 64) }},
            course_font_size: {{ $v('course_font_size', 18) }},
            course_font_color: '{{ $v('course_font_color', '#2563EB') }}',
            course_align: '{{ $v('course_align', 'center') }}',
            course_bold: {{ $v('course_bold', false) ? 'true' : 'false' }},

            show_cert_number: {{ $v('show_cert_number', true) ? 'true' : 'false' }},
            cert_num_x: {{ $v('cert_num_x', 50) }},
            cert_num_y: {{ $v('cert_num_y', 78) }},
            cert_num_font_size: {{ $v('cert_num_font_size', 14) }},
            cert_num_font_color: '{{ $v('cert_num_font_color', '#64748B') }}',

            show_date: {{ $v('show_date', true) ? 'true' : 'false' }},
            date_x: {{ $v('date_x', 50) }},
            date_y: {{ $v('date_y', 84) }},
            date_font_size: {{ $v('date_font_size', 14) }},
            date_font_color: '{{ $v('date_font_color', '#64748B') }}',
        },

        fieldColors: {
            name: 'bg-blue-500',
            course: 'bg-purple-500',
            cert_num: 'bg-green-500',
            date: 'bg-orange-500',
        },

        init() {
            this.$nextTick(() => { this._measurePreview(); });
            window.addEventListener('resize', () => this._measurePreview());
        },

        _measurePreview() {
            const area = this.$refs.previewArea;
            if (area) this._previewW = area.clientWidth;
        },

        get activeFieldLabel() {
            return { name:'Nama Penerima', course:'Nama Kursus', cert_num:'No. Sertifikat', date:'Tanggal' }[this.activeField] || '';
        },

        /* ── Computed style getters — Alpine re-evaluates when cfg.* changes ── */
        get nameStyle() {
            return this._buildStyle(this.cfg.name_x, this.cfg.name_y, this.cfg.name_font_size, this.cfg.name_font_color, this.cfg.name_align, this.cfg.name_bold);
        },
        get courseStyle() {
            return this._buildStyle(this.cfg.course_x, this.cfg.course_y, this.cfg.course_font_size, this.cfg.course_font_color, this.cfg.course_align, this.cfg.course_bold);
        },
        get certNumStyle() {
            return this._buildStyle(this.cfg.cert_num_x, this.cfg.cert_num_y, this.cfg.cert_num_font_size, this.cfg.cert_num_font_color, 'center', false);
        },
        get dateStyle() {
            return this._buildStyle(this.cfg.date_x, this.cfg.date_y, this.cfg.date_font_size, this.cfg.date_font_color, 'center', false);
        },

        _buildStyle(x, y, fontSize, color, align, bold) {
            var scale = this._previewW / CERT_PX_W;
            var scaledFont = Math.max(4, fontSize * scale);
            var css = 'top:' + y + '%;color:' + color + ';font-size:' + scaledFont.toFixed(1) + 'px;font-weight:' + (bold ? 'bold' : 'normal') + ';font-family:DejaVu Sans,Arial,sans-serif;';

            if (align === 'center') {
                css += 'left:0;width:100%;text-align:center;';
            } else if (align === 'right') {
                css += 'right:' + (100 - x) + '%;text-align:right;';
            } else {
                css += 'left:' + x + '%;text-align:left;';
            }
            return css;
        },

        setActiveField(field) {
            this.activeField = this.activeField === field ? null : field;
        },

        handlePreviewClick(event) {
            if (!this.activeField) return;
            var area = this.$refs.previewArea;
            var rect = area.getBoundingClientRect();
            var x = +((event.clientX - rect.left) / rect.width * 100).toFixed(1);
            var y = +((event.clientY - rect.top) / rect.height * 100).toFixed(1);

            var f = this.activeField;
            if (f === 'name')     { this.cfg.name_x = x; this.cfg.name_y = y; }
            if (f === 'course')   { this.cfg.course_x = x; this.cfg.course_y = y; }
            if (f === 'cert_num') { this.cfg.cert_num_x = x; this.cfg.cert_num_y = y; }
            if (f === 'date')     { this.cfg.date_x = x; this.cfg.date_y = y; }
        },

        handleFileSelect(event) {
            var file = event.target.files[0];
            if (!file) return;
            this.localPreview = URL.createObjectURL(file);
            this.previewBg = this.localPreview;
        },
    };
}
</script>
@endpush
