<x-admin-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">{{ __('Tambah Produk Baru') }}</h2>
    </x-slot>

    <div class="container mx-auto mt-10 px-4">

        @if (session('error'))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg font-semibold shadow-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- Kotak Error Validation yang sudah ada --}}
        @if ($errors->any())
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                <span class="font-bold block mb-1">Terjadi kesalahan input data:</span>
                <ul class="list-disc pl-5 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" novalidate
            class="bg-white shadow-md rounded-lg p-8 border border-gray-200">
            @csrf

            @php
                $inputBase = 'w-full rounded border p-2.5 text-sm transition-all';
                $inputOk = 'border-gray-300 focus:ring-orange-500 focus:border-orange-500';
                $inputError = 'border-red-400 bg-red-50 text-red-900 focus:ring-red-500 focus:border-red-400';
                $labelOk = 'block font-bold mb-2 text-gray-700';
                $labelError = 'block font-bold mb-2 text-red-600';
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- ===== KOLOM KIRI ===== --}}
                <div>

                    {{-- Kode Produk --}}
                    <label class="{{ $errors->has('product_code') ? $labelError : $labelOk }}">
                        Kode Produk
                    </label>

                    <input type="text" id="product_code" name="product_code"
                        value="{{ old('product_code', $productCode ?? '') }}" readonly
                        class="{{ $inputBase }} bg-gray-100 cursor-not-allowed {{ $errors->has('product_code') ? $inputError : $inputOk }}">

                    @error('product_code')
                        <p class="mt-1 text-xs text-red-500 font-semibold">{{ $message }}</p>
                    @enderror

                    {{-- Nama Produk --}}
                    <label class="mt-4 {{ $errors->has('product_name') ? $labelError : $labelOk }}">Nama Produk</label>
                    <input type="text" name="product_name" value="{{ old('product_name') }}"
                        placeholder="Pakan Penggemukan A"
                        class="{{ $inputBase }} {{ $errors->has('product_name') ? $inputError : $inputOk }}">
                    @error('product_name')
                        <p class="mt-1 text-xs text-red-500 font-semibold">{{ $message }}</p>
                    @enderror

                    {{-- Sumber Produk --}}
                    <label class="mt-4 {{ $errors->has('source') ? $labelError : $labelOk }}">Sumber Produk</label>
                    <select name="source"
                        class="{{ $inputBase }} {{ $errors->has('source') ? $inputError : $inputOk }}">
                        <option value="production" {{ old('source') == 'production' ? 'selected' : '' }}>Hasil Produksi
                            Sendiri</option>
                        <option value="purchase" {{ old('source') == 'purchase' ? 'selected' : '' }}>Beli Jadi</option>
                    </select>
                    @error('source')
                        <p class="mt-1 text-xs text-red-500 font-semibold">{{ $message }}</p>
                    @enderror

                    {{-- Deskripsi --}}
                    <label class="mt-4 {{ $errors->has('description') ? $labelError : $labelOk }}">Deskripsi
                        Produk</label>
                    <textarea name="description" rows="3"
                        placeholder="Masukkan deskripsi produk, manfaat, atau cara penggunaan..."
                        class="{{ $inputBase }} {{ $errors->has('description') ? $inputError : $inputOk }}">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-500 font-semibold">{{ $message }}</p>
                    @enderror

                    {{-- Foto Produk --}}
                    <label class="mt-4 {{ $errors->has('images') ? $labelError : $labelOk }}">Foto Produk</label>
                    <input type="file" name="images[]" multiple accept=".jpg,.jpeg,.png,.webp"
                        class="w-full rounded border p-1 text-sm transition-all
                            {{ ($errors->has('images') || $errors->has('images.*')) ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                    <p class="text-xs text-gray-500 mt-1">Maksimal 2MB (JPG, PNG, WEBP)</p>
                    @error('images')
                        <p class="mt-1 text-xs text-red-500 font-semibold">{{ $message }}</p>
                    @enderror

                </div>

                {{-- ===== KOLOM KANAN ===== --}}
                <div>

                    {{-- Tipe --}}
                    <label class="{{ $errors->has('category') ? $labelError : $labelOk }}">Tipe</label>
                    <select id="category" name="category"
                        class="{{ $inputBase }} {{ $errors->has('category') ? $inputError : $inputOk }}">
                        <option value="pakan" {{ old('category') == 'pakan' ? 'selected' : '' }}>Pakan</option>
                        <option value="obat" {{ old('category') == 'obat' ? 'selected' : '' }}>Obat</option>
                    </select>
                    @error('category')
                        <p class="mt-1 text-xs text-red-500 font-semibold">{{ $message }}</p>
                    @enderror

                    {{-- Harga Jual --}}
                    <label class="mt-4 {{ $errors->has('selling_price') ? $labelError : $labelOk }}">Harga Jual</label>
                    <input type="text" name="selling_price"
                        value="{{ old('selling_price') ? 'Rp ' . number_format(old('selling_price'), 0, ',', '.') : '' }}"
                        placeholder="Rp 0" oninput="formatRupiah(this)"
                        class="{{ $inputBase }} {{ $errors->has('selling_price') ? $inputError : $inputOk }}">
                    @error('selling_price')
                        <p class="mt-1 text-xs text-red-500 font-semibold">{{ $message }}</p>
                    @enderror

                    {{-- Formula --}}
                    <label class="mt-4 {{ $errors->has('formula_id') ? $labelError : $labelOk }}">Pilih Formula (Jika
                        Hasil Produksi)</label>
                    <select name="formula_id"
                        class="{{ $inputBase }} {{ $errors->has('formula_id') ? $inputError : $inputOk }}">
                        <option value="">-- Tanpa Formula --</option>
                        @foreach($formulas as $f)
                            <option value="{{ $f->id }}" {{ old('formula_id') == $f->id ? 'selected' : '' }}>
                                {{ $f->formula_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('formula_id')
                        <p class="mt-1 text-xs text-red-500 font-semibold">{{ $message }}</p>
                    @enderror

                </div>
            </div>

            <div class="mt-8 pt-4 border-t border-gray-100">
                <button type="submit"
                    class="bg-brand-orange text-white px-6 py-2.5 rounded-lg font-bold hover:bg-orange-700 shadow transition-colors">
                    Simpan Produk
                </button>
                <a href="{{ route('admin.products.index') }}"
                    class="ml-4 text-gray-600 hover:text-gray-900 font-medium">Batal</a>
            </div>
        </form>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const categorySelect = document.getElementById('category');
            const productCodeInput = document.getElementById('product_code');

            categorySelect.addEventListener('change', function () {

                fetch(`/admin/products/generate-code/${this.value}`)
                    .then(response => response.json())
                    .then(data => {
                        productCodeInput.value = data.code;
                    })
                    .catch(error => {
                        console.error('Gagal mengambil kode produk:', error);
                    });

            });

        });
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
    </script>

</x-admin-app-layout>