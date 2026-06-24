@props([
    'name',
    'value' => null,
    'label' => 'Gambar',
    'info' => null,
    'maxSize' => '2MB',
    'accept' => 'image/*',
    'required' => false,
    'id' => null,
    'aspect' => 'aspect-video', // aspect-video, aspect-square, aspect-[4/3], etc
])

@php
    $id = $id ?? 'img_' . $name;
@endphp

<div x-data="{ 
    preview: '{{ $value }}',
    handleFileChange(event) {
        const file = event.target.files[0];
        if (file) {
            this.preview = URL.createObjectURL(file);
        }
    }
}" class="space-y-2">
    @if($label)
        <label for="{{ $id }}" class="block text-sm font-bold text-gray-700">
            {{ $label }} @if($required)<span class="text-red-500">*</span>@endif
        </label>
    @endif

    <div class="relative group">
        {{-- Preview Area --}}
        <div class="relative {{ $aspect }} w-full bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200 overflow-hidden group-hover:border-primary-400 transition-colors flex items-center justify-center">
            <template x-if="preview">
                <img :src="preview" class="w-full h-full object-cover">
            </template>
            
            <template x-if="!preview">
                <div class="flex flex-col items-center justify-center text-gray-400 p-6">
                    <svg class="w-10 h-10 mb-2 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="text-xs font-medium">Klik untuk upload gambar</span>
                </div>
            </template>

            {{-- Overlay on Hover --}}
            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center pointer-events-none">
                <div class="bg-white/20 backdrop-blur-md px-4 py-2 rounded-xl border border-white/30 text-white text-xs font-bold uppercase tracking-wider">
                    Ganti Gambar
                </div>
            </div>

            {{-- Hidden File Input --}}
            <input type="file" 
                   id="{{ $id }}" 
                   name="{{ $name }}" 
                   accept="{{ $accept }}" 
                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                   @change="handleFileChange"
                   @if($required && !$value) required @endif
                   {{ $attributes->whereStartsWith('onchange') }}>
        </div>

        {{-- Info & Validation --}}
        <div class="flex items-center justify-between mt-2 px-1">
            <div class="space-y-0.5">
                @if($info)
                    <p class="text-[11px] font-bold text-gray-500 uppercase tracking-tight">Rekomendasi: <span class="text-primary-600">{{ $info }}</span></p>
                @endif
                <p class="text-[10px] text-gray-400 font-medium italic">Format: PNG, JPG, WebP (Maks. {{ $maxSize }})</p>
            </div>
            @error($name)
                <p class="text-xs font-bold text-red-500">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
