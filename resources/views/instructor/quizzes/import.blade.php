@extends('layouts.instructor')

@section('title', 'Import Soal Aiken — ' . ucfirst($quiz->type))

@section('page-header')
    <div class="flex items-center gap-3">
        <a href="{{ route('instructor.courses.quizzes.questions', [$course, $quiz]) }}" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-lg font-bold text-gray-900">Import Soal (Aiken Format)</h1>
            <p class="text-sm text-gray-500 truncate max-w-xs">{{ $quiz->title }}</p>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-3xl">

    {{-- Flash --}}
    @if(session('parse_errors'))
        <div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800">
            <p class="font-semibold mb-2 text-sm">❌ Format tidak valid:</p>
            <ul class="list-disc pl-5 space-y-1 text-sm">
                @foreach(session('parse_errors') as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($errors->has('content'))
        <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm">
            {{ $errors->first('content') }}
        </div>
    @endif

    {{-- Panduan Format --}}
    <div class="mb-6 bg-blue-50 border border-blue-200 rounded-2xl p-5">
        <div class="flex items-start gap-3">
            <div class="w-9 h-9 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="font-semibold text-blue-900 mb-1.5">Panduan Format Aiken</h3>
                <p class="text-sm text-blue-800 mb-3">Format standar Moodle untuk import soal pilihan ganda. Setiap soal dipisahkan <strong>baris kosong</strong>.</p>

                <div class="bg-white border border-blue-200 rounded-lg p-3 mb-3">
                    <pre class="text-xs text-gray-700 font-mono whitespace-pre-wrap leading-relaxed">Apa ibu kota Indonesia?
A. Jakarta
B. Surabaya
C. Bandung
D. Medan
ANSWER: A

2 + 2 = ?
A. 3
B. 4
C. 5
ANSWER: B</pre>
                </div>

                <ul class="text-xs text-blue-800 space-y-1 list-disc pl-4">
                    <li>Baris pertama = pertanyaan</li>
                    <li>Pilihan diawali huruf kapital <code class="bg-white px-1 rounded">A.</code>, <code class="bg-white px-1 rounded">B.</code>, dst (titik atau kurung)</li>
                    <li>Jawaban: <code class="bg-white px-1 rounded">ANSWER: &lt;huruf&gt;</code> di baris terakhir</li>
                    <li>Minimal 2 pilihan per soal</li>
                    <li>Semua soal di-import sebagai <strong>Pilihan Ganda</strong></li>
                </ul>

                <button type="button" onclick="downloadSample()" class="mt-3 text-xs font-medium text-blue-700 hover:text-blue-900 underline">
                    📥 Download contoh file sample.txt
                </button>
            </div>
        </div>
    </div>

    {{-- Form Import --}}
    <form action="{{ route('instructor.courses.quizzes.import.store', [$course, $quiz]) }}" method="POST" enctype="multipart/form-data"
          x-data="{ tab: 'paste' }" class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-5">
        @csrf

        {{-- Tab Switcher --}}
        <div class="flex gap-2 border-b border-gray-100 pb-3">
            <button type="button" @click="tab = 'paste'"
                    :class="tab === 'paste' ? 'bg-primary-50 text-primary-700 border-primary-200' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50'"
                    class="px-4 py-2 rounded-lg text-sm font-medium border transition-colors">
                📝 Paste Text
            </button>
            <button type="button" @click="tab = 'file'"
                    :class="tab === 'file' ? 'bg-primary-50 text-primary-700 border-primary-200' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50'"
                    class="px-4 py-2 rounded-lg text-sm font-medium border transition-colors">
                📎 Upload File (.txt)
            </button>
        </div>

        {{-- Paste Tab --}}
        <div x-show="tab === 'paste'">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Isi Soal Aiken</label>
            <textarea name="content" rows="14"
                      class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm font-mono focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                      placeholder="Tempelkan soal format Aiken di sini...">{{ old('content') }}</textarea>
            <p class="text-xs text-gray-400 mt-1">Maks 100.000 karakter</p>
        </div>

        {{-- File Tab --}}
        <div x-show="tab === 'file'">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Upload File .txt</label>
            <input type="file" name="file" accept=".txt"
                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
            <p class="text-xs text-gray-400 mt-1">Maks 1 MB, format plain text</p>
        </div>

        {{-- Options --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-gray-100">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Poin per Soal <span class="text-red-500">*</span></label>
                <input type="number" name="points_per_question" value="{{ old('points_per_question', 1) }}" min="1" max="100" required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                <p class="text-xs text-gray-400 mt-1">Berlaku untuk semua soal yang di-import</p>
            </div>

            <div class="flex items-start">
                <label class="flex items-start gap-2.5 cursor-pointer mt-6">
                    <input type="hidden" name="replace_existing" value="0">
                    <input type="checkbox" name="replace_existing" value="1" {{ old('replace_existing') ? 'checked' : '' }}
                           class="mt-0.5 rounded border-gray-300 text-red-600 focus:ring-red-500">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Ganti semua soal lama</p>
                        <p class="text-xs text-red-600">⚠ Soal & jawaban siswa akan terhapus</p>
                    </div>
                </label>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex gap-3 pt-4 border-t border-gray-100">
            <button type="submit"
                    class="flex-1 py-2.5 {{ $quiz->type === 'pretest' ? 'bg-blue-600 hover:bg-blue-700' : 'bg-purple-600 hover:bg-purple-700' }} text-white font-semibold rounded-xl transition-colors">
                Import Soal
            </button>
            <a href="{{ route('instructor.courses.quizzes.questions', [$course, $quiz]) }}"
               class="px-5 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
function downloadSample() {
    const sample = `Apa ibu kota negara Indonesia?
A. Jakarta
B. Surabaya
C. Bandung
D. Medan
ANSWER: A

Manakah yang merupakan bahasa pemrograman?
A. HTML
B. Python
C. Microsoft Word
D. Photoshop
ANSWER: B

Siapakah penemu bola lampu?
A. Albert Einstein
B. Isaac Newton
C. Thomas Edison
D. Nikola Tesla
ANSWER: C
`;
    const blob = new Blob([sample], { type: 'text/plain' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'sample-aiken.txt';
    a.click();
    URL.revokeObjectURL(url);
}
</script>
@endpush
@endsection
