<x-admin-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-black leading-tight">{{ __('Tambah Supplier Baru') }}</h2>
    </x-slot>

    <div class="container mx-auto mt-10 px-4">
        
        {{-- Kotak Pesan Error Global di Bagian Atas --}}
        @if ($errors->any())
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-sm">
                <span class="font-bold block mb-1">Terjadi kesalahan input data:</span>
                <ul class="list-disc pl-5 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.suppliers.store') }}" method="POST" novalidate
              class="bg-white shadow-md rounded-lg p-8 border border-gray-200">
            @csrf

            @php
                // Template variabel class biar kodingan di bawah gak panjang dan rapi
                $inputBase  = 'w-full rounded border p-2.5 text-sm transition-all';
                $inputOk    = 'border-gray-300 focus:ring-orange-500 focus:border-orange-500 text-black';
                $inputError = 'border-red-400 bg-red-50 text-red-900 focus:ring-red-500 focus:border-red-400';
                $labelOk    = 'block font-bold mb-2 text-gray-700 text-sm';
                $labelError = 'block font-bold mb-2 text-red-600 text-sm';
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- ===== KOLOM KIRI ===== --}}
                <div>
                    {{-- Nama Supplier --}}
                    <label class="{{ $errors->has('nama_supplier') ? $labelError : $labelOk }}">Nama Supplier</label>
                    <input type="text" name="nama_supplier" value="{{ old('nama_supplier') }}"
                           placeholder="Masukkan nama supplier/PT"
                           class="{{ $inputBase }} {{ $errors->has('nama_supplier') ? $inputError : $inputOk }}" required>
                    @error('nama_supplier')
                        <p class="mt-1 text-xs text-red-500 font-semibold">{{ $message }}</p>
                    @enderror
                    
                    {{-- Kontak --}}
                    <label class="mt-4 {{ $errors->has('kontak') ? $labelError : $labelOk }}">Kontak (Telp/WA)</label>
                    <input type="text" name="kontak" value="{{ old('kontak') }}"
                           placeholder="08xxxxxxxxxx"
                           class="{{ $inputBase }} {{ $errors->has('kontak') ? $inputError : $inputOk }}">
                    @error('kontak')
                        <p class="mt-1 text-xs text-red-500 font-semibold">{{ $message }}</p>
                    @enderror

                    {{-- Alamat Lengkap --}}
                    <label class="mt-4 {{ $errors->has('alamat') ? $labelError : $labelOk }}">Alamat Lengkap</label>
                    <textarea name="alamat" rows="3" placeholder="Nama jalan, nomor gudang, dll..."
                              class="{{ $inputBase }} {{ $errors->has('alamat') ? $inputError : $inputOk }}">{{ old('alamat') }}</textarea>
                    @error('alamat')
                        <p class="mt-1 text-xs text-red-500 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                {{-- ===== KOLOM KANAN ===== --}}
                <div>
                    {{-- Kota --}}
                    <label class="{{ $errors->has('kota') ? $labelError : $labelOk }}">Kota</label>
                    <input type="text" name="kota" value="{{ old('kota') }}"
                           placeholder="Contoh: Bandar Lampung"
                           class="{{ $inputBase }} {{ $errors->has('kota') ? $inputError : $inputOk }}">
                    @error('kota')
                        <p class="mt-1 text-xs text-red-500 font-semibold">{{ $message }}</p>
                    @enderror
                    
                    {{-- Provinsi --}}
                    <label class="mt-4 {{ $errors->has('provinsi') ? $labelError : $labelOk }}">Provinsi</label>
                    <input type="text" name="provinsi" value="{{ old('provinsi') }}"
                           placeholder="Contoh: Lampung"
                           class="{{ $inputBase }} {{ $errors->has('provinsi') ? $inputError : $inputOk }}">
                    @error('provinsi')
                        <p class="mt-1 text-xs text-red-500 font-semibold">{{ $message }}</p>
                    @enderror

                    {{-- Catatan Tambahan --}}
                    <label class="mt-4 {{ $errors->has('catatan') ? $labelError : $labelOk }}">Catatan Tambahan</label>
                    <textarea name="catatan" rows="3" placeholder="Catatan jam operasional atau info sales..."
                              class="{{ $inputBase }} {{ $errors->has('catatan') ? $inputError : $inputOk }}">{{ old('catatan') }}</textarea>
                    @error('catatan')
                        <p class="mt-1 text-xs text-red-500 font-semibold">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="mt-8 pt-4 border-t border-gray-100 flex items-center">
                <button type="submit" class="bg-brand-orange text-white px-6 py-2.5 rounded-lg font-bold hover:bg-orange-700 shadow transition-colors text-sm">
                    Simpan Supplier
                </button>
                <a href="{{ route('admin.suppliers.index') }}" class="ml-4 text-gray-600 hover:text-gray-900 font-medium transition-colors text-sm">
                    Batal
                </a>
            </div>
        </form>
    </div>
</x-admin-app-layout>