<x-owner-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-black leading-tight">
            Detail Produk: {{ $product->product_name }}
        </h2>
    </x-slot>

    <div class="container mx-auto mt-10 px-4" x-data="{ editMode: false }">
        <form action="{{ route('owner.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200">
                {{-- Header Card --}}
                <div class="p-6 bg-brand-orange text-white flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white">Informasi Master Produk</h3>
                    <div class="flex gap-2">
                        <a href="{{ route('owner.products.index') }}" x-show="!editMode"
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
                            <p class="text-sm text-gray-700">{{ $product->product_code }}</p>
                        </div>

                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Nama Produk</p>
                            <p class="text-sm text-gray-700">{{ $product->product_name }}</p>
                        </div>
                    </div>

                    <div>
                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Stok Sistem Saat Ini</p>
                            <p class="text-3xl font-bold text-orange-600">{{ $product->stock }} <span
                                    class="text-sm text-gray-500">Unit</span></p>
                        </div>

                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Kategori</p>
                            <p class="text-sm text-gray-700">{{ $product->category }}</p>
                        </div>

                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Sumber</p>
                            <p class="text-sm text-gray-700">
                                @if($product->source == 'production')
                                    Produksi
                                @elseif($product->source == 'purchase')
                                    Pembelian
                                @else
                                    {{ $product->source }}
                                @endif
                            </p>
                        </div>

                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Harga Jual (Rp)</p>
                            <p class="text-sm text-gray-700">
                                Rp{{ number_format($product->selling_price, 0, ',', '.') }}
                            </p>
                        </div>

                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">
                                Resep / Formula
                            </p>

                            <p class="text-sm text-gray-700">
                                {{ $product->formula?->formula_name ?? '-' }}
                            </p>
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
    </div>
</x-owner-app-layout>