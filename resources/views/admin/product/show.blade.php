<x-admin-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-black leading-tight">
            Detail Produk: {{ $product->product_name }}
        </h2>
    </x-slot>

    <div class="container mx-auto mt-10 px-4" x-data="{
        editMode: false,
        previewImage: null
    }">
        <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
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
                            class="bg-white text-brand-orange px-4 py-1 rounded text-sm font-bold shadow hover:bg-orange-400">
                            Simpan Perubahan
                        </button>

                        <a href="{{ route('admin.products.index') }}" x-show="!editMode"
                            class="bg-white text-brand-orange px-4 py-1 rounded text-sm font-bold shadow hover:bg-white-100">
                            Kembali
                        </a>
                    </div>
                </div>

                {{-- Content Card --}}
                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div>
                        {{-- Foto View --}}
                        <div class="mb-6">

                            {{-- gambar utama --}}
                            <div class="flex justify-center bg-gray-50 p-4 rounded-lg">
                                @if($product->primaryImage)
                                    <img src="{{ asset('storage/' . $product->primaryImage->file_path) }}"
                                        @click="previewImage='{{ asset('storage/' . $product->primaryImage->file_path) }}'"
                                        class="w-full max-h-72 object-contain cursor-pointer">
                                @else
                                    <div class="h-72 flex items-center justify-center bg-gray-100 rounded-lg text-gray-400">
                                        Tidak ada gambar
                                    </div>
                                @endif
                            </div>

                            {{-- gallery --}}
                            @if($product->media->count())
                                <div class="grid grid-cols-4 gap-2 mt-3">

                                    @foreach($product->media as $media)

                                        <div class="relative">
                                            @if($media->is_primary)
                                                <span
                                                    class="absolute bottom-1 left-1 bg-green-600 text-white text-[10px] px-1 rounded">
                                                    Utama
                                                </span>
                                            @endif

                                            <img src="{{ asset('storage/' . $media->file_path) }}"
                                                @click="previewImage='{{ asset('storage/' . $media->file_path) }}'"
                                                class="h-20 w-full object-cover rounded border cursor-pointer hover:opacity-80">

                                            <button x-show="editMode" type="button" onclick="deleteMedia({{ $media->id }})"
                                                class="absolute top-1 right-1 bg-black/50 hover:bg-red-500 text-white w-6 h-6 flex items-center justify-center rounded-full transition">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>

                                        </div>

                                    @endforeach

                                </div>
                            @endif

                        </div>

                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Kode</p>
                            <p x-show="!editMode" class="text-lg text-gray-800 font-semibold">
                                {{ $product->product_code }}
                            </p>
                            <input x-show="editMode" type="text" name="product_code"
                                value="{{ $product->product_code }}" readonly
                                class="w-full border-gray-300 rounded focus:ring-orange-500 text-black">
                        </div>

                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Nama Produk</p>
                            <p x-show="!editMode" class="text-lg text-gray-800 font-semibold">
                                {{ $product->product_name }}
                            </p>
                            <input x-show="editMode" type="text" name="product_name"
                                value="{{ $product->product_name }}"
                                class="w-full border-gray-300 rounded focus:ring-orange-500 text-black">
                        </div>

                        <div class="mb-4" x-show="editMode">
                            <p class="text-xs text-black font-bold uppercase mb-1">
                                Tambah Gambar Produk
                            </p>

                            <input type="file" name="images[]" multiple accept=".jpg,.jpeg,.png,.webp"
                                class="w-full border-gray-300 rounded p-1 text-black">

                            {{-- ERROR VALIDASI DI SINI --}}
                            @error('images')
                                <p class="mt-1 text-xs text-red-500 font-semibold">
                                    {{ $message }}
                                </p>
                            @enderror

                            @error('images.*')
                                <p class="mt-1 text-xs text-red-500 font-semibold">
                                    {{ $message }}
                                </p>
                            @enderror

                            <p class="text-xs text-gray-500 mt-1">
                                Maksimal total 10 gambar (JPG, PNG, WEBP).
                            </p>
                        </div>
                    </div>

                    <div>
                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Stok Sistem Saat Ini</p>
                            <p class="text-3xl font-bold text-orange-600">{{ $product->stock }} <span
                                    class="text-sm text-gray-500">Unit</span></p>
                        </div>

                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Tipe</p>
                            <p x-show="!editMode" class="text-lg text-gray-800 uppercase">{{ $product->category }}</p>
                            <select x-show="editMode" name="category" class="w-full border-gray-300 rounded text-black">
                                <option value="pakan" {{ $product->category == 'pakan' ? 'selected' : '' }}>PAKAN</option>
                                <option value="obat" {{ $product->category == 'obat' ? 'selected' : '' }}>OBAT</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Sumber</p>
                            <p x-show="!editMode" class="text-lg text-gray-800 uppercase">{{ $product->source_label }}
                            </p>
                            <select x-show="editMode" name="source" class="w-full border-gray-300 rounded text-black">
                                <option value="purchase" {{ $product->source_label == 'purchase' ? 'selected' : '' }}>
                                    Pembelian</option>
                                <option value="production" {{ $product->source_label == 'production' ? 'selected' : '' }}>
                                    Produksi
                                </option>
                                <option value="production" {{ $product->source_label == 'manual_adjustment' ? 'selected' : '' }}>
                                    Penyesuaian Stok
                                </option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">
                                Deskripsi Produk
                            </p>

                            <p x-show="!editMode" style="text-align:left !important;"
                                class="text-sm text-gray-600 italic whitespace-pre-wrap w-full block">
                                {{ $product->description ?? 'Tidak ada deskripsi' }}
                            </p>

                            <textarea x-show="editMode" name="description" rows="3"
                                class="w-full border-gray-300 rounded focus:ring-orange-500 text-black">
                                {{ $product->description }}
                            </textarea>
                        </div>

                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Harga Jual (Rp)</p>
                            <p x-show="!editMode" class="text-lg text-gray-800">Rp
                                {{ number_format($product->selling_price, 0, ',', '.') }}
                            </p>
                            <input x-show="editMode" type="text" name="selling_price"
                                value="Rp {{ number_format($product->selling_price, 0, ',', '.') }}"
                                oninput="formatRupiah(this)" class="w-full border-gray-300 rounded text-black">
                        </div>

                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Resep / Formula</p>
                            <p x-show="!editMode" class="text-lg text-gray-800">
                                {{ $product->formula->formula_name ?? '-- Tanpa Formula --' }}
                            </p>
                            <select x-show="editMode" name="formula_id"
                                class="w-full border-gray-300 rounded text-black">
                                <option value="">-- Tanpa Formula --</option>
                                @foreach($formulas as $f)
                                    <option value="{{ $f->id }}" {{ $product->formula_id == $f->id ? 'selected' : '' }}>
                                        {{ $f->formula_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Footer Info --}}
                <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex justify-between text-xs text-gray-400">
                    <span>Dibuat pada: {{ $product->created_at->format('d M Y H:i') }}</span>
                    <span>Terakhir diperbarui: {{ $product->updated_at->format('d M Y H:i') }}</span>
                </div>
            </div>
        </form>
        <form id="delete-media-form" method="POST" style="display:none;">
            @csrf
            @method('DELETE')
        </form>
        <div x-show="previewImage" x-transition class="fixed inset-0 bg-black/80 flex items-center justify-center z-50"
            @click="previewImage = null" style="display:none">

            <button class="absolute top-5 right-5 text-white text-3xl font-bold" @click="previewImage = null">
                ×
            </button>

            <img :src="previewImage" class="max-w-[90vw] max-h-[90vh] object-contain">
        </div>
    </div>
    <script>
        function formatRupiah(input) {
            let angka = input.value.replace(/[^,\d]/g, '');

            let split = angka.split(',');
            let sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] !== undefined
                ? rupiah + ',' + split[1]
                : rupiah;

            input.value = rupiah ? 'Rp ' + rupiah : '';
        }

        function deleteMedia(id) {
            if (!confirm('Hapus gambar ini?')) return;
            <form
                id="delete-media-form"
                data-url="{{ url('/admin/products/media') }}"
                method="POST"
                style="display:none;">

                const form = document.getElementById('delete-media-form');
                form.action = `/admin/products/media/${id}`;
            form.submit();
        }
    </script>
</x-admin-app-layout>