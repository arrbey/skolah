{{-- Shared bootcamp form partial --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Main --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-5">
            <h2 class="text-base font-bold text-gray-900">Informasi Bootcamp</h2>

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul <span class="text-red-500">*</span></label>
                <input type="text" id="title" name="title" value="{{ old('title', $bootcamp?->title) }}" required
                       class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('title') border-red-500 @enderror">
                @error('title') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi <span class="text-red-500">*</span></label>
                <textarea id="description" name="description" rows="6" required
                          class="tinymce w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('description') border-red-500 @enderror">{{ old('description', $bootcamp?->description) }}</textarea>
                @error('description') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <x-image-upload 
                name="thumbnail" 
                :value="$bootcamp?->thumbnail_url" 
                label="Thumbnail Bootcamp" 
                info="1280 x 720 px (16:9)" 
                aspect="aspect-video"
                :required="!$bootcamp"
            />

            <div class="grid grid-cols-2 gap-4" x-data="{ type: '{{ old('type', $bootcamp?->type ?? 'online') }}' }">
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Tipe <span class="text-red-500">*</span></label>
                    <select id="type" name="type" x-model="type" required
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="online">Online</option>
                        <option value="offline">Offline</option>
                    </select>
                </div>
                <div x-show="type === 'online'">
                    <label for="platform" class="block text-sm font-medium text-gray-700 mb-1">Platform</label>
                    <select id="platform" name="platform"
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="Zoom" {{ old('platform', $bootcamp?->platform) === 'Zoom' ? 'selected' : '' }}>Zoom</option>
                        <option value="Google Meet" {{ old('platform', $bootcamp?->platform) === 'Google Meet' ? 'selected' : '' }}>Google Meet</option>
                    </select>
                    @error('platform') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="meeting_link" class="block text-sm font-medium text-gray-700 mb-1">Meeting Link / Lokasi</label>
                <input type="text" id="meeting_link" name="meeting_link" value="{{ old('meeting_link', $bootcamp?->meeting_link) }}"
                       class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                @error('meeting_link') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Lokasi (untuk offline)</label>
                <input type="text" id="location" name="location" value="{{ old('location', $bootcamp?->location) }}"
                       class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                @error('location') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                    <input type="datetime-local" id="start_date" name="start_date" required
                           value="{{ old('start_date', $bootcamp?->start_date?->format('Y-m-d\TH:i')) }}"
                           class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('start_date') border-red-500 @enderror">
                    @error('start_date') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai <span class="text-red-500">*</span></label>
                    <input type="datetime-local" id="end_date" name="end_date" required
                           value="{{ old('end_date', $bootcamp?->end_date?->format('Y-m-d\TH:i')) }}"
                           class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('end_date') border-red-500 @enderror">
                    @error('end_date') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- SEO --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-5">
            <h2 class="text-base font-bold text-gray-900">SEO (Opsional)</h2>
            <div>
                <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
                <input type="text" id="meta_title" name="meta_title" value="{{ old('meta_title', $bootcamp?->meta_title) }}"
                       class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                <textarea id="meta_description" name="meta_description" rows="2"
                          class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('meta_description', $bootcamp?->meta_description) }}</textarea>
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-6">
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-5">
            <h2 class="text-base font-bold text-gray-900">Pengaturan</h2>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                <select id="status" name="status" required
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="upcoming" {{ old('status', $bootcamp?->status ?? 'upcoming') === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                    <option value="ongoing" {{ old('status', $bootcamp?->status) === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                    <option value="completed" {{ old('status', $bootcamp?->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>

            <div>
                <label for="max_participants" class="block text-sm font-medium text-gray-700 mb-1">Maks Peserta</label>
                <input type="number" id="max_participants" name="max_participants" min="0"
                       value="{{ old('max_participants', $bootcamp?->max_participants) }}"
                       placeholder="0 = unlimited"
                       class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                @error('max_participants') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="pt-2 border-t border-gray-100">
                <label for="institution_id" class="block text-sm font-medium text-gray-700 mb-1">Lembaga (Opsional)</label>
                <select id="institution_id" name="institution_id"
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Umum (Tanpa Lembaga)</option>
                    @foreach($institutions as $inst)
                        <option value="{{ $inst->id }}" {{ old('institution_id', $bootcamp?->institution_id) == $inst->id ? 'selected' : '' }}>{{ $inst->name }}</option>
                    @endforeach
                </select>
                @error('institution_id') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                <p class="text-[10px] text-gray-400 mt-1 italic">Pilih jika ini bootcamp milik lembaga tertentu.</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-5">
            <h2 class="text-base font-bold text-gray-900">Harga</h2>
            <div>
                <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp) <span class="text-red-500">*</span></label>
                <input type="number" id="price" name="price" min="0" required value="{{ old('price', $bootcamp?->price ?? 0) }}"
                       class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                @error('price') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="discount_price" class="block text-sm font-medium text-gray-700 mb-1">Harga Diskon (Rp)</label>
                <input type="number" id="discount_price" name="discount_price" min="0" value="{{ old('discount_price', $bootcamp?->discount_price) }}"
                       class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                @error('discount_price') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="flex-1 px-5 py-3 rounded-xl bg-secondary-600 text-white text-sm font-semibold hover:bg-secondary-700 transition-colors text-center">
                {{ $bootcamp ? 'Simpan Perubahan' : 'Simpan Bootcamp' }}
            </button>
            <a href="{{ route('instructor.bootcamps.index') }}" class="px-5 py-3 rounded-xl bg-gray-100 text-gray-700 text-sm font-medium hover:bg-gray-200 transition-colors text-center">Batal</a>
        </div>
    </div>
</div>
