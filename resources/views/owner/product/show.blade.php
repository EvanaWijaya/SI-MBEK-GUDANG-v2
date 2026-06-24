<x-owner-app-layout> {{-- Sesuaikan kalau lu punya <x-owner-app-layout> --}}
    <x-slot name="header">
        <h2 class="font-bold text-xl text-black leading-tight">
            Detail Produk: {{ $product->product_name }}
        </h2>
    </x-slot>

    <div class="container mx-auto mt-10 px-4" x-data="productViewForm()">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200">

            {{-- Header Card --}}
            <div class="p-6 bg-brand-orange text-white flex justify-between items-center">
                <h3 class="text-lg font-bold text-white">Informasi Data Utama</h3>
                <div class="flex gap-2">
                    <a href="{{ route('owner.products.index') }}"
                        class="bg-white text-brand-orange px-4 py-2 rounded text-sm font-bold shadow hover:bg-gray-100 transition-colors">
                        Kembali
                    </a>
                </div>
            </div>

            {{-- Content Card --}}
            <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">

                {{-- KOLOM KIRI (FOTO & INFO DASAR) --}}
                <div>
                    {{-- Area Foto --}}
                    <div class="mb-6">
                        <p class="text-xs text-black font-bold uppercase mb-2">Foto Produk</p>

                        @if($product->media->count())
                            {{-- Gambar Utama --}}
                            <div class="flex justify-center bg-gray-50 p-4 rounded-lg mb-3 cursor-pointer"
                                 @click="openLightbox(0)">
                                <img src="{{ $product->media->first()->url }}"
                                     class="max-h-72 object-contain hover:opacity-90 transition-opacity">
                            </div>

                            {{-- Thumbnail Gallery --}}
                            @if($product->media->count() > 1)
                                <div class="grid grid-cols-4 gap-2">
                                    @foreach($product->media as $index => $media)
                                        <div class="relative cursor-pointer" @click="openLightbox({{ $index }})">
                                            @if($media->is_primary)
                                                <span class="absolute bottom-1 left-1 bg-green-600 text-white text-[10px] px-1 rounded z-10">
                                                    Utama
                                                </span>
                                            @endif
                                            <img src="{{ $media->url }}"
                                                 class="h-20 w-full object-cover rounded border hover:opacity-80 transition-opacity">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @else
                            <div class="bg-gray-50 p-8 rounded-lg text-center text-gray-400 border border-dashed border-gray-300">
                                <p>Belum ada foto produk</p>
                            </div>
                        @endif
                    </div>

                    {{-- KODE PRODUK --}}
                    <div class="mb-4">
                        <p class="text-xs text-black font-bold uppercase mb-1">Kode</p>
                        <p class="text-lg text-gray-800 font-semibold">{{ $product->product_code }}</p>
                    </div>

                    {{-- NAMA PRODUK --}}
                    <div class="mb-4">
                        <p class="text-xs text-black font-bold uppercase mb-1">Nama Produk</p>
                        <p class="text-lg text-gray-800 font-semibold">{{ $product->product_name }}</p>
                    </div>
                </div>

                {{-- KOLOM KANAN (DETAIL LAIN) --}}
                <div>
                    <div class="mb-4">
                        <p class="text-xs text-black font-bold uppercase mb-1">Stok Sistem Saat Ini</p>
                        <p class="text-3xl font-bold text-orange-600">
                            {{ $product->stock }} <span class="text-sm text-gray-500">Unit</span>
                        </p>
                    </div>

                    {{-- TIPE / KATEGORI --}}
                    <div class="mb-4">
                        <p class="text-xs text-black font-bold uppercase mb-1">Tipe</p>
                        <p class="text-lg text-gray-800 uppercase">{{ $product->category }}</p>
                    </div>

                    {{-- SUMBER --}}
                    <div class="mb-4">
                        <p class="text-xs text-black font-bold uppercase mb-1">Sumber</p>
                        <p class="text-lg text-gray-800 uppercase">{{ $product->source_label }}</p>
                    </div>

                    {{-- DESKRIPSI --}}
                    <div class="mb-4">
                        <p class="text-xs text-black font-bold uppercase mb-1">Deskripsi Produk</p>
                        <p class="text-sm text-gray-600 italic whitespace-pre-line text-left">{{ trim($product->description ?? 'Tidak ada deskripsi') }}</p>
                    </div>

                    {{-- HARGA JUAL --}}
                    <div class="mb-4">
                        <p class="text-xs text-black font-bold uppercase mb-1">Harga Jual (Rp)</p>
                        <p class="text-lg text-gray-800">
                            Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                        </p>
                    </div>

                    {{-- FORMULA --}}
                    <div class="mb-4">
                        <p class="text-xs text-black font-bold uppercase mb-1">Resep</p>
                        <p class="text-lg text-gray-800">
                            {{ $product->formula->formula_name ?? '-- Tanpa Formula --' }}
                        </p>
                    </div>
                </div>

            </div>

            <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex justify-between text-xs text-gray-400">
                <span>Dibuat pada: {{ $product->created_at->format('d M Y H:i') }}</span>
                <span>Terakhir diperbarui: {{ $product->updated_at->format('d M Y H:i') }}</span>
            </div>
        </div>

        {{-- LIGHTBOX --}}
        <div x-show="lightboxOpen"
             class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-95"
             style="display: none;"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @keydown.escape.window="closeLightbox()"
             @keydown.arrow-right.window="nextImage()"
             @keydown.arrow-left.window="prevImage()">

            {{-- Zone klik kiri (prev) --}}
            <div x-show="savedMediaUrls.length > 1"
                 class="absolute left-0 top-0 bottom-0 w-1/3 cursor-pointer z-10"
                 @click="prevImage()"></div>

            {{-- Zone klik kanan (next) --}}
            <div x-show="savedMediaUrls.length > 1"
                 class="absolute right-0 top-0 bottom-0 w-1/3 cursor-pointer z-10"
                 @click="nextImage()"></div>

            {{-- Tombol tutup --}}
            <button @click="closeLightbox()"
                    class="absolute top-6 right-6 text-white hover:text-orange-400 p-2 z-30 transition-colors">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            {{-- Tombol Prev --}}
            <button x-show="savedMediaUrls.length > 1"
                    class="absolute left-4 md:left-10 text-white hover:text-orange-400 p-3 bg-black bg-opacity-50 rounded-full transition-colors z-20 pointer-events-none">
                <svg class="w-8 h-8 md:w-12 md:h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            {{-- Gambar --}}
            <div class="relative flex justify-center items-center w-full h-full px-16 z-20 pointer-events-none">
                <template x-if="savedMediaUrls[currentImageIndex]">
                    <img :src="savedMediaUrls[currentImageIndex]"
                         class="max-h-[85vh] max-w-[85vw] object-contain shadow-2xl rounded pointer-events-auto"
                         @click.stop>
                </template>
            </div>

            {{-- Counter --}}
            <div x-show="savedMediaUrls.length > 1"
                 class="absolute bottom-8 text-white text-sm font-medium tracking-widest bg-black bg-opacity-50 px-6 py-2 rounded-full z-20 pointer-events-none">
                <span x-text="currentImageIndex + 1"></span> / <span x-text="savedMediaUrls.length"></span>
            </div>

            {{-- Tombol Next --}}
            <button x-show="savedMediaUrls.length > 1"
                    class="absolute right-4 md:right-10 text-white hover:text-orange-400 p-3 bg-black bg-opacity-50 rounded-full transition-colors z-20 pointer-events-none">
                <svg class="w-8 h-8 md:w-12 md:h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

        </div>
    </div>

    @push('scripts')
    <script>
        // JS-nya dibikin super simple karena cuma buat handle buka-tutup Lightbox foto
        function productViewForm() {
            const existingUrls = @json($product->media->pluck('url'));

            return {
                lightboxOpen: false,
                currentImageIndex: 0,
                savedMediaUrls: existingUrls,

                openLightbox(index) {
                    if (this.savedMediaUrls.length === 0) return;
                    this.currentImageIndex = index;
                    this.lightboxOpen = true;
                    document.body.style.overflow = 'hidden';
                },

                closeLightbox() {
                    this.lightboxOpen = false;
                    document.body.style.overflow = '';
                },

                nextImage() {
                    this.currentImageIndex = (this.currentImageIndex + 1) % this.savedMediaUrls.length;
                },

                prevImage() {
                    this.currentImageIndex = this.currentImageIndex > 0
                        ? this.currentImageIndex - 1
                        : this.savedMediaUrls.length - 1;
                }
            }
        }
    </script>
    @endpush
</x-owner-app-layout>