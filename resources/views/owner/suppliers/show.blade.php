<x-owner-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-black leading-tight">Detail Supplier: {{ $supplier->supplier_name }}</h2>
    </x-slot>

    <div class="container mx-auto mt-10 px-4" x-data="{ editMode: false }">
        <form action="{{ route('owner.suppliers.update', $supplier->id) }}" method="POST">
            @csrf @method('PUT')

            <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200">
                <div class="p-6 bg-brand-orange text-white flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white">Informasi Supplier</h3>
                    <div class="flex gap-2 text-black">
                        <a href="{{ route('owner.suppliers.index') }}" x-show="!editMode" class="bg-white text-brand-orange px-4 py-1 rounded text-sm font-bold shadow">Kembali</a>
                    </div>
                </div>
                
                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div>
                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Nama Supplier</p>
                            <p x-show="!editMode" class="text-lg text-gray-800 font-semibold">{{ $supplier->supplier_name }}</p>
                            <input x-show="editMode" type="text" name="supplier_name" value="{{ $supplier->supplier_name }}" class="w-full border-gray-300 rounded text-black">
                        </div>
                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Kontak</p>
                            <p x-show="!editMode" class="text-lg text-gray-800">{{ $supplier->contact ?? '-' }}</p>
                            <input x-show="editMode" type="text" name="contact" value="{{ $supplier->contact }}" class="w-full border-gray-300 rounded text-black">
                        </div>
                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Alamat</p>
                            <p x-show="!editMode" class="text-gray-700 italic">{{ $supplier->address ?? '-' }}</p>
                            <textarea x-show="editMode" name="address" class="w-full border-gray-300 rounded text-black">{{ $supplier->address }}</textarea>
                        </div>
                    </div>
                    <div>
                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Kota / Provinsi</p>
                            <p x-show="!editMode" class="text-lg text-gray-800">{{ $supplier->city }}, {{ $supplier->province }}</p>
                            <div x-show="editMode" class="flex gap-2">
                                <input type="text" name="city" value="{{ $supplier->city }}" class="w-1/2 border-gray-300 rounded text-black" placeholder="Kota">
                                <input type="text" name="province" value="{{ $supplier->province }}" class="w-1/2 border-gray-300 rounded text-black" placeholder="Provinsi">
                            </div>
                        </div>
                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Catatan</p>
                            <p x-show="!editMode" class="text-gray-700 italic">{{ $supplier->notes ?? '-' }}</p>
                            <textarea x-show="editMode" name="notes" class="w-full border-gray-300 rounded text-black">{{ $supplier->notes }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-owner-app-layout>