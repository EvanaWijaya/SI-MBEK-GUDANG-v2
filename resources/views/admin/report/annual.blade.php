<x-admin-app-layout>

@push('styles')
<style>
    @media print {
        .no-print { display: none !important; }
        body { background: white !important; }
        .shadow-sm { box-shadow: none !important; }
    }
    .year-row:hover { background: #fafafa; }
</style>
@endpush

<div class="p-6 max-w-7xl mx-auto">

    @include('admin.report._nav', ['active' => 'annual'])

    {{-- ── Totals 5 Tahun ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-green-100 shadow-sm p-4">
            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide">Stok Masuk</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ number_format($totals['stok_masuk']) }}</p>
            <p class="text-xs text-gray-400 mt-0.5">unit (5 Thn)</p>
        </div>
        <div class="bg-white rounded-xl border border-red-100 shadow-sm p-4">
            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide">Stok Keluar</p>
            <p class="text-2xl font-bold text-red-500 mt-1">{{ number_format($totals['stok_keluar']) }}</p>
            <p class="text-xs text-gray-400 mt-0.5">unit (5 Thn)</p>
        </div>
        <div class="bg-white rounded-xl border border-blue-100 shadow-sm p-4">
            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide">Total Produksi</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($totals['total_produksi']) }}</p>
            <p class="text-xs text-gray-400 mt-0.5">unit (5 Thn)</p>
        </div>
        <div class="bg-white rounded-xl border border-purple-100 shadow-sm p-4">
            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide">Total Batch</p>
            <p class="text-2xl font-bold text-purple-600 mt-1">{{ number_format($totals['total_batch']) }}</p>
            <p class="text-xs text-gray-400 mt-0.5">batch (5 Thn)</p>
        </div>
        <div class="bg-white rounded-xl border border-orange-100 shadow-sm p-4">
            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide">Total Disposal</p>
            <p class="text-2xl font-bold text-orange-500 mt-1">{{ number_format($totals['total_disposal']) }}</p>
            <p class="text-xs text-gray-400 mt-0.5">kejadian (5 Thn)</p>
        </div>
    </div>

    {{-- ── Chart Tahunan ── --}}
    <div class="no-print grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Pergerakan Stok per Tahun</h3>
            <div class="h-56">
                <canvas id="stockYearChart"></canvas>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Produksi & Disposal per Tahun</h3>
            <div class="h-56">
                <canvas id="prodYearChart"></canvas>
            </div>
        </div>
    </div>

    {{-- ── Tabel Rekap Tahunan ── --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-700">Rincian per Tahun</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Tahun</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-green-600 uppercase tracking-wide">Stok Masuk</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-red-500 uppercase tracking-wide">Stok Keluar</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Net Stok</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-blue-600 uppercase tracking-wide">Produksi (unit)</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-purple-600 uppercase tracking-wide">Batch</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-orange-500 uppercase tracking-wide">Disposal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($years as $y)
                        @php
                            $isCurrentYear = $y['tahun'] == now()->year;
                            $net = $y['stok_masuk'] - $y['stok_keluar'];
                        @endphp
                        <tr class="year-row transition-colors {{ $isCurrentYear ? 'bg-orange-50/40' : '' }}">
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-gray-800">{{ $y['tahun'] }}</span>
                                    @if($isCurrentYear)
                                        <span class="text-xs bg-orange-100 text-orange-600 px-1.5 py-0.5 rounded-full font-medium">Tahun ini</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="{{ $y['stok_masuk'] > 0 ? 'font-bold text-green-600' : 'text-gray-300' }}">
                                    {{ $y['stok_masuk'] > 0 ? '+' . number_format($y['stok_masuk']) : '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="{{ $y['stok_keluar'] > 0 ? 'font-bold text-red-500' : 'text-gray-300' }}">
                                    {{ $y['stok_keluar'] > 0 ? '-' . number_format($y['stok_keluar']) : '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                @if($y['stok_masuk'] > 0 || $y['stok_keluar'] > 0)
                                    <span class="font-semibold {{ $net >= 0 ? 'text-gray-700' : 'text-red-600' }}">
                                        {{ ($net >= 0 ? '+' : '') . number_format($net) }}
                                    </span>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="{{ $y['total_produksi'] > 0 ? 'font-bold text-blue-600' : 'text-gray-300' }}">
                                    {{ $y['total_produksi'] > 0 ? number_format($y['total_produksi']) : '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-center text-gray-600">
                                {{ $y['total_batch'] > 0 ? $y['total_batch'] : '—' }}
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="{{ $y['total_disposal'] > 0 ? 'font-bold text-orange-500' : 'text-gray-300' }}">
                                    {{ $y['total_disposal'] > 0 ? $y['total_disposal'] . 'x' : '—' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Balik urutan untuk grafik supaya tahun lama di kiri, terbaru di kanan
const chartYears = {!! $years->reverse()->values()->toJson() !!};
const labels = chartYears.map(y => y.tahun);

new Chart(document.getElementById('stockYearChart'), {
    type: 'bar',
    data: {
        labels,
        datasets: [
            {
                label: 'Masuk',
                data: chartYears.map(y => y.stok_masuk),
                backgroundColor: 'rgba(34,197,94,0.7)',
                borderRadius: 4,
            },
            {
                label: 'Keluar',
                data: chartYears.map(y => y.stok_keluar),
                backgroundColor: 'rgba(239,68,68,0.7)',
                borderRadius: 4,
            }
        ]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { position: 'top' } },
        scales: { y: { beginAtZero: true, grid: { color: '#f3f4f6' } }, x: { grid: { display: false } } }
    }
});

new Chart(document.getElementById('prodYearChart'), {
    type: 'line',
    data: {
        labels,
        datasets: [
            {
                type: 'bar',
                label: 'Produksi (unit)',
                data: chartYears.map(y => y.total_produksi),
                backgroundColor: 'rgba(59,130,246,0.7)',
                borderRadius: 4,
            },
            {
                type: 'line',
                label: 'Disposal',
                data: chartYears.map(y => y.total_disposal),
                borderColor: 'rgba(249,115,22,1)',
                backgroundColor: 'rgba(249,115,22,1)',
                borderWidth: 2,
                fill: false,
                tension: 0.3
            }
        ]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { position: 'top' } },
        scales: { y: { beginAtZero: true, grid: { color: '#f3f4f6' } }, x: { grid: { display: false } } }
    }
});
</script>
@endpush

</x-admin-app-layout>