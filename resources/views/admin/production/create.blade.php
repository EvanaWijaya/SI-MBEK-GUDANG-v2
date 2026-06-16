<x-admin-app-layout>
    <div class="p-6 max-w-2xl mx-auto">

        {{-- Header --}}
        <div class="flex items-center gap-3 mb-8">
            <a href="{{ route('admin.productions.index') }}"
                class="p-2 rounded-lg hover:bg-gray-100 text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Buat Produksi Baru</h1>
                <p class="text-sm text-gray-500 mt-0.5">Mulai proses produksi berdasarkan formula aktif</p>
            </div>
        </div>

        {{-- Kotak List Error Validasi Global --}}
        @if($errors->any())
            <div class="mb-6 flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm shadow-sm">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <span class="font-bold block mb-0.5">Terjadi kesalahan input produksi:</span>
                    <ul class="list-disc list-inside space-y-0.5 text-xs text-red-700">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">

            {{-- Info alur --}}
            <div class="mb-6 bg-orange-50 border border-orange-100 rounded-lg px-4 py-3 text-xs text-orange-700">
                <p class="font-semibold mb-1">Alur Produksi:</p>
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="inline-flex items-center gap-1 bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full font-medium">1. Buat Produksi</span>
                    <svg class="w-3 h-3 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    <span class="inline-flex items-center gap-1 bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-medium">2. Input QC</span>
                    <svg class="w-3 h-3 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    <span class="inline-flex items-center gap-1 bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium">3. Selesaikan</span>
                </div>
                <p class="mt-2 text-orange-600">Pastikan produk yang dipilih sudah di-assign ke formula tersebut.</p>
            </div>

            <form action="{{ route('admin.productions.store') }}" method="POST" novalidate>
                @csrf

                @php
                    // Variabel bumbu desainer Tailwind biar konsisten rapi merahnya
                    $inputBase  = 'w-full border rounded-lg px-3 py-2.5 text-sm focus:outline-none transition-all duration-200';
                    $inputOk    = 'border-gray-300 focus:ring-2 focus:ring-orange-400 focus:border-orange-400 text-black';
                    $inputError = 'border-red-400 bg-red-50 text-red-900 focus:ring-2 focus:ring-red-500 focus:border-red-400';
                    $labelOk    = 'block text-xs font-bold mb-1.5 text-gray-500 uppercase tracking-wide';
                    $labelError = 'block text-xs font-bold mb-1.5 text-red-600 uppercase tracking-wide';
                @endphp

                <div class="space-y-5">

                    {{-- Dropdown Formula --}}
                    <div>
                        <label class="{{ $errors->has('formula_id') ? $labelError : $labelOk }}">
                            Formula <span class="text-red-500">*</span>
                        </label>
                        <select name="formula_id" id="selectFormula" required onchange="loadProducts(this.value)"
                            class="{{ $inputBase }} {{ $errors->has('formula_id') ? $inputError : $inputOk }}">
                            <option value="">-- Pilih Formula --</option>
                            @foreach($formulas as $formula)
                                <option value="{{ $formula->id }}" {{ old('formula_id') == $formula->id ? 'selected' : '' }}>
                                    [{{ $formula->kode_formula }}] {{ $formula->nama_formula }}
                                </option>
                            @endforeach
                        </select>
                        @error('formula_id')
                            <p class="mt-1 text-xs text-red-500 font-semibold">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-400">Hanya formula aktif yang ditampilkan</p>
                    </div>

                    {{-- Dropdown Produk --}}
                    <div>
                        <label class="{{ $errors->has('product_id') ? $labelError : $labelOk }}">
                            Produk <span class="text-red-500">*</span>
                        </label>
                        <select name="product_id" id="selectProduct" required
                            class="{{ $inputBase }} {{ $errors->has('product_id') ? $inputError : $inputOk }}">
                            <option value="">-- Pilih formula dahulu --</option>
                        </select>
                        @error('product_id')
                            <p class="mt-1 text-xs text-red-500 font-semibold">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-400">
                            Produk yang muncul hanya yang memiliki <code class="bg-gray-100 px-1 rounded">formula_id</code> sesuai formula yang dipilih.
                        </p>
                    </div>

                    {{-- Input Quantity Produksi --}}
                    <div>
                        <label class="{{ $errors->has('production_quantity') ? $labelError : $labelOk }}">
                            Jumlah Produksi <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                           <input type="number" name="production_quantity" id="qtyProduksi" min="1" step="1" required
                                value="{{ old('production_quantity') }}" placeholder="0"
                                class="pl-3 pr-10 {{ $inputBase }} {{ $errors->has('production_quantity') ? $inputError : $inputOk }}">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-400">kg</span>
                        </div>
                        @error('production_quantity')
                            <p class="mt-1 text-xs text-red-500 font-semibold">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-400">Stok bahan baku akan dikurangi otomatis sesuai komposisi formula (FIFO)</p>
                    </div>

                    {{-- Preview Komposisi Realtime --}}
                    <div id="previewKomposisi" class="hidden">
                        <label class="block text-xs font-bold mb-1.5 text-gray-500 uppercase tracking-wide">Preview Kebutuhan Komposisi Bahan</label>
                        <div id="komposisiList" class="bg-gray-50 rounded-lg border border-gray-200 divide-y divide-gray-100 shadow-sm overflow-hidden"></div>
                    </div>

                </div>

                {{-- Tombol Aksi Bawah --}}
                <div class="mt-6 pt-5 border-t border-gray-100 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.productions.index') }}"
                        class="px-5 py-2.5 text-sm font-medium text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                        class="inline-flex items-center gap-2 bg-brand-orange hover:bg-orange-600 text-white font-semibold px-6 py-2.5 rounded-lg shadow text-sm transition-all active:scale-95">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Mulai Proses Produksi
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    const formulaProducts  = @json($formulaProducts ?? []);
    const formulaMaterials = @json($formulaMaterials ?? []);

    const qtyInput = document.getElementById('qtyProduksi');
    const selectFormula = document.getElementById('selectFormula');

    // 🔥 FIX UTAMA: Menambahkan parameter selectedProductId untuk menahan data lama (old value)
    function loadProducts(formulaId, selectedProductId = null) {
        const select   = document.getElementById('selectProduct');
        const preview  = document.getElementById('previewKomposisi');
        const products = formulaProducts[formulaId] || [];

        select.innerHTML = '';
        if (!formulaId) {
            select.innerHTML = '<option value="">-- Pilih formula dahulu --</option>';
            preview.classList.add('hidden');
            return;
        }
        if (products.length === 0) {
            select.innerHTML = '<option value="">-- Tidak ada produk untuk formula ini --</option>';
        } else {
            select.innerHTML = '<option value="">-- Pilih Produk --</option>';
            products.forEach(p => {
                // Beri tanda 'selected' jika ID produk cocok dengan data lama yang diinput user
                const isSelected = (selectedProductId == p.id) ? 'selected' : '';
                select.innerHTML += `<option value="${p.id}" ${isSelected}>[${p.kode}] ${p.nama}</option>`;
            });
        }

        // Tampilkan preview komposisi dan langsung hitung konversi Kg
        const materials = formulaMaterials[formulaId] || [];
        if (materials.length > 0) {
            preview.classList.remove('hidden');
            hitungKebutuhanKg(formulaId);
        } else {
            preview.classList.add('hidden');
        }
    }

    // Fungsi hitung Kg realtime komposisi bahan baku
    function hitungKebutuhanKg(formulaId = null) {
        if(!formulaId) formulaId = selectFormula.value;

        const list = document.getElementById('komposisiList');
        const materials = formulaMaterials[formulaId] || [];
        let totalProduksi = parseFloat(qtyInput.value) || 0; 

        if (materials.length > 0) {
            list.innerHTML = materials.map(m => {
                let hitungKg = (parseFloat(m.persentase) / 100) * totalProduksi;
                let tampilKg = Math.round(hitungKg * 100) / 100; 

                return `
                <div class="flex justify-between items-center px-4 py-2.5 text-xs bg-white hover:bg-gray-50/50 transition-colors">
                    <span class="text-gray-700 font-medium">${m.nama_bahan} <span class="text-gray-400 font-normal">(${m.persentase}%)</span></span>
                    <span class="font-bold text-orange-600">${tampilKg.toLocaleString('id-ID')} Kg</span>
                </div>`;
            }).join('');
        }
    }

    // Trigger hitung ulang pas ngetik angka jumlah produksi
    qtyInput.addEventListener('input', () => hitungKebutuhanKg());

    // 🔥 LOGIKA CERDAS: Mencegah hilangnya item yang dipilih saat gagal validasi (old value handling)
    const oldFormula = selectFormula.value;
    const oldProduct = "{{ old('product_id') }}";
    if (oldFormula) {
        loadProducts(oldFormula, oldProduct);
    }
    </script>
    @endpush
</x-admin-app-layout>