<x-admin-app-layout>
    <div class="p-6 max-w-5xl mx-auto">

        {{-- Header --}}
        <div class="flex items-center gap-3 mb-8">
            <a href="{{ route('admin.purchase-orders.index') }}"
                class="p-2 rounded-lg hover:bg-gray-100 text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Buat Pemesanan Bahan</h1>
                <p class="text-sm text-gray-500 mt-0.5">Isi detail pemesanan bahan baku atau produk</p>
            </div>
        </div>

        {{-- Kotak Notifikasi Kesalahan Global --}}
        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <p class="text-sm font-semibold text-red-700 mb-2">Terdapat kesalahan input data:</p>
                <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.purchase-orders.store') }}" method="POST" novalidate id="purchaseOrder-form">
            @csrf

            <div class="space-y-6">

                {{-- 1. INFORMASI UMUM --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h2 class="text-base font-semibold text-gray-700 mb-5 flex items-center gap-2">
                        <span
                            class="w-6 h-6 bg-orange-100 text-orange-600 rounded-md flex items-center justify-center text-xs font-bold">1</span>
                        Informasi Umum
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                        {{-- Supplier --}}
                        <div>
                            <label
                                class="block text-sm font-medium mb-1.5 {{ $errors->has('supplier_id') ? 'text-red-600 font-bold' : 'text-gray-700' }}">Supplier
                                <span class="text-red-500">*</span></label>
                            <select name="supplier_id" required
                                class="w-full border {{ $errors->has('supplier_id') ? 'border-red-400 bg-red-50 focus:ring-red-500' : 'border-gray-300 focus:ring-orange-400' }} rounded-lg px-3 py-2.5 text-sm text-gray-800 focus:outline-none transition">
                                <option value="">-- Pilih Supplier --</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->supplier_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Tipe PO --}}
                        <div>
                            <label
                                class="block text-sm font-medium mb-1.5 {{ $errors->has('type') ? 'text-red-600' : 'text-gray-700' }}">Tipe
                                Pemesanan Bahan <span class="text-red-500">*</span></label>
                            <select name="type" id="purchaseOrder-type" required
                                class="w-full border {{ $errors->has('type') ? 'border-red-400 bg-red-50 focus:ring-red-500' : 'border-gray-300 focus:ring-orange-400' }} rounded-lg px-3 py-2.5 text-sm text-gray-800 focus:outline-none transition">
                                <option value="">-- Pilih Tipe --</option>
                                <option value="material" {{ old('type') === 'material' ? 'selected' : '' }}>Material
                                    (Bahan Baku)</option>
                                <option value="product" {{ old('type') === 'product' ? 'selected' : '' }}>Produk Jadi
                                </option>
                            </select>
                        </div>

                        {{-- Tanggal Pesan --}}
                        <div>
                            <label
                                class="block text-sm font-medium mb-1.5 {{ $errors->has('order_date') ? 'text-red-600' : 'text-gray-700' }}">Tanggal
                                Pesan <span class="text-red-500">*</span></label>
                            <input type="date" name="order_date" value="{{ old('order_date', date('Y-m-d')) }}" required
                                class="w-full border {{ $errors->has('order_date') ? 'border-red-400 bg-red-50 focus:ring-red-500' : 'border-gray-300 focus:ring-orange-400' }} rounded-lg px-3 py-2.5 text-sm text-gray-800 focus:outline-none transition">
                        </div>

                        {{-- Dipesan Atas Nama --}}
                        @if(auth()->guard('admin')->check())
                            <div class="col-span-1 sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Dipesan Atas Nama <span
                                            class="text-red-500">*</span></label>
                                    <div class="flex gap-4 p-2.5 bg-gray-50 rounded-lg border border-gray-200">
                                        <label class="flex items-center gap-2 cursor-pointer group">
                                            <input type="radio" name="ordered_by_type" value="Admin"
                                                class="text-orange-500 focus:ring-orange-400" {{ old('ordered_by_type', 'Admin') === 'Admin' ? 'checked' : '' }}>
                                            <span class="text-sm text-gray-700 group-hover:text-gray-900">Admin
                                                (Saya)</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer group">
                                            <input type="radio" name="ordered_by_type" value="Owner"
                                                class="text-orange-500 focus:ring-orange-400" {{ old('ordered_by_type') === 'Owner' ? 'checked' : '' }}>
                                            <span class="text-sm text-gray-700 group-hover:text-gray-900">Owner</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Catatan --}}
                    <div class="mt-5">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Catatan</label>
                        <textarea name="notes" rows="3" placeholder="Tambahkan catatan opsional..."
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-orange-400 transition resize-none">{{ old('notes') }}</textarea>
                    </div>
                </div>

                {{-- 2. ITEM PO --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="text-base font-semibold text-gray-700 flex items-center gap-2">
                            <span
                                class="w-6 h-6 bg-orange-100 text-orange-600 rounded-md flex items-center justify-center text-xs font-bold">2</span>
                            Bahan Pesanan
                        </h2>
                        <button type="button" id="add-item"
                            class="inline-flex items-center gap-1.5 text-sm font-semibold text-orange-600 hover:text-orange-800 border border-orange-300 hover:border-orange-500 px-3 py-1.5 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah Bahan
                        </button>
                    </div>

                    <div id="items-container" class="space-y-3">
                        {{-- Logika Penahan Data Lama Saat Gagal Validasi --}}
                        @if(old('items'))
                            @foreach(old('items') as $index => $oldItem)
                                @php $purchaseOrderType = old('type'); @endphp
                                <div class="item-row grid grid-cols-12 gap-3 items-start bg-gray-50 rounded-lg p-4 border {{ $errors->has("items.$index.*") ? 'border-red-400 bg-red-50/20' : 'border-gray-200' }}"
                                    data-index="{{ $index }}">
                                    <div class="col-span-12 sm:col-span-5">
                                        <label
                                            class="block text-xs font-medium mb-1 {{ $errors->has("items.$index.material_id") || $errors->has("items.$index.product_id") ? 'text-red-600 font-bold' : 'text-gray-500' }}">
                                            {{ $purchaseOrderType === 'material' ? 'Bahan / Material' : 'Produk Obat' }}
                                        </label>
                                        <select
                                            name="items[{{ $index }}][{{ $purchaseOrderType === 'material' ? 'material_id' : 'product_id' }}]"
                                            required onchange="updateUnit(this, {{ $index }}); updateDropdownOptions();"
                                            class="item-select w-full border {{ $errors->has("items.$index.material_id") || $errors->has("items.$index.product_id") ? 'border-red-400 bg-red-50' : 'border-gray-300' }} bg-white rounded-lg px-3 py-2 text-sm focus:outline-none">
                                            @if($purchaseOrderType === 'material')
                                                <option value="">-- Pilih Material --</option>
                                                @foreach($materials as $m)
                                                    <option value="{{ $m->id }}" {{ (isset($oldItem['material_id']) && $oldItem['material_id'] == $m->id) ? 'selected' : '' }}>{{ $m->material_name }}
                                                        ({{ $m->unit }})</option>
                                                @endforeach
                                            @else
                                                <option value="">-- Pilih Obat --</option>
                                                @foreach($products as $p)
                                                    <option value="{{ $p->id }}" {{ (isset($oldItem['product_id']) && $oldItem['product_id'] == $p->id) ? 'selected' : '' }}>{{ $p->product_name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-span-4 sm:col-span-2">
                                        <label
                                            class="block text-xs font-medium mb-1 {{ $errors->has("items.$index.quantity") ? 'text-red-600 font-bold' : 'text-gray-500' }}">Jumlah</label>
                                        <input type="number" name="items[{{ $index }}][quantity]"
                                            value="{{ $oldItem['quantity'] ?? '' }}" min="1" placeholder="0" required
                                            oninput="calcRow({{ $index }})"
                                            class="w-full border {{ $errors->has("items.$index.quantity") ? 'border-red-400 bg-red-50' : 'border-gray-300' }} bg-white rounded-lg px-3 py-2 text-sm">
                                    </div>
                                    <div class="col-span-8 sm:col-span-4">
                                        <label
                                            class="block text-xs font-medium mb-1 {{ $errors->has("items.$index.unit_price") ? 'text-red-600 font-bold' : 'text-gray-500' }}">Harga
                                            Satuan (Rp)</label>
                                        <input type="text" name="items[{{ $index }}][unit_price]"
                                            value="{{ isset($oldItem['unit_price']) ? 'Rp ' . number_format($oldItem['unit_price'], 0, ',', '.') : '' }}"
                                            placeholder="Rp 0" required oninput="formatRupiah(this); calcRow({{ $index }})"
                                            class="w-full border {{ $errors->has("items.$index.unit_price") ? 'border-red-400 bg-red-50' : 'border-gray-300' }} bg-white rounded-lg px-3 py-2 text-sm">
                                    </div>
                                    <div class="col-span-12 sm:col-span-1 flex items-end sm:pt-5">
                                        <button type="button" onclick="removeItem(this)"
                                            class="p-2 text-gray-400 hover:text-red-500">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="col-span-12">
                                        <div class="flex items-center justify-between text-xs text-gray-400">
                                            <span id="unit-label-{{ $index }}"></span>
                                            <span>Subtotal: <strong class="text-gray-700" id="subtotal-{{ $index }}">Rp
                                                    0</strong></span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    {{-- Total --}}
                    <div class="mt-5 pt-4 border-t border-gray-100 flex justify-end">
                        <div class="text-right">
                            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Total Estimasi</p>
                            <p class="text-2xl font-bold text-gray-800 mt-0.5" id="grand-total">Rp 0</p>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('admin.purchase-orders.index') }}"
                        class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 text-sm font-semibold text-white bg-orange-500 hover:bg-orange-600 rounded-lg shadow transition-colors">
                        Simpan Purchase Order
                    </button>
                </div>

            </div>
        </form>
    </div>

    @push('scripts')
        <script>

            const materials = @json($materials->map(fn($m) => ['id' => $m->id, 'material_name' => $m->material_name, 'unit' => $m->unit]));
            const products = @json($products->map(fn($p) => ['id' => $p->id, 'product_name' => $p->product_name, 'type' => $p->category]));

            // Set indeks awal dinamis mendeteksi keberadaan old data
            let itemIndex = {{ old('items') ? count(old('items')) : 0 }};

            document.getElementById('purchaseOrder-type').addEventListener('change', function () {
                document.getElementById('items-container').innerHTML = '';
                itemIndex = 0;
                addItem();
            });

            document.getElementById('add-item').addEventListener('click', addItem);

            function addItem() {
                const container = document.getElementById('items-container');
                const purchaseOrderType = document.getElementById('purchaseOrder-type').value;

                if (!purchaseOrderType) {
                    alert('Silakan pilih Tipe PO terlebih dahulu');
                    return;
                }

                const idx = itemIndex++;
                const row = document.createElement('div');
                row.className = 'item-row grid grid-cols-12 gap-3 items-start bg-gray-50 rounded-lg p-4 border border-gray-200';
                row.dataset.index = idx;

                let options = '';
                let selectName = (purchaseOrderType === 'material') ? `items[${idx}][material_id]` : `items[${idx}][product_id]`;

                if (purchaseOrderType === 'material') {
                    options = `<option value="">-- Pilih Material --</option>` +
                        materials.map(m => `<option value="${m.id}">${m.material_name} (${m.unit})</option>`).join('');
                } else {
                    options = `<option value="">-- Pilih Obat --</option>` +
                        products.map(p => `<option value="${p.id}">${p.product_name}</option>`).join('');
                }

                row.innerHTML = `
                            <div class="col-span-12 sm:col-span-5">
                                <label class="block text-xs font-medium text-gray-500 mb-1">
                                    ${purchaseOrderType === 'material' ? 'Bahan / Material' : 'Produk Obat'}
                                </label>
                                <select name="${selectName}" required onchange="updateUnit(this, ${idx}); updateDropdownOptions();"
                                    class="item-select w-full border border-gray-300 bg-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                                    ${options}
                                </select>
                            </div>
                            <div class="col-span-4 sm:col-span-2">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Jumlah</label>
                                <input type="number" name="items[${idx}][quantity]" min="1" placeholder="0" required oninput="calcRow(${idx})"
                                    class="w-full border border-gray-300 bg-white rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div class="col-span-8 sm:col-span-4">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Harga Satuan (Rp)</label>
                                <input type="text" name="items[${idx}][unit_price]" placeholder="Rp 0" required oninput="formatRupiah(this); calcRow(${idx})"
                                    class="w-full border border-gray-300 bg-white rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div class="col-span-12 sm:col-span-1 flex items-end sm:pt-5">
                                <button type="button" onclick="removeItem(this)" class="p-2 text-gray-400 hover:text-red-500">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                            <div class="col-span-12">
                                <div class="flex items-center justify-between text-xs text-gray-400">
                                    <span id="unit-label-${idx}"></span>
                                    <span>Subtotal: <strong class="text-gray-700" id="subtotal-${idx}">Rp 0</strong></span>
                                </div>
                            </div>
                        `;
                container.appendChild(row);
                updateDropdownOptions();
            }

            function removeItem(btn) {
                const rows = document.querySelectorAll('.item-row');
                if (rows.length <= 1) {
                    alert('Minimal harus ada 1 item.');
                    return;
                }
                btn.closest('.item-row').remove();
                updateGrandTotal();
                updateDropdownOptions();
            }

            function updateUnit(select, idx) {
                const poType = document.getElementById('purchaseOrder-type').value;
                const label = document.getElementById(`unit-label-${idx}`);
                if (!select.value) { if (label) label.textContent = ''; return; }

                if (poType === 'material') {
                    const mat = materials.find(m => m.id == select.value);
                    if (label) label.textContent = mat ? `Satuan: ${mat.unit}` : '';
                } else {
                    const prod = products.find(p => p.id == select.value);
                    if (label) label.textContent = prod ? `Tipe: ${prod.type}` : '';
                }
            }

            function formatRupiah(input) {
                let angka = input.value.replace(/\D/g, '');
                if (!angka) { input.value = ''; return; }
                input.value = 'Rp ' + Number(angka).toLocaleString('id-ID');
            }

            function calcRow(idx) {
                const row = document.querySelector(`[data-index="${idx}"]`);
                if (!row) return;

                const quantity = parseFloat(row.querySelector(`[name*="[quantity]"]`).value) || 0;
                const priceInput = row.querySelector(`[name*="[unit_price]"]`).value;
                const price = parseFloat(priceInput.replace(/\D/g, '')) || 0;
                const subtotal = quantity * price;
                const el = document.getElementById(`subtotal-${idx}`);
                if (el) el.textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
                updateGrandTotal();
            }

            function updateGrandTotal() {
                let total = 0;
                document.querySelectorAll('.item-row').forEach(row => {
                    const idx = row.dataset.index;
                    const quantity = parseFloat(row.querySelector(`[name*="[quantity]"]`)?.value) || 0;
                    const priceInput = row.querySelector(`[name*="[unit_price]"]`)?.value || '';
                    const price = parseFloat(priceInput.replace(/\D/g, '')) || 0;
                    total += quantity * price;
                });
                document.getElementById('grand-total').textContent = 'Rp ' + total.toLocaleString('id-ID');
            }

            function updateDropdownOptions() {
                const allSelects = document.querySelectorAll('.item-select');
                const selectedValues = Array.from(allSelects).map(select => select.value).filter(v => v !== '');

                allSelects.forEach(select => {
                    const currentValue = select.value;
                    Array.from(select.options).forEach(option => {
                        if (option.value === '') return;
                        if (selectedValues.includes(option.value) && option.value !== currentValue) {
                            option.disabled = true;
                            option.classList.add('text-gray-300', 'bg-gray-100');
                        } else {
                            option.disabled = false;
                            option.classList.remove('text-gray-300', 'bg-gray-100');
                        }
                    });
                });
            }

            // Jalankan kalkulasi otomatis saat memuat ulang data lama (old data)
            if (itemIndex > 0) {
                window.addEventListener('DOMContentLoaded', () => {
                    document.querySelectorAll('.item-row').forEach(row => {
                        const idx = row.dataset.index;
                        const select = row.querySelector('.item-select');
                        updateUnit(select, idx);
                        calcRow(idx);
                    });
                    updateDropdownOptions();
                });
            }

            // Bersihkan format Rp saat form dikirim ke controller
            document.getElementById('purchaseOrder-form').addEventListener('submit', function () {
                document.querySelectorAll('input[name*="[unit_price]"]').forEach(input => {
                    input.value = input.value.replace(/\D/g, '');
                });
            });

            document.addEventListener('DOMContentLoaded', function () {

                const radios = document.querySelectorAll(
                    'input[name="ordered_by_type"]'
                );

                radios.forEach(radio => {
                    radio.addEventListener('change', toggleOwnerDropdown);
                });

                toggleOwnerDropdown();
            });
        </script>
    @endpush
</x-admin-app-layout>