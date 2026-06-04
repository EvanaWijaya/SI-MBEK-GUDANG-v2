<x-admin-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">{{ __('Admin - Master Data Admin') }}</h2>
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 mt-10">
        
        @if (session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        {{-- 🔥 TOMBOL SEKARANG LANGSUNG DIARAHKAN KE HALAMAN BARU 🔥 --}}
        <div class="mt-8 mb-6">
            <a href="{{ route('admin.admins.create') }}" 
                class="bg-brand-orange hover:bg-orange-700 px-4 py-2.5 rounded-md text-white font-bold shadow transition-colors inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Admin
            </a>
        </div>

        {{-- Filter & Search Data --}}
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

        {{-- Tabel Master Admin --}}
        <div class="flex flex-col">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-300 shadow-sm rounded-lg overflow-hidden border border-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Nama Lengkap</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Email</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Role</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse ($admins as $admin)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="whitespace-nowrap px-3 py-4 text-sm font-medium text-gray-900">{{ $admin->name }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $admin->email }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    <span class="px-2 py-1 rounded text-xs bg-blue-100 text-blue-800 uppercase font-semibold">
                                        {{ $admin->role }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm flex gap-2">
                                    <a href="{{ route('admin.admins.show', $admin->id) }}" class="bg-blue-600 text-white p-2 rounded hover:bg-blue-700 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('admin.admins.destroy', $admin->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus admin ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-600 text-white p-2 rounded hover:bg-red-700 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-6 text-gray-500">Belum ada admin lain yang terdaftar.</td>
                            </tr>
                        @endforelse
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
            
            let originalRows = Array.from(tbody.querySelectorAll('tr:not(#noDataRow)'));

            function checkEmptyState(visibleCount) {
                let noDataRow = document.getElementById('noDataRow');
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

            // Live Search
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

                checkEmptyState(visibleCount);
            });

            // Sorting
            sortSelect.addEventListener('change', function() {
                const sortBy = this.value;
                let rowsArray = Array.from(tbody.querySelectorAll('tr:not(#noDataRow)'));

                if (sortBy === 'terbaru') {
                    rowsArray = [...originalRows];
                } else if (sortBy === 'terlama') {
                    rowsArray = [...originalRows].reverse();
                } else if (sortBy === 'az' || sortBy === 'za') {
                    rowsArray.sort((a, b) => {
                        const textA = a.cells[0].textContent.trim().toLowerCase();
                        const textB = b.cells[0].textContent.trim().toLowerCase();
                        
                        if (sortBy === 'az') return textA.localeCompare(textB);
                        return textB.localeCompare(textA);
                    });
                }

                tbody.innerHTML = '';
                rowsArray.forEach(row => tbody.appendChild(row));
                searchInput.dispatchEvent(new Event('input'));
            });
        });
    </script>
</x-admin-app-layout>