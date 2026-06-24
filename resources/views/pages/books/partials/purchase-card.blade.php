{{-- resources/views/pages/books/partials/purchase-card.blade.php --}}
{{-- Kartu pembelian buku — 3 state: purchased (digital/physical), not purchased --}}

<div class="bg-gray-900 border border-white/10 rounded-2xl overflow-hidden">

    {{-- ─── SUDAH DIBELI ──────────────────────────────────────────────── --}}
    @if($hasPurchased)
        <div class="p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-green-500/10 flex items-center justify-center ring-1 ring-green-500/30">
                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-green-400">Sudah Dibeli</p>
                    <p class="text-xs text-gray-500">Kamu sudah memiliki buku ini</p>
                </div>
            </div>

            {{-- Digital: Download button --}}
            @if($book->is_digital)
                <a href="{{ route('books.download', $book->slug) }}"
                   class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl font-semibold text-sm text-white
                          bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500
                          transition-all shadow-lg shadow-purple-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Download E-Book
                </a>
            @endif

            {{-- Physical: Shipping status --}}
            @if($book->is_physical && $userBookOrder)
                <div class="mt-3 p-3 bg-gray-800/50 rounded-xl ring-1 ring-white/5">
                    <p class="text-xs text-gray-500 mb-1">Status Pengiriman</p>
                    <div class="flex items-center gap-2">
                        @php
                            $statusColor = match($userBookOrder->shipping_status) {
                                'pending' => 'text-yellow-400',
                                'processing' => 'text-blue-400',
                                'shipped' => 'text-indigo-400',
                                'delivered' => 'text-green-400',
                                default => 'text-gray-400',
                            };
                        @endphp
                        <span class="w-2 h-2 rounded-full {{ str_replace('text-', 'bg-', $statusColor) }}"></span>
                        <span class="text-sm font-medium {{ $statusColor }}">{{ $userBookOrder->status_label }}</span>
                    </div>
                    @if($userBookOrder->tracking_number)
                        <p class="text-xs text-gray-400 mt-2">
                            No. Resi: <span class="font-mono text-white">{{ $userBookOrder->tracking_number }}</span>
                        </p>
                    @endif
                </div>
            @endif

            <a href="{{ route('dashboard.orders') }}"
               class="mt-3 block text-center text-sm text-gray-400 hover:text-white transition">
                Lihat Riwayat Pesanan →
            </a>
        </div>

    {{-- ─── BELUM DIBELI ──────────────────────────────────────────────── --}}
    @else
        <div class="p-6">
            {{-- Price display --}}
            <div class="mb-5">
                @if($book->effective_price === 0)
                    <p class="text-3xl font-bold text-green-400">Gratis</p>
                @else
                    <div class="flex items-end gap-3">
                        <p class="text-3xl font-bold text-white">{{ rupiah($book->effective_price) }}</p>
                        @if($book->has_discount)
                            <p class="text-sm text-gray-500 line-through mb-1">{{ rupiah($book->price) }}</p>
                            <span class="text-xs font-bold text-red-400 bg-red-500/10 px-2 py-0.5 rounded-full ring-1 ring-red-500/30 mb-1">
                                -{{ $book->discount_percent }}%
                            </span>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Type info --}}
            <div class="space-y-2 mb-5 text-sm">
                <div class="flex items-center gap-2 text-gray-400">
                    <span>📦</span>
                    <span>Format: <span class="text-white font-medium">{{ $book->type_label }}</span></span>
                </div>
                @if($book->is_physical && $book->stock !== null)
                    <div class="flex items-center gap-2 text-gray-400">
                        <span>📊</span>
                        <span>Stok: <span class="{{ $book->stock > 0 ? 'text-green-400' : 'text-red-400' }} font-medium">
                            {{ $book->stock > 0 ? $book->stock . ' tersedia' : 'Habis' }}
                        </span></span>
                    </div>
                @endif
                @if($book->is_digital)
                    <div class="flex items-center gap-2 text-gray-400">
                        <span>⚡</span>
                        <span class="text-green-400 font-medium">Download langsung setelah pembayaran</span>
                    </div>
                @endif
            </div>

            {{-- Type selection for "both" books --}}
            @if($book->type === 'both')
                <div class="mb-5" x-data="{ selectedType: 'digital' }">
                    <p class="text-xs font-medium text-gray-400 mb-2">Pilih format:</p>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="cursor-pointer">
                            <input type="radio" x-model="selectedType" value="digital" class="peer hidden" name="book_type_select">
                            <div class="p-3 rounded-xl ring-1 ring-white/10 text-center peer-checked:ring-purple-500 peer-checked:bg-purple-500/10 transition">
                                <span class="text-lg">📄</span>
                                <p class="text-xs text-gray-300 mt-1">E-Book</p>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" x-model="selectedType" value="physical" class="peer hidden" name="book_type_select">
                            <div class="p-3 rounded-xl ring-1 ring-white/10 text-center peer-checked:ring-purple-500 peer-checked:bg-purple-500/10 transition">
                                <span class="text-lg">📦</span>
                                <p class="text-xs text-gray-300 mt-1">Buku Fisik</p>
                            </div>
                        </label>
                    </div>

                    {{-- CTA form (both type) --}}
                    @auth
                        <form method="POST" action="{{ route('book.checkout.process') }}" class="mt-4">
                            @csrf
                            <input type="hidden" name="book_id" value="{{ $book->id }}">
                            <input type="hidden" name="purchase_type" :value="selectedType">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit"
                                {{ !$book->is_in_stock ? 'disabled' : '' }}
                                class="w-full py-3.5 px-4 rounded-xl font-semibold text-sm text-white
                                       bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500
                                       disabled:opacity-50 disabled:cursor-not-allowed
                                       transition-all shadow-lg shadow-purple-500/20 flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                                </svg>
                                <span x-text="selectedType === 'digital' ? 'Beli E-Book' : 'Beli Buku Fisik'"></span>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login', ['redirect' => url()->current()]) }}"
                           class="mt-4 w-full py-3.5 px-4 rounded-xl font-semibold text-sm text-white
                                  bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500
                                  transition-all shadow-lg shadow-purple-500/20 flex items-center justify-center gap-2">
                            Login untuk Membeli
                        </a>
                    @endauth
                </div>
            @else
                {{-- CTA form (single type) --}}
                @auth
                    <form method="POST" action="{{ route('book.checkout.process') }}">
                        @csrf
                        <input type="hidden" name="book_id" value="{{ $book->id }}">
                        <input type="hidden" name="purchase_type" value="{{ $book->type }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit"
                            {{ (!$book->is_in_stock && $book->is_physical) ? 'disabled' : '' }}
                            class="w-full py-3.5 px-4 rounded-xl font-semibold text-sm text-white
                                   bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500
                                   disabled:opacity-50 disabled:cursor-not-allowed
                                   transition-all shadow-lg shadow-purple-500/20 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                            </svg>
                            @if($book->effective_price === 0)
                                Dapatkan Gratis
                            @elseif($book->type === 'digital')
                                Beli E-Book
                            @else
                                Beli Buku Fisik
                            @endif
                        </button>
                    </form>
                @else
                    <a href="{{ route('login', ['redirect' => url()->current()]) }}"
                       class="w-full py-3.5 px-4 rounded-xl font-semibold text-sm text-white
                              bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500
                              transition-all shadow-lg shadow-purple-500/20 flex items-center justify-center gap-2">
                        Login untuk Membeli
                    </a>
                @endauth
            @endif

            {{-- Details --}}
            <div class="mt-5 pt-4 border-t border-white/10 space-y-2">
                <div class="flex items-center gap-2 text-xs text-gray-500">
                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Pembayaran aman via Midtrans
                </div>
                @if($book->is_digital)
                <div class="flex items-center gap-2 text-xs text-gray-500">
                    <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Akses download langsung setelah bayar
                </div>
                @endif
                @if($book->is_physical)
                <div class="flex items-center gap-2 text-xs text-gray-500">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                    Pengiriman ke seluruh Indonesia
                </div>
                @endif
            </div>
        </div>
    @endif
</div>
