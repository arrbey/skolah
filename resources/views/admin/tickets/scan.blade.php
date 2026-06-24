@extends('layouts.admin')

@section('title', 'Scan & Absensi Peserta')

@section('page-header')
    <div class="flex items-center gap-3">
        <span class="text-base font-semibold text-gray-900">Scan & Absensi Peserta</span>
        @if($selectedBootcamp)
            <span class="text-gray-300">|</span>
            <span class="text-sm text-gray-500">{{ $selectedBootcamp->title }}</span>
        @endif
    </div>
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    {{-- Bootcamp Selector --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
        <form method="GET" action="{{ route('admin.tickets.scan') }}" class="flex flex-col sm:flex-row items-start sm:items-end gap-4">
            <div class="flex-1 w-full">
                <label for="bootcamp_id" class="text-sm font-semibold text-gray-700 mb-2 block">Pilih Program Bootcamp</label>
                <select name="bootcamp_id" id="bootcamp_id" onchange="this.form.submit()"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                    <option value="">-- Pilih Bootcamp --</option>
                    @foreach($bootcamps as $bc)
                        <option value="{{ $bc->id }}" {{ $selectedBootcamp && $selectedBootcamp->id == $bc->id ? 'selected' : '' }}>
                            {{ $bc->title }} — {{ $bc->start_date->translatedFormat('d M Y') }}
                            ({{ $bc->status === 'ongoing' ? 'Berlangsung' : 'Akan Datang' }})
                        </option>
                    @endforeach
                </select>
            </div>
            @if($selectedBootcamp)
                <a href="{{ route('admin.tickets.show-bootcamp', $selectedBootcamp) }}"
                   class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition-colors whitespace-nowrap">
                    Lihat Detail Absensi →
                </a>
            @endif
        </form>
    </div>

    @if($selectedBootcamp)

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <p class="text-2xl font-bold text-blue-600" id="statTotal">{{ $totalCount }}</p>
            <p class="text-xs text-gray-500 mt-1">Total Peserta</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <p class="text-2xl font-bold text-green-600" id="statHadir">{{ $checkedInCount }}</p>
            <p class="text-xs text-gray-500 mt-1">Sudah Hadir</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <p class="text-2xl font-bold text-red-500" id="statBelum">{{ $totalCount - $checkedInCount }}</p>
            <p class="text-xs text-gray-500 mt-1">Belum Hadir</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <p class="text-2xl font-bold text-purple-600" id="statPersen">{{ $totalCount > 0 ? round(($checkedInCount / $totalCount) * 100) : 0 }}%</p>
            <p class="text-xs text-gray-500 mt-1">Persentase</p>
        </div>
    </div>

    {{-- Progress Bar --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <div class="flex items-center justify-between mb-2">
            <p class="text-sm font-medium text-gray-700">Progress Kehadiran</p>
            <p class="text-sm font-bold text-blue-600" id="progressLabel">{{ $checkedInCount }}/{{ $totalCount }}</p>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
            <div id="progressBar" class="bg-gradient-to-r from-green-500 to-emerald-500 h-full transition-all duration-500 rounded-full"
                 style="width: {{ $totalCount > 0 ? ($checkedInCount / $totalCount) * 100 : 0 }}%"></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

        {{-- LEFT: Scanner (2 cols) --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Permission Warning --}}
            <div id="permissionWarning" class="hidden bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-yellow-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div class="flex-1">
                        <p class="font-semibold text-sm text-yellow-900">Izinkan Akses Kamera</p>
                        <p class="text-sm text-yellow-700 mt-1">Klik <strong>"Izinkan"</strong> saat dialog muncul.</p>
                        <button onclick="retryCamera()" class="mt-2 text-xs font-semibold text-yellow-900 hover:text-yellow-800 underline">Coba Lagi &rarr;</button>
                    </div>
                </div>
            </div>

            {{-- Status Alert --}}
            <div id="statusAlert" class="hidden p-4 rounded-xl border">
                <div class="flex gap-3">
                    <div id="statusIcon" class="shrink-0 w-5 h-5 mt-0.5"></div>
                    <div class="flex-1">
                        <p id="statusTitle" class="font-semibold text-sm"></p>
                        <p id="statusMessage" class="text-sm mt-1"></p>
                        <div id="statusDetails" class="mt-2 text-xs space-y-0.5"></div>
                    </div>
                    <button onclick="document.getElementById('statusAlert').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Scanner Card --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                <div class="p-4 border-b border-gray-100">
                    <h2 class="text-base font-bold text-gray-900">Pemindai QR Code</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Arahkan kamera ke QR code tiket peserta</p>
                </div>

                <div class="p-4">
                    {{-- Camera Preview --}}
                    <div id="cameraContainer" class="relative bg-gray-900 rounded-xl overflow-hidden mb-4 aspect-square">
                        <video id="video" playsinline autoplay muted class="w-full h-full object-cover"></video>

                        {{-- Scan overlay --}}
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                            <div class="absolute inset-0" style="background: radial-gradient(circle at center, transparent 20%, rgba(0,0,0,0.55) 70%);"></div>
                            <div class="relative w-44 h-44 sm:w-52 sm:h-52">
                                <div class="absolute inset-0 border-2 border-blue-400/60 rounded-xl" style="animation: framePulse 2s ease-in-out infinite;"></div>
                                <div class="absolute -top-px -left-px w-6 h-6 border-t-[3px] border-l-[3px] border-blue-400 rounded-tl-lg"></div>
                                <div class="absolute -top-px -right-px w-6 h-6 border-t-[3px] border-r-[3px] border-blue-400 rounded-tr-lg"></div>
                                <div class="absolute -bottom-px -left-px w-6 h-6 border-b-[3px] border-l-[3px] border-blue-400 rounded-bl-lg"></div>
                                <div class="absolute -bottom-px -right-px w-6 h-6 border-b-[3px] border-r-[3px] border-blue-400 rounded-br-lg"></div>
                                <div class="absolute left-2 right-2 h-0.5 bg-blue-400 rounded-full shadow-lg shadow-blue-400/50" style="animation: scanLine 2.5s ease-in-out infinite;"></div>
                            </div>
                        </div>

                        {{-- Camera placeholder --}}
                        <div id="cameraPlaceholder" class="absolute inset-0 flex flex-col items-center justify-center bg-gray-800 text-gray-400">
                            <svg class="w-14 h-14 mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-sm font-medium">Menginisialisasi kamera...</p>
                        </div>

                        {{-- Loading overlay --}}
                        <div id="loadingOverlay" class="absolute inset-0 bg-black/60 flex items-center justify-center" style="display: none;">
                            <div class="text-center">
                                <div class="w-10 h-10 rounded-full border-4 border-white/30 border-t-blue-400 mx-auto mb-3" style="animation: spin 1s linear infinite;"></div>
                                <p class="text-white text-sm font-medium">Memproses...</p>
                            </div>
                        </div>
                    </div>

                    {{-- Manual Input --}}
                    <div class="space-y-3">
                        <div>
                            <label for="manualTicketCode" class="text-xs font-semibold text-gray-700 mb-1.5 block">Input Kode Tiket Manual</label>
                            <div class="flex gap-2">
                                <input type="text" id="manualTicketCode" placeholder="TKT-A1B2C3D4"
                                       class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono">
                                <button onclick="processManualInput()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">
                                    Cek
                                </button>
                            </div>
                        </div>

                        {{-- Divider --}}
                        <div class="relative">
                            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
                            <div class="relative flex justify-center text-xs"><span class="px-3 bg-white text-gray-400">atau</span></div>
                        </div>

                        {{-- Upload QR Image --}}
                        <label class="flex items-center justify-center gap-2 px-3 py-2.5 border-2 border-dashed border-gray-300 rounded-lg hover:border-blue-400 hover:bg-blue-50/50 cursor-pointer transition-all">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-sm font-medium text-gray-500">Upload foto QR</span>
                            <input type="file" id="qrImageInput" accept="image/*" class="hidden" onchange="handleQRImageUpload(event)">
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT: Attendance List (3 cols) --}}
        <div class="lg:col-span-3">
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                <div class="p-4 border-b border-gray-100">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h2 class="text-base font-bold text-gray-900">Daftar Absensi</h2>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $selectedBootcamp->title }}</p>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <a href="{{ route('admin.tickets.export-pdf', $selectedBootcamp) }}" title="Export PDF"
                               class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 text-xs font-semibold rounded-lg transition-colors border border-red-200">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                PDF
                            </a>
                            <a href="{{ route('admin.tickets.export-excel', $selectedBootcamp) }}" title="Export Excel"
                               class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-green-50 hover:bg-green-100 text-green-600 text-xs font-semibold rounded-lg transition-colors border border-green-200">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Excel
                            </a>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="filterAbsensi('all')" id="filterAll" class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700 transition-colors">Semua</button>
                        <button onclick="filterAbsensi('hadir')" id="filterHadir" class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600 hover:bg-green-100 hover:text-green-700 transition-colors">Hadir</button>
                        <button onclick="filterAbsensi('belum')" id="filterBelum" class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600 hover:bg-red-100 hover:text-red-700 transition-colors">Belum</button>
                    </div>
                </div>

                <div class="overflow-x-auto max-h-[600px] overflow-y-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200 sticky top-0">
                            <tr>
                                <th class="px-4 py-2.5 text-left text-xs font-bold text-gray-600 uppercase">#</th>
                                <th class="px-4 py-2.5 text-left text-xs font-bold text-gray-600 uppercase">Peserta</th>
                                <th class="px-4 py-2.5 text-left text-xs font-bold text-gray-600 uppercase">Kode Tiket</th>
                                <th class="px-4 py-2.5 text-center text-xs font-bold text-gray-600 uppercase">Status</th>
                                <th class="px-4 py-2.5 text-center text-xs font-bold text-gray-600 uppercase">Jam</th>
                            </tr>
                        </thead>
                        <tbody id="attendanceBody" class="divide-y divide-gray-50">
                            @forelse($registrations as $idx => $reg)
                                <tr class="hover:bg-gray-50/50 transition-colors attendance-row {{ $reg->checked_in ? 'row-hadir' : 'row-belum' }}"
                                    data-ticket="{{ $reg->ticket_code }}" id="row-{{ $reg->ticket_code }}">
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $idx + 1 }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2.5">
                                            <img src="{{ avatarUrl($reg->user) }}" alt="" class="w-8 h-8 rounded-full object-cover">
                                            <div class="min-w-0">
                                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $reg->user->name }}</p>
                                                <p class="text-xs text-gray-400 truncate">{{ $reg->user->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <code class="px-2 py-1 bg-gray-100 rounded text-xs font-mono text-gray-600">{{ $reg->ticket_code }}</code>
                                    </td>
                                    <td class="px-4 py-3 text-center" id="status-{{ $reg->ticket_code }}">
                                        @if($reg->checked_in)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                Hadir
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-500">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                                Belum
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center text-xs text-gray-500" id="time-{{ $reg->ticket_code }}">
                                        @if($reg->checked_in)
                                            <span class="font-semibold text-green-700">{{ $reg->checked_in_at->format('H:i') }}</span>
                                        @else
                                            <span class="text-gray-300">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-12 text-center text-gray-400 text-sm">
                                        Belum ada peserta terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    @else

    {{-- No bootcamp selected --}}
    <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <h3 class="text-lg font-bold text-gray-700 mb-2">Pilih Bootcamp Terlebih Dahulu</h3>
        <p class="text-sm text-gray-500 max-w-md mx-auto">Pilih program bootcamp di atas untuk mulai scan QR code dan mencatat absensi peserta.</p>
    </div>

    @endif

</div>

<style>
    @keyframes scanLine {
        0%   { top: 8px; opacity: 1; }
        50%  { top: calc(100% - 10px); opacity: 0.8; }
        100% { top: 8px; opacity: 1; }
    }
    @keyframes framePulse {
        0%, 100% { opacity: 0.4; }
        50%      { opacity: 1; }
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    @keyframes highlightRow {
        0%   { background-color: #dcfce7; }
        100% { background-color: transparent; }
    }
    .row-highlight {
        animation: highlightRow 3s ease-out;
    }
</style>

@if($selectedBootcamp)
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
<script nonce="{{ $cspNonce ?? '' }}">
    var canvas, canvasCtx, isProcessing = false, videoStream = null;
    var PROCESS_URL = '{{ route("admin.tickets.process-scan") }}';
    var CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
    var currentFilter = 'all';

    // ── Camera ────────────────────────────────────────────────────────────────
    async function initCamera() {
        try {
            document.getElementById('permissionWarning').classList.add('hidden');
            var stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'environment', width: { ideal: 1280 }, height: { ideal: 720 } },
                audio: false
            });
            videoStream = stream;
            var video = document.getElementById('video');
            video.srcObject = stream;
            canvas = document.createElement('canvas');
            canvasCtx = canvas.getContext('2d', { willReadFrequently: true });
            video.onloadedmetadata = function() {
                video.play();
                document.getElementById('cameraPlaceholder').style.display = 'none';
                scanQR();
            };
        } catch (err) {
            console.error('Camera error:', err);
            handleCameraError(err);
        }
    }

    function handleCameraError(error) {
        var ph = document.getElementById('cameraPlaceholder');
        ph.innerHTML = '<svg class="w-14 h-14 mb-3 text-yellow-500 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg><p class="text-sm font-medium text-gray-300">Kamera tidak tersedia</p><p class="text-xs text-gray-500 mt-1">Gunakan input manual</p>';
        document.getElementById('permissionWarning').classList.remove('hidden');
        var msg = 'Gagal mengakses kamera.';
        if (error.name === 'NotAllowedError') msg = 'Izinkan akses kamera, lalu klik Coba Lagi.';
        else if (error.name === 'NotFoundError') msg = 'Tidak ada kamera. Gunakan input manual.';
        else if (error.name === 'NotReadableError') msg = 'Kamera dipakai aplikasi lain.';
        showAlert('warning', 'Kamera Error', msg);
    }

    function retryCamera() {
        if (videoStream) { videoStream.getTracks().forEach(function(t) { t.stop(); }); videoStream = null; }
        var ph = document.getElementById('cameraPlaceholder');
        ph.style.display = 'flex';
        ph.innerHTML = '<svg class="w-14 h-14 mb-3 opacity-50 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg><p class="text-sm font-medium text-gray-400">Menginisialisasi kamera...</p>';
        document.getElementById('permissionWarning').classList.add('hidden');
        initCamera();
    }

    // ── QR Scanning ───────────────────────────────────────────────────────────
    function scanQR() {
        var video = document.getElementById('video');
        if (video.readyState === video.HAVE_ENOUGH_DATA && !isProcessing) {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvasCtx.drawImage(video, 0, 0, canvas.width, canvas.height);
            try {
                var imageData = canvasCtx.getImageData(0, 0, canvas.width, canvas.height);
                var code = jsQR(imageData.data, imageData.width, imageData.height, { inversionAttempts: 'dontInvert' });
                if (code && code.data) {
                    var tc = code.data.trim();
                    if (tc.length > 0) processTicket(tc);
                }
            } catch (e) {}
        }
        requestAnimationFrame(scanQR);
    }

    // ── Process Ticket ────────────────────────────────────────────────────────
    async function processTicket(ticketCode) {
        if (isProcessing) return;
        isProcessing = true;
        document.getElementById('loadingOverlay').style.display = 'flex';
        try {
            var res = await fetch(PROCESS_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                body: JSON.stringify({ ticket_code: ticketCode })
            });
            var data = await res.json();
            if (data.success) {
                showAlert('success', 'Absensi Berhasil!', data.message, data.ticket);
                updateRowToHadir(data.ticket.ticket_code, data.ticket.checked_in_at);
                if (data.stats) updateStats(data.stats);
                document.getElementById('manualTicketCode').value = '';
            } else {
                showAlert('error', 'Gagal', data.message, data.ticket || null);
            }
        } catch (err) {
            showAlert('error', 'Error Koneksi', 'Gagal menghubungi server.');
            console.error(err);
        } finally {
            document.getElementById('loadingOverlay').style.display = 'none';
            isProcessing = false;
        }
    }

    function processManualInput() {
        var code = document.getElementById('manualTicketCode').value.trim();
        if (code.length > 0) processTicket(code);
    }

    function handleQRImageUpload(event) {
        var file = event.target.files[0];
        if (!file) return;
        if (!canvas) { canvas = document.createElement('canvas'); canvasCtx = canvas.getContext('2d', { willReadFrequently: true }); }
        var reader = new FileReader();
        reader.onload = function(e) {
            var img = new Image();
            img.onload = function() {
                canvas.width = img.width; canvas.height = img.height;
                canvasCtx.drawImage(img, 0, 0);
                try {
                    var imageData = canvasCtx.getImageData(0, 0, img.width, img.height);
                    var code = jsQR(imageData.data, imageData.width, imageData.height, { inversionAttempts: 'attemptBoth' });
                    if (code && code.data) processTicket(code.data.trim());
                    else showAlert('error', 'QR Tidak Terdeteksi', 'Gambar tidak mengandung QR code.');
                } catch (err) { showAlert('error', 'Error', 'Gagal membaca gambar.'); }
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
        event.target.value = '';
    }

    // ── Update Row in Table ───────────────────────────────────────────────────
    function updateRowToHadir(ticketCode, checkedInAt) {
        var row = document.getElementById('row-' + ticketCode);
        if (!row) return;

        // Update class
        row.classList.remove('row-belum');
        row.classList.add('row-hadir', 'row-highlight');

        // Update status cell
        var statusCell = document.getElementById('status-' + ticketCode);
        if (statusCell) {
            statusCell.innerHTML = '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>Hadir</span>';
        }

        // Update time cell
        var timeCell = document.getElementById('time-' + ticketCode);
        if (timeCell) {
            var now = new Date();
            var time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            timeCell.innerHTML = '<span class="font-semibold text-green-700">' + time + '</span>';
        }

        // Apply filter visibility
        filterAbsensi(currentFilter);

        // Scroll row into view
        row.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    // ── Update Stats ──────────────────────────────────────────────────────────
    function updateStats(stats) {
        document.getElementById('statHadir').textContent = stats.checked_in;
        document.getElementById('statBelum').textContent = stats.total - stats.checked_in;
        document.getElementById('statPersen').textContent = stats.percentage + '%';
        document.getElementById('progressLabel').textContent = stats.checked_in + '/' + stats.total;
        document.getElementById('progressBar').style.width = stats.percentage + '%';
    }

    // ── Filter ────────────────────────────────────────────────────────────────
    function filterAbsensi(type) {
        currentFilter = type;
        var rows = document.querySelectorAll('.attendance-row');
        rows.forEach(function(row) {
            if (type === 'all') { row.style.display = ''; }
            else if (type === 'hadir') { row.style.display = row.classList.contains('row-hadir') ? '' : 'none'; }
            else if (type === 'belum') { row.style.display = row.classList.contains('row-belum') ? '' : 'none'; }
        });

        // Update button styles
        ['All', 'Hadir', 'Belum'].forEach(function(name) {
            var btn = document.getElementById('filter' + name);
            btn.className = 'px-3 py-1 text-xs font-semibold rounded-full transition-colors ';
            if (name.toLowerCase() === type || (name === 'All' && type === 'all')) {
                if (type === 'all') btn.className += 'bg-blue-100 text-blue-700';
                else if (type === 'hadir') btn.className += 'bg-green-100 text-green-700';
                else btn.className += 'bg-red-100 text-red-700';
            } else {
                btn.className += 'bg-gray-100 text-gray-600 hover:bg-gray-200';
            }
        });
    }

    // ── Alert ─────────────────────────────────────────────────────────────────
    function showAlert(type, title, message, details) {
        var el = document.getElementById('statusAlert');
        var icon = document.getElementById('statusIcon');
        var titleEl = document.getElementById('statusTitle');
        var msgEl = document.getElementById('statusMessage');
        var detailsEl = document.getElementById('statusDetails');
        el.className = 'p-4 rounded-xl border';
        if (type === 'success') {
            el.classList.add('bg-green-50', 'border-green-200');
            icon.innerHTML = '<svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
            titleEl.className = 'font-semibold text-sm text-green-900';
            msgEl.className = 'text-sm text-green-700 mt-1';
        } else if (type === 'error') {
            el.classList.add('bg-red-50', 'border-red-200');
            icon.innerHTML = '<svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>';
            titleEl.className = 'font-semibold text-sm text-red-900';
            msgEl.className = 'text-sm text-red-700 mt-1';
        } else {
            el.classList.add('bg-yellow-50', 'border-yellow-200');
            icon.innerHTML = '<svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>';
            titleEl.className = 'font-semibold text-sm text-yellow-900';
            msgEl.className = 'text-sm text-yellow-700 mt-1';
        }
        titleEl.textContent = title;
        msgEl.textContent = message;
        if (details) {
            detailsEl.innerHTML = '<p><strong>Nama:</strong> ' + details.user_name + '</p><p><strong>Event:</strong> ' + details.bootcamp_name + '</p><p><strong>Kode:</strong> ' + details.ticket_code + '</p><p><strong>Waktu:</strong> ' + details.checked_in_at + '</p>';
            detailsEl.style.display = '';
        } else {
            detailsEl.innerHTML = '';
            detailsEl.style.display = 'none';
        }
        el.classList.remove('hidden');
        if (type !== 'warning') setTimeout(function() { el.classList.add('hidden'); }, 5000);
    }

    // ── Init ──────────────────────────────────────────────────────────────────
    document.getElementById('manualTicketCode').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') processManualInput();
    });
    window.addEventListener('load', initCamera);
</script>
@endif
@endsection