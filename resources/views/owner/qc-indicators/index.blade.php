<x-owner-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">{{ __('Admin — Master Indikator QC') }}</h2>
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 mt-10">

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
                <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Toolbar: Search + Sort --}}
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-4 mt-2">
            <div class="w-full sm:w-1/3">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" id="searchInput"
                        class="pl-10 w-full border-gray-300 rounded-lg shadow-sm focus:ring-brand-orange focus:border-brand-orange text-sm"
                        placeholder="Cari nama indikator...">
                </div>
            </div>
            <div class="w-full sm:w-auto flex items-center gap-2 flex-wrap">
                <label class="text-sm text-gray-600 font-medium">Filter Tipe:</label>
                <select id="filterTipe" class="border-gray-300 rounded-lg shadow-sm focus:ring-brand-orange focus:border-brand-orange text-sm py-2">
                    <option value="">Semua Tipe</option>
                    <option value="kritis">Kritis</option>
                    <option value="non-kritis">Non-Kritis</option>
                </select>

                <label class="text-sm text-gray-600 font-medium ml-2">Urutkan:</label>
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
                <table class="min-w-full divide-y divide-gray-300" id="indicatorTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Nama Indikator</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Tipe</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($indicators as $indicator)
                        <tr data-tipe="{{ $indicator->is_critical ? 'kritis' : 'non-kritis' }}">
                            <td class="px-3 py-4 text-sm font-medium text-gray-900">{{ $indicator->name }}</td>
                            <td class="px-3 py-4 text-sm">
                                @if($indicator->is_critical)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500 flex-shrink-0"></span>
                                        Kritis
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-400 flex-shrink-0"></span>
                                        Non-Kritis
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 py-4 text-sm">
                                @if($indicator->is_active)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Aktif</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-3 py-4 text-sm flex gap-2">
                                {{-- Edit --}}
                                <a href="{{ route('owner.qc-indicators.show', $indicator->id) }}"
                                    class="bg-blue-600 text-white p-2 rounded hover:bg-blue-700" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr id="emptyState">
                            <td colspan="5" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center gap-2 text-gray-400">
                                    <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                    </svg>
                                    <p class="text-sm font-medium">Belum ada indikator pengecekan kualitas</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput  = document.getElementById('searchInput');
            const sortSelect   = document.getElementById('sortSelect');
            const filterTipe   = document.getElementById('filterTipe');
            const tbody        = document.querySelector('#indicatorTable tbody');
            const originalRows = Array.from(tbody.querySelectorAll('tr[data-tipe]'));

            function checkEmptyState(visibleCount) {
                let noDataRow = document.getElementById('noDataRow');
                const colCount = document.querySelector('#indicatorTable thead tr').children.length;
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

                // Sembunyikan empty state bawaan kalau ada pencarian
                const emptyState = document.getElementById('emptyState');
                if (emptyState) emptyState.style.display = 'none';
            }

            function applyFilters() {
                const searchTerm   = searchInput.value.toLowerCase();
                const tipeFilter   = filterTipe.value;
                const sortBy       = sortSelect.value;

                // Sort dulu
                let rowsArray = [...originalRows];
                if (sortBy === 'terlama') {
                    rowsArray = [...originalRows].reverse();
                } else if (sortBy === 'az' || sortBy === 'za') {
                    rowsArray.sort((a, b) => {
                        const textA = a.cells[0].textContent.trim().toLowerCase();
                        const textB = b.cells[0].textContent.trim().toLowerCase();
                        return sortBy === 'az'
                            ? textA.localeCompare(textB)
                            : textB.localeCompare(textA);
                    });
                } else if (sortBy === 'kritis') {
                    rowsArray.sort((a, b) => {
                        const aKritis = a.dataset.tipe === 'kritis' ? 0 : 1;
                        const bKritis = b.dataset.tipe === 'kritis' ? 0 : 1;
                        return aKritis - bKritis;
                    });
                }

                // Bersihkan tbody
                tbody.innerHTML = '';
                let visibleCount = 0;

                rowsArray.forEach(row => {
                    const text     = row.textContent.toLowerCase();
                    const rowTipe  = row.dataset.tipe;
                    const matchSearch = text.includes(searchTerm);
                    const matchTipe   = !tipeFilter || rowTipe === tipeFilter;

                    if (matchSearch && matchTipe) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                    tbody.appendChild(row);
                });

                checkEmptyState(visibleCount);
            }

            searchInput.addEventListener('input', applyFilters);
            sortSelect.addEventListener('change', applyFilters);
            filterTipe.addEventListener('change', applyFilters);
        });
    </script>
</x-owner-app-layout>