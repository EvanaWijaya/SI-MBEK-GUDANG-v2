<x-admin-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-black leading-tight">
            Detail Produk: {{ $product->product_name }}
        </h2>
    </x-slot>

    <div class="container mx-auto mt-10 px-4" x-data="productEditForm()">
        <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" id="main-edit-form">
            @csrf
            @method('PUT')

            <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200">

                {{-- Header Card --}}
                <div class="p-6 bg-brand-orange text-white flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white">Informasi Master Produk</h3>
                    <div class="flex gap-2">
                        <button type="button" x-show="!editMode" @click="editMode = true"
                            class="bg-orange-300 text-white px-4 py-1 rounded text-sm font-bold shadow hover:bg-orange-400">
                            Edit Data
                        </button>
                        <button type="button" x-show="editMode" @click="editMode = false"
                            class="bg-orange-300 text-white px-4 py-1 rounded text-sm font-bold shadow hover:bg-orange-400">
                            Batal
                        </button>
                        <button type="submit" x-show="editMode"
                            class="bg-white text-brand-orange px-4 py-1 rounded text-sm font-bold shadow hover:bg-gray-100">
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('admin.products.index') }}" x-show="!editMode"
                            class="bg-white text-brand-orange px-4 py-1 rounded text-sm font-bold shadow hover:bg-gray-100">
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

                            {{-- MODE LIHAT --}}
                            <div x-show="!editMode">
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

                            {{-- MODE EDIT --}}
                            <div x-show="editMode" style="display: none;">
                                <p class="text-xs text-black font-bold uppercase mb-2">Kelola Foto Produk</p>

                                {{-- Gambar yang sudah ada --}}
                                @if($product->media->count())
                                    <p class="text-xs text-gray-500 mb-2">Foto yang sudah ada:</p>
                                    <div class="grid grid-cols-4 gap-2 mb-4">
                                        @foreach($product->media as $media)
                                            <div class="relative group">
                                                @if($media->is_primary)
                                                    <span class="absolute bottom-1 left-1 bg-green-600 text-white text-[10px] px-1 rounded z-10">
                                                        Utama
                                                    </span>
                                                @endif
                                                <img src="{{ $media->url }}"
                                                     class="h-20 w-full object-cover rounded border">
                                                <button type="button" onclick="deleteMedia({{ $media->id }})"
                                                    class="absolute top-1 right-1 bg-black/50 hover:bg-red-500 text-white w-6 h-6 flex items-center justify-center rounded-full transition opacity-0 group-hover:opacity-100">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Upload Foto Tambahan --}}
                                <p class="text-xs text-gray-500 mb-2">Upload foto tambahan:</p>
                                <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-dashed rounded-md"
                                     :class="isDragging ? 'border-orange-500 bg-orange-50' : 'border-gray-300'"
                                     @dragover.prevent="isDragging = true"
                                     @dragleave.prevent="isDragging = false"
                                     @drop.prevent="handleDrop($event)">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-10 w-10 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <div class="flex justify-center text-sm text-gray-600">
                                            <label class="cursor-pointer font-medium text-orange-600 hover:text-orange-500">
                                                <span>Pilih Gambar Tambahan</span>
                                                <input type="file" multiple accept=".jpg,.jpeg,.png,.webp" class="sr-only" @change="handleFileSelect">
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                {{-- Container hidden input (diisi syncHiddenInputs) --}}
                                <div id="hidden-inputs-container"></div>

                                {{-- Preview gambar baru --}}
                                <div x-show="newImages.length > 0" class="mt-4 grid grid-cols-4 gap-2" style="display: none;">
                                    <template x-for="(image, index) in newImages" :key="index">
                                        <div class="relative group">
                                            <img :src="image.url" class="h-20 w-full object-cover rounded border">
                                            <button type="button" @click="removeNewImage(index)"
                                                class="absolute top-1 right-1 bg-red-500 text-white w-5 h-5 rounded-full flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity">
                                                ×
                                            </button>
                                        </div>
                                    </template>
                                </div>

                                {{-- Pesan error validasi --}}
                                @error('images')
                                    <p class="mt-1 text-xs text-red-500 font-semibold">{{ $message }}</p>
                                @enderror
                                @error('images.*')
                                    <p class="mt-1 text-xs text-red-500 font-semibold">{{ $message }}</p>
                                @enderror

                                <p class="text-xs text-gray-400 mt-2">Maksimal total 10 gambar (JPG, PNG, WEBP, maks 2MB/file).</p>
                            </div>

                        </div>{{-- end Area Foto --}}

                        {{-- KODE PRODUK --}}
                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Kode</p>
                            <p x-show="!editMode" class="text-lg text-gray-800 font-semibold">{{ $product->product_code }}</p>
                            <input x-show="editMode" type="text" name="product_code"
                                value="{{ $product->product_code }}" readonly
                                class="w-full border-gray-300 rounded focus:ring-orange-500 text-black bg-gray-100 cursor-not-allowed">
                        </div>

                        {{-- NAMA PRODUK --}}
                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Nama Produk</p>
                            <p x-show="!editMode" class="text-lg text-gray-800 font-semibold">{{ $product->product_name }}</p>
                            <input x-show="editMode" type="text" name="product_name"
                                value="{{ $product->product_name }}"
                                class="w-full border-gray-300 rounded focus:ring-orange-500 text-black">
                        </div>

                    </div>{{-- end KOLOM KIRI --}}

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
                            <p x-show="!editMode" class="text-lg text-gray-800 uppercase">{{ $product->category_label }}</p>
                            <select x-show="editMode" name="category" class="w-full border-gray-300 rounded text-black">
                                <option value="feed"     {{ $product->category == 'feed'     ? 'selected' : '' }}>PAKAN</option>
                                <option value="medicine" {{ $product->category == 'medicine' ? 'selected' : '' }}>OBAT</option>
                            </select>
                        </div>

                        {{-- SUMBER --}}
                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Sumber</p>
                            <p x-show="!editMode" class="text-lg text-gray-800 uppercase">{{ $product->source_label }}</p>
                            <select x-show="editMode" name="source" class="w-full border-gray-300 rounded text-black">
                                <option value="purchase"   {{ $product->source == 'purchase'   ? 'selected' : '' }}>Pembelian</option>
                                <option value="production" {{ $product->source == 'production' ? 'selected' : '' }}>Produksi</option>
                            </select>
                        </div>

                        {{-- DESKRIPSI --}}
                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Deskripsi Produk</p>
                            <p x-show="!editMode"
                                 class="text-sm text-gray-600 italic whitespace-pre-line text-left">{{ trim($product->description ?? 'Tidak ada deskripsi') }}</p>
                            <textarea x-show="editMode" name="description" rows="3"
                                class="w-full border-gray-300 rounded focus:ring-orange-500 text-black">{{ $product->description }}</textarea>
                        </div>

                        {{-- HARGA JUAL --}}
                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Harga Jual (Rp)</p>
                            <p x-show="!editMode" class="text-lg text-gray-800">
                                Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                            </p>
                            <input x-show="editMode" type="text" name="selling_price"
                                value="Rp {{ number_format($product->selling_price, 0, ',', '.') }}"
                                oninput="formatRupiah(this)"
                                class="w-full border-gray-300 rounded text-black">
                        </div>

                        {{-- FORMULA --}}
                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Resep / Formula</p>
                            <p x-show="!editMode" class="text-lg text-gray-800">
                                {{ $product->formula->formula_name ?? '-- Tanpa Formula --' }}
                            </p>
                            <select x-show="editMode" name="formula_id" class="w-full border-gray-300 rounded text-black">
                                <option value="">-- Tanpa Formula --</option>
                                @foreach($formulas as $f)
                                    <option value="{{ $f->id }}" {{ $product->formula_id == $f->id ? 'selected' : '' }}>
                                        {{ $f->formula_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>{{-- end KOLOM KANAN --}}

                </div>{{-- end grid --}}

                <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex justify-between text-xs text-gray-400">
                    <span>Dibuat pada: {{ $product->created_at->format('d M Y H:i') }}</span>
                    <span>Terakhir diperbarui: {{ $product->updated_at->format('d M Y H:i') }}</span>
                </div>

            </div>{{-- end card --}}
        </form>

        {{-- Form DELETE media (di-submit via JS) --}}
        <form id="delete-media-form" method="POST" style="display:none;">
            @csrf
            @method('DELETE')
        </form>

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

        </div>{{-- end lightbox --}}

    </div>{{-- end x-data container --}}

    @push('scripts')
    <script>
        function formatRupiah(input) {
            let angka = input.value.replace(/[^,\d]/g, '');
            let split  = angka.split(',');
            let sisa   = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                rupiah += (sisa ? '.' : '') + ribuan.join('.');
            }

            rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
            input.value = rupiah ? 'Rp ' + rupiah : '';
        }

        function deleteMedia(id) {
            if (!confirm('Hapus gambar ini secara permanen?')) return;
            const form = document.getElementById('delete-media-form');
            form.action = `/admin/products/media/${id}`;
            form.submit();
        }

        function productEditForm() {
            const existingUrls = @json($product->media->pluck('url'));

            return {
                editMode: false,
                isDragging: false,
                newImages: [],
                lightboxOpen: false,
                currentImageIndex: 0,
                savedMediaUrls: existingUrls,

                handleFileSelect(event) {
                    this.addFiles(event.target.files);
                    event.target.value = '';
                },

                handleDrop(event) {
                    this.isDragging = false;
                    this.addFiles(event.dataTransfer.files);
                },

                addFiles(files) {
                    if (!files || files.length === 0) return;
                    Array.from(files).forEach(file => {
                        if (!file.type.match('image.*')) return;
                        if (file.size > 2 * 1024 * 1024) {
                            alert(`File ${file.name} terlalu besar (Maks 2MB)`);
                            return;
                        }
                        this.newImages.push({ file, url: URL.createObjectURL(file) });
                    });
                    this.syncHiddenInputs();
                },

                removeNewImage(index) {
                    URL.revokeObjectURL(this.newImages[index].url);
                    this.newImages.splice(index, 1);
                    this.syncHiddenInputs();
                },

                syncHiddenInputs() {
                    const container = document.getElementById('hidden-inputs-container');
                    container.innerHTML = '';
                    const dt = new DataTransfer();
                    this.newImages.forEach(img => dt.items.add(img.file));
                    if (dt.files.length > 0) {
                        const input = document.createElement('input');
                        input.type     = 'file';
                        input.name     = 'images[]';
                        input.multiple = true;
                        input.className = 'hidden';
                        input.files    = dt.files;
                        container.appendChild(input);
                    }
                },

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
</x-admin-app-layout>