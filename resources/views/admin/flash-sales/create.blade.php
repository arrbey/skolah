@extends('layouts.admin')

@section('title', 'Buat Flash Sale Baru')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.flash-sales.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-slate-900 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali ke Daftar
    </a>
    <h1 class="text-2xl font-bold text-slate-900 mt-2">Buat Flash Sale Baru</h1>
</div>

<div class="max-w-3xl">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <form action="{{ route('admin.flash-sales.store') }}" method="POST" class="p-6">
            @csrf
            
            <div class="space-y-6">
                {{-- Title --}}
                <div>
                    <label for="title" class="block text-sm font-bold text-slate-700 mb-2">Judul Flash Sale</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" placeholder="Contoh: Promo Ramadhan Kilat" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none @error('title') border-rose-500 @enderror" required>
                    @error('title') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-bold text-slate-700 mb-2">Deskripsi (Opsional)</label>
                    <textarea name="description" id="description" rows="3" placeholder="Jelaskan promo ini kepada pengguna..." class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none @error('description') border-rose-500 @enderror">{{ old('description') }}</textarea>
                    @error('description') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Start At --}}
                    <div>
                        <label for="start_at" class="block text-sm font-bold text-slate-700 mb-2">Waktu Mulai</label>
                        <input type="datetime-local" name="start_at" id="start_at" value="{{ old('start_at') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none @error('start_at') border-rose-500 @enderror" required>
                        @error('start_at') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- End At --}}
                    <div>
                        <label for="end_at" class="block text-sm font-bold text-slate-700 mb-2">Waktu Selesai</label>
                        <input type="datetime-local" name="end_at" id="end_at" value="{{ old('end_at') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none @error('end_at') border-rose-500 @enderror" required>
                        @error('end_at') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Is Active --}}
                <div class="flex items-center gap-3 p-4 bg-slate-50 rounded-lg">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                    <label for="is_active" class="text-sm font-bold text-slate-700">Aktifkan Flash Sale</label>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end gap-3">
                <a href="{{ route('admin.flash-sales.index') }}" class="px-6 py-2 border border-slate-200 text-slate-600 font-bold rounded-lg hover:bg-slate-50 transition-colors">Batal</a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 shadow-lg shadow-blue-500/20 transition-all">Simpan Flash Sale</button>
            </div>
        </form>
    </div>
</div>
@endsection
