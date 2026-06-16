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
                                <img src="{{ $product->image_url }}" @click="previewImage='{{ $product->image_url }}'"
                                    class="max-h-72 object-contain cursor-pointer">
                            </div>

                            {{-- gallery --}}
                            @if($product->media->count())
                                <div class="grid grid-cols-4 gap-2 mt-3">

                                    @foreach($product->media as $media)

                                        <div class="relative">

                                            <img src="{{ $media->url }}" @click="previewImage='{{ $media->url }}'"
                                                class="h-20 w-full object-cover rounded border cursor-pointer hover:opacity-80">

                                            <button type="button" onclick="deleteMedia({{ $media->id }})"
                                                class="absolute top-1 right-1 bg-red-500 text-white px-2 py-1 rounded text-xs">

                                                X

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
                                value="{{ $product->product_code }}"
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
                            <p class="text-xs text-black font-bold uppercase mb-1">Ganti Foto Produk</p>
                            <input type="file" name="images[]" multiple accept=".jpg,.jpeg,.png,.webp"
                                class="w-full border-gray-300 rounded p-1 text-black">
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
                            <p x-show="!editMode" class="text-lg text-gray-800 uppercase">{{ $product->source }}</p>
                            <select x-show="editMode" name="source" class="w-full border-gray-300 rounded text-black">
                                <option value="purchase" {{ $product->source == 'purchase' ? 'selected' : '' }}>
                                    Pembelian</option>
                                <option value="production" {{ $product->source == 'production' ? 'selected' : '' }}>
                                    Produksi
                                </option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Deskripsi Produk</p>
                            <p x-show="!editMode" class="text-sm text-gray-600 italic whitespace-pre-wrap">
                                {{ $product->description ?? 'Tidak ada deskripsi' }}
                            </p>
                            <textarea x-show="editMode" name="description" rows="3"
                                class="w-full border-gray-300 rounded focus:ring-orange-500 text-black">{{ $product->description }}</textarea>
                        </div>

                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Harga Jual (Rp)</p>
                            <p x-show="!editMode" class="text-lg text-gray-800">Rp
                                {{ number_format($product->selling_price, 0, ',', '.') }}
                            </p>
                            <input x-show="editMode" type="number" name="selling_price"
                                value="{{ $product->selling_price }}" class="w-full border-gray-300 rounded text-black">
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

            <img :src="previewImage" class="max-w-[90vw] max-h-[90vh] object-contain">

        </div>
    </div>
    <script>
        function deleteMedia(id) {

            if (!confirm('Hapus gambar ini?')) {
                return;
            }

            const form = document.getElementById('delete-media-form');

            form.action = `/admin/products/media/${id}`;

            form.submit();
        }
    </script>
</x-admin-app-layout>