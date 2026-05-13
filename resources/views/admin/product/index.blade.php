<x-admin-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">{{ __('Admin - Master Produk') }}</h2>
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 mt-10">
        <button class="mt-8 bg-brand-orange hover:bg-orange-700 p-3 rounded-md mb-2 text-white">
            <a href="{{ route('admin.products.create') }}">Tambah Produk</a>
        </button>

        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-4 mt-2">
            <div class="w-full sm:w-1/3">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" id="searchInput" class="pl-10 w-full border-gray-300 rounded-lg shadow-sm focus:ring-brand-orange focus:border-brand-orange text-sm" placeholder="Cari nama...">
                </div>
            </div>
            <div class="w-full sm:w-auto flex items-center gap-2">
                <label class="text-sm text-gray-600 font-medium">Urutkan:</label>
                <select id="sortSelect" class="border-gray-300 rounded-lg shadow-sm focus:ring-brand-orange focus:border-brand-orange text-sm py-2">
                    <option value="terbaru">Data Terbaru</option>
                    <option value="terlama">Data Terlama</option>
                    <option value="az">Abjad (A - Z)</option>
                    <option value="za">Abjad (Z - A)</option>
                </select>
            </div>
        </div>

        <div class="flex flex-col">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Kode</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Nama Produk</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Tipe</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Sumber</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Harga Jual</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Aksi</th>
                        </tr>
                    </thead>
                   <tbody class="divide-y divide-gray-200 bg-white">
    @foreach ($products as $p)
        <tr>
            <td class="whitespace-nowrap px-3 py-4 text-sm font-medium text-gray-900">{{ $p->kode }}</td>
            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $p->nama }}</td>
            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 uppercase">{{ $p->type }}</td>
            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                <span class="px-2 py-1 rounded text-xs {{ $p->source == 'produksi' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                    {{ ucfirst($p->source) }}
                </span>
            </td>
            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">Rp {{ number_format($p->harga, 0, ',', '.') }}</td>
            <td class="whitespace-nowrap px-3 py-4 text-sm flex gap-2">
                <a href="{{ route('admin.products.update', $p->id) }}" class="bg-blue-600 text-white p-2 rounded hover:bg-blue-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </a>
                <form action="{{ route('admin.products.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Hapus produk ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 text-white p-2 rounded hover:bg-red-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
            </td>
        </tr>
    @endforeach
</tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchInput');
            const sortSelect = document.getElementById('sortSelect');
            const tbody = document.querySelector('table tbody');
            
            // Simpan urutan asli (kecuali baris "kosong" kalau udah keburu ada)
            let originalRows = Array.from(tbody.querySelectorAll('tr:not(#noDataRow)'));

            // Fungsi buat nampilin tulisan "Data tidak ditemukan"
            function checkEmptyState(visibleCount) {
                let noDataRow = document.getElementById('noDataRow');
                // Otomatis hitung jumlah kolom dari thead biar teksnya rata tengah sempurna
                const colCount = document.querySelector('thead tr').children.length; 

                if (visibleCount === 0) {
                    if (!noDataRow) {
                        noDataRow = document.createElement('tr');
                        noDataRow.id = 'noDataRow';
                        noDataRow.innerHTML = `<td colspan="${colCount}" class="px-4 py-8 text-center text-gray-500 italic">Data tidak ditemukan</td>`;
                        tbody.appendChild(noDataRow);
                    }
                    noDataRow.style.display = '';
                } else {
                    if (noDataRow) noDataRow.style.display = 'none';
                }
            }

            // 1. Logika Live Search / Pencarian
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const rows = tbody.querySelectorAll('tr:not(#noDataRow)');
                let visibleCount = 0;

                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Panggil fungsi cek kosong
                checkEmptyState(visibleCount);
            });

            // 2. Logika Sorting
            sortSelect.addEventListener('change', function() {
                const sortBy = this.value;
                let rowsArray = Array.from(tbody.querySelectorAll('tr:not(#noDataRow)'));

                if (sortBy === 'terbaru') {
                    rowsArray = [...originalRows]; // Balik ke urutan asli
                } else if (sortBy === 'terlama') {
                    rowsArray = [...originalRows].reverse(); // Dibalik dari bawah ke atas
                } else if (sortBy === 'az' || sortBy === 'za') {
                    rowsArray.sort((a, b) => {
                        // Ambil teks dari kolom pertama (Nama/Kode)
                        const textA = a.cells[0].textContent.trim().toLowerCase();
                        const textB = b.cells[0].textContent.trim().toLowerCase();
                        
                        if (sortBy === 'az') return textA.localeCompare(textB);
                        return textB.localeCompare(textA);
                    });
                }

                // Hapus isi tabel lama
                tbody.innerHTML = '';
                
                // Masukkan hasil sorting yang baru
                rowsArray.forEach(row => tbody.appendChild(row));
                
                // Trigger ulang pencarian biar nggak bentrok kalau lagi nge-search terus diganti sort-nya
                searchInput.dispatchEvent(new Event('input'));
            });
        });
    </script>
</x-admin-app-layout>