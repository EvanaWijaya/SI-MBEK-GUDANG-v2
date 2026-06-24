<x-admin-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">{{ __('Tambah Material Pakan Baru') }}</h2>
    </x-slot>

    <div class="container mx-auto mt-10 px-4">
        
        {{-- Kotak Pesan Error Global di Bagian Atas --}}
        @if ($errors->any())
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative">
                <span class="font-bold block mb-1">Terjadi kesalahan input data:</span>
                <ul class="list-disc pl-5 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.materials.store') }}" method="POST" novalidate class="bg-white shadow-md rounded-xl p-8 border border-gray-200">
            @csrf
            
            {{-- Kategori Otomatis diset "pakan" secara tersembunyi --}}
            <input type="hidden" name="category" value="feed">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Kolom Kiri --}}
                <div>
                    {{-- Input Nama Bahan --}}
                    <label class="block font-bold mb-2 {{ $errors->has('material_name') ? 'text-red-600' : 'text-gray-700' }}">Nama Bahan</label>
                    <input type="text" name="material_name" value="{{ old('material_name') }}" 
                        class="w-full border {{ $errors->has('material_name') ? 'border-red-400 bg-red-50 text-red-900 focus:ring-red-500 focus:border-red-400' : 'border-gray-300 focus:ring-orange-500 focus:border-brand-orange' }} rounded-lg p-2.5 text-sm transition-all" 
                        placeholder="Contoh: Jagung Giling" required>
                    @error('material_name')
                        <p class="mt-1 text-xs text-red-500 font-semibold">{{ $message }}</p>
                    @enderror
                    
                    {{-- Input Satuan --}}
                    <label class="block font-bold mt-4 mb-2 {{ $errors->has('unit') ? 'text-red-600' : 'text-gray-700' }}">Unit</label>
                    <input type="text" name="unit" value="{{ old('unit') }}" 
                        class="w-full border {{ $errors->has('unit') ? 'border-red-400 bg-red-50 text-red-900 focus:ring-red-500 focus:border-red-400' : 'border-gray-300 focus:ring-orange-500 focus:border-brand-orange' }} rounded-lg p-2.5 text-sm transition-all" 
                        placeholder="kg, karung, liter" required>
                    @error('unit')
                        <p class="mt-1 text-xs text-red-500 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kolom Kanan --}}
                <div>
                    {{-- Input Deskripsi --}}
                    <label class="block font-bold mb-2 {{ $errors->has('description') ? 'text-red-600' : 'text-gray-700' }}">Deskripsi</label>
                    <textarea name="description" 
                        class="w-full border {{ $errors->has('description') ? 'border-red-400 bg-red-50 text-red-900 focus:ring-red-500 focus:border-red-400' : 'border-gray-300 focus:ring-orange-500 focus:border-brand-orange' }} rounded-lg p-2.5 text-sm transition-all resize-none" 
                        rows="5" placeholder="Keterangan bahan...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-500 font-semibold">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            {{-- Tombol Aksi Bawah --}}
            <div class="mt-8 pt-4 border-t border-gray-100 flex items-center">
                <button type="submit" class="bg-brand-orange text-white px-6 py-2.5 rounded-lg font-bold hover:bg-orange-700 shadow transition-colors">Simpan Material Pakan</button>
                <a href="{{ route('admin.materials.index') }}" class="ml-4 text-gray-600 hover:text-gray-900 font-medium transition-colors">Batal</a>
            </div>
        </form>
    </div>
</x-admin-app-layout>