{{-- Shared book form partial --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="{ bookType: '{{ old('type', $book?->type ?? 'digital') }}' }">

    {{-- Main --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-5">
            <h2 class="text-base font-bold text-gray-900">Informasi Buku</h2>

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul <span class="text-red-500">*</span></label>
                <input type="text" id="title" name="title" value="{{ old('title', $book?->title) }}" required
                       class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('title') border-red-500 @enderror">
                @error('title') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="author" class="block text-sm font-medium text-gray-700 mb-1">Penulis <span class="text-red-500">*</span></label>
                    <input type="text" id="author" name="author" value="{{ old('author', $book?->author) }}" required
                           class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('author') border-red-500 @enderror">
                    @error('author') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="publisher" class="block text-sm font-medium text-gray-700 mb-1">Penerbit</label>
                    <input type="text" id="publisher" name="publisher" value="{{ old('publisher', $book?->publisher) }}"
                           class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi <span class="text-red-500">*</span></label>
                <textarea id="description" name="description" rows="6" required
                          class="tinymce w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('description') border-red-500 @enderror">{{ old('description', $book?->description) }}</textarea>
                @error('description') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div x-data="{ preview: '{{ $book?->cover_url ?? '' }}' }">
                <label class="block text-sm font-medium text-gray-700 mb-1">Cover Buku</label>
                <div class="flex items-start gap-4">
                    <div class="w-28 h-40 bg-gray-100 rounded-xl overflow-hidden border-2 border-dashed border-gray-300 flex items-center justify-center shrink-0">
                        <img x-show="preview" :src="preview" class="w-full h-full object-cover" x-cloak>
                        <svg x-show="!preview" class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <div>
                        <input type="file" name="cover_image" accept="image/*" @change="preview = URL.createObjectURL($event.target.files[0])"
                               class="text-sm text-gray-600 file:mr-3 file:px-4 file:py-2 file:rounded-xl file:border-0 file:bg-primary-50 file:text-primary-600 file:text-sm file:font-medium hover:file:bg-primary-100">
                        <p class="text-xs text-gray-400 mt-1">JPG, PNG, max 2 MB</p>
                        @error('cover_image') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label for="isbn" class="block text-sm font-medium text-gray-700 mb-1">ISBN</label>
                    <input type="text" id="isbn" name="isbn" value="{{ old('isbn', $book?->isbn) }}"
                           class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label for="pages" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Halaman</label>
                    <input type="number" id="pages" name="pages" min="1" value="{{ old('pages', $book?->pages) }}"
                           class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Tipe <span class="text-red-500">*</span></label>
                    <select id="type" name="type" x-model="bookType" required
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="digital">Digital</option>
                        <option value="physical">Fisik</option>
                        <option value="both">Keduanya</option>
                    </select>
                </div>
            </div>

            {{-- Digital file upload --}}
            <div x-show="bookType === 'digital' || bookType === 'both'" x-transition class="bg-blue-50 rounded-xl p-4 space-y-3">
                <h3 class="text-sm font-semibold text-blue-800">File Digital (PDF)</h3>
                @if($book?->file_path)
                    <p class="text-xs text-blue-600">File saat ini: {{ basename($book->file_path) }}</p>
                @endif
                <input type="file" name="file_path" accept="application/pdf"
                       class="text-sm text-gray-600 file:mr-3 file:px-4 file:py-2 file:rounded-xl file:border-0 file:bg-blue-100 file:text-blue-700 file:text-sm file:font-medium hover:file:bg-blue-200">
                <p class="text-xs text-blue-500">PDF, max 50 MB</p>
                @error('file_path') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Stock (for physical) --}}
            <div x-show="bookType === 'physical' || bookType === 'both'" x-transition>
                <label for="stock" class="block text-sm font-medium text-gray-700 mb-1">Stok</label>
                <input type="number" id="stock" name="stock" min="0" value="{{ old('stock', $book?->stock ?? 0) }}"
                       class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
        </div>

        {{-- SEO --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-5">
            <h2 class="text-base font-bold text-gray-900">SEO (Opsional)</h2>
            <div>
                <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
                <input type="text" id="meta_title" name="meta_title" value="{{ old('meta_title', $book?->meta_title) }}"
                       class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                <textarea id="meta_description" name="meta_description" rows="2"
                          class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('meta_description', $book?->meta_description) }}</textarea>
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
                    <option value="draft" {{ old('status', $book?->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ old('status', $book?->status) === 'published' ? 'selected' : '' }}>Published</option>
                </select>
            </div>

            <div class="pt-2 border-t border-gray-100">
                <label for="institution_id" class="block text-sm font-medium text-gray-700 mb-1">Lembaga (Opsional)</label>
                <select id="institution_id" name="institution_id"
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Umum (Tanpa Lembaga)</option>
                    @foreach($institutions as $inst)
                        <option value="{{ $inst->id }}" {{ old('institution_id', $book?->institution_id) == $inst->id ? 'selected' : '' }}>{{ $inst->name }}</option>
                    @endforeach
                </select>
                <p class="text-[10px] text-gray-400 mt-1 italic">Pilih jika ini buku milik lembaga tertentu.</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-5">
            <h2 class="text-base font-bold text-gray-900">Harga</h2>
            <div>
                <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp) <span class="text-red-500">*</span></label>
                <input type="number" id="price" name="price" min="0" required value="{{ old('price', $book?->price ?? 0) }}"
                       class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                @error('price') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="discount_price" class="block text-sm font-medium text-gray-700 mb-1">Harga Diskon (Rp)</label>
                <input type="number" id="discount_price" name="discount_price" min="0" value="{{ old('discount_price', $book?->discount_price) }}"
                       class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                @error('discount_price') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="flex-1 px-5 py-3 rounded-xl bg-secondary-600 text-white text-sm font-semibold hover:bg-secondary-700 transition-colors text-center">
                {{ $book ? 'Simpan Perubahan' : 'Simpan Buku' }}
            </button>
            <a href="{{ route('instructor.books.index') }}" class="px-5 py-3 rounded-xl bg-gray-100 text-gray-700 text-sm font-medium hover:bg-gray-200 transition-colors text-center">Batal</a>
        </div>
    </div>
</div>
