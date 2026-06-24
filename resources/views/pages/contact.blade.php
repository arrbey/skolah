@extends('layouts.app')
@section('title', 'Hubungi Kami' . ' — ' . \App\Models\Setting::get('site_name', '' . \App\Models\Setting::get('site_name', 'Skolah.com') . ''))

@section('content')

<section class="bg-gradient-to-br from-primary-700 via-primary-600 to-secondary-600 pt-28 pb-16">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 text-center">
        <h1 class="text-4xl font-bold text-white mb-4">Hubungi Kami</h1>
        <p class="text-white/80 text-lg">Ada pertanyaan? Tim kami siap membantu kamu.</p>
    </div>
</section>

<section class="bg-gray-50 py-16">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-3 gap-8">

            {{-- Contact Info --}}
            <div class="space-y-6">
                @php
                    $email = \App\Models\Setting::get('site_email', 'hello@skolah.com');
                    $whatsapp = \App\Models\Setting::get('site_whatsapp', '+62 811-0000-0000');
                    $address = \App\Models\Setting::get('site_address', 'Jakarta, Indonesia');
                    
                    $whatsappClean = preg_replace('/[^0-9]/', '', $whatsapp);
                    if(str_starts_with($whatsappClean, '0')) {
                        $whatsappClean = '62' . substr($whatsappClean, 1);
                    }
                @endphp
                @foreach([
                    ['icon' => '📧', 'title' => 'Email', 'value' => $email, 'href' => 'mailto:'.$email],
                    ['icon' => '💬', 'title' => 'WhatsApp', 'value' => $whatsapp, 'href' => 'https://wa.me/'.$whatsappClean],
                    ['icon' => '📍', 'title' => 'Alamat', 'value' => $address, 'href' => null],
                    ['icon' => '⏰', 'title' => 'Jam Operasional', 'value' => 'Senin–Jumat, 09.00–17.00 WIB', 'href' => null],
                ] as $info)
                    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-start gap-4">
                        <span class="text-2xl flex-shrink-0">{{ $info['icon'] }}</span>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-0.5">{{ $info['title'] }}</p>
                            @if($info['href'])
                                <a href="{{ $info['href'] }}" class="text-sm font-medium text-primary-600 hover:underline">{{ $info['value'] }}</a>
                            @else
                                <p class="text-sm font-medium text-gray-700">{{ $info['value'] }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Contact Form --}}
            <div class="lg:col-span-2 bg-white rounded-2xl p-8 border border-gray-100 shadow-sm">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Kirim Pesan</h2>

                @if(session('success'))
                    <div class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl mb-6">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('contact') }}" method="POST" class="space-y-5">
                    @csrf
                    <div class="grid sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', auth()->user()?->name) }}" required
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary-400 focus:ring-1 focus:ring-primary-200 @error('name') border-red-400 @enderror">
                            @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" required
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary-400 focus:ring-1 focus:ring-primary-200 @error('email') border-red-400 @enderror">
                            @error('email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Subjek <span class="text-red-500">*</span></label>
                        <select name="subject" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary-400 focus:ring-1 focus:ring-primary-200">
                            <option value="">-- Pilih Topik --</option>
                            <option value="Pertanyaan Umum" {{ old('subject') === 'Pertanyaan Umum' ? 'selected' : '' }}>Pertanyaan Umum</option>
                            <option value="Masalah Pembayaran" {{ old('subject') === 'Masalah Pembayaran' ? 'selected' : '' }}>Masalah Pembayaran</option>
                            <option value="Masalah Teknis" {{ old('subject') === 'Masalah Teknis' ? 'selected' : '' }}>Masalah Teknis</option>
                            <option value="Kerjasama Instruktur" {{ old('subject') === 'Kerjasama Instruktur' ? 'selected' : '' }}>Kerjasama Instruktur</option>
                            <option value="Lainnya" {{ old('subject') === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Pesan <span class="text-red-500">*</span></label>
                        <textarea name="message" rows="5" required placeholder="Tuliskan pesan kamu di sini..."
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary-400 focus:ring-1 focus:ring-primary-200 resize-none @error('message') border-red-400 @enderror">{{ old('message') }}</textarea>
                        @error('message')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit"
                        class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 rounded-xl transition flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        Kirim Pesan
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

@endsection
