<x-admin-app-layout>
    <div class="p-6 max-w-6xl mx-auto">

        {{-- Header --}}
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('admin.inventory.product.index') }}"
                class="p-2 rounded-lg hover:bg-gray-100 text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-2xl font-bold text-gray-800">{{ $product->product_name }}</h1>
                    <span class="font-mono text-sm text-gray-400">{{ $product->product_code }}</span>
                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold
                        {{ $product->category === 'pakan' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                        {{ ucfirst($product->category) }}
                    </span>
                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold
                        {{ $product->source === 'produksi' ? 'bg-orange-100 text-orange-700' : 'bg-purple-100 text-purple-700' }}">
                        {{ ucfirst($product->source) }}
                    </span>
                    @if($product->isBelowReorderPoint())
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700 border border-red-200">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            Di Bawah ROP
                        </span>
                    @endif
                </div>
                <p class="text-sm text-gray-500 mt-0.5">Inventori detail · batch · alokasi · riwayat pergerakan stok</p>
            </div>
        </div>

        {{-- Shared vars untuk semua partials --}}
        @php
            $belowRop  = $product->isBelowReorderPoint();
            $qJual     = $allocations->where('type', 'sale')->first()?->quantity ?? 0;
            $qInternal = $allocations->where('type', 'internal')->first()?->quantity ?? 0;
            $activeTab = request('tab', 'overview');
        @endphp

        {{-- Tab Nav --}}
        <div class="flex gap-1 border-b border-gray-200 mb-6">
            @php
                $tabs = [
                    'overview'  => ['label' => '📦 Overview',          'icon' => null],
                    'alokasi'   => ['label' => '⚖ Alokasi & Adjustment', 'icon' => null],
                    'batches'   => ['label' => '📋 Batch & Riwayat',   'icon' => null],
                ];
            @endphp
            @foreach($tabs as $key => $tab)
                <a href="{{ request()->fullUrlWithQuery(['tab' => $key]) }}"
                    class="px-5 py-2.5 text-sm font-semibold rounded-t-lg border-b-2 -mb-px transition-colors
                        {{ $activeTab === $key
                            ? 'border-orange-500 text-orange-600 bg-white'
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    {{ $tab['label'] }}
                    @if($key === 'alokasi' && ($qJual + $qInternal) > $product->stock)
                        <span class="ml-1 inline-flex w-2 h-2 bg-red-500 rounded-full"></span>
                    @endif
                    @if($key === 'overview' && $belowRop)
                        <span class="ml-1 inline-flex w-2 h-2 bg-red-500 rounded-full"></span>
                    @endif
                </a>
            @endforeach
        </div>

        {{-- Tab Content --}}
        @if($activeTab === 'overview')
            @include('admin.inventory.product.partials._overview')
        @elseif($activeTab === 'alokasi')
            @include('admin.inventory.product.partials._alokasi')
        @elseif($activeTab === 'batches')
            @include('admin.inventory.product.partials._batches')
        @endif

    </div>
</x-admin-app-layout>