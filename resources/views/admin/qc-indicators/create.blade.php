<x-admin-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">{{ __('Tambah Indikator QC Baru') }}</h2>
    </x-slot>

    <div class="container mx-auto mt-10 px-4">

        {{-- Error Box Global --}}
        @if($errors->any())
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative shadow-sm">
                <span class="font-bold block mb-1">Terjadi kesalahan input data:</span>
                <ul class="list-disc pl-5 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.qc-indicators.store') }}" method="POST" novalidate
            class="bg-white shadow-md rounded-xl p-8 border border-gray-200">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                {{-- KOLOM KIRI --}}
                <div class="space-y-6">
                    {{-- Nama Indikator --}}
                    <div>
                        <label class="block font-bold mb-2 {{ $errors->has('name') ? 'text-red-600' : 'text-gray-700' }}">
                            Nama Indikator <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}"
                            class="w-full border {{ $errors->has('name') ? 'border-red-400 bg-red-50 text-red-900 focus:ring-red-500 focus:border-red-400' : 'border-gray-300 focus:ring-orange-500 focus:border-brand-orange' }} rounded-lg p-2.5 text-sm transition-all"
                            placeholder="Contoh: Warna Produk, Aroma, Kadar Air">
                        @error('name')
                            <p class="mt-1 text-xs text-red-500 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Info Box --}}
                    <div class="bg-blue-50 border border-blue-100 rounded-lg px-4 py-3 text-xs text-blue-700">
                        <p class="font-semibold mb-1 flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Informasi
                        </p>
                        <ul class="space-y-1 list-disc pl-5 mt-2">
                            <li>Indikator baru akan otomatis berstatus <strong>Aktif</strong>.</li>
                            <li>Hanya indikator aktif yang muncul di formulir pengecekan kualitas produksi.</li>
                            <li>Status dapat diubah kapan saja dari halaman detail.</li>
                        </ul>
                    </div>
                </div>

                {{-- KOLOM KANAN --}}
                <div>
                    {{-- Tipe Indikator --}}
                    <div class="h-full">
                        <label class="block font-bold mb-2 {{ $errors->has('is_critical') ? 'text-red-600' : 'text-gray-700' }}">
                            Tipe Indikator <span class="text-red-500">*</span>
                        </label>
                        
                        <div class="space-y-3">
                            {{-- Opsi Kritis --}}
                            <label class="flex items-start gap-3 p-4 border rounded-lg cursor-pointer transition-all
                                {{ old('is_critical') === '1' ? 'border-red-400 bg-red-50' : 'border-gray-200 hover:bg-gray-50' }}">
                                <input type="radio" name="is_critical" value="1" class="mt-0.5 accent-red-600"
                                    {{ old('is_critical') === '1' ? 'checked' : '' }}>
                                <div>
                                    <p class="text-sm font-bold text-red-700">Kritis</p>
                                    <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                                        Indikator ini wajib lulus. Gagal pada indikator kritis langsung mengakibatkan pengecekan kualiatas <strong>Tidak Layak</strong>, tanpa menghitung akumulasi.
                                    </p>
                                </div>
                            </label>

                            {{-- Opsi Non-Kritis --}}
                            <label class="flex items-start gap-3 p-4 border rounded-lg cursor-pointer transition-all
                                {{ old('is_critical') === '0' ? 'border-yellow-400 bg-yellow-50' : 'border-gray-200 hover:bg-gray-50' }}">
                                <input type="radio" name="is_critical" value="0" class="mt-0.5 accent-yellow-500"
                                    {{ old('is_critical') === '0' ? 'checked' : '' }}>
                                <div>
                                    <p class="text-sm font-bold text-yellow-700">Non-Kritis</p>
                                    <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                                        Indikator ini dihitung secara akumulatif. Persentase kelulusan dihitung dari semua indikator non-kritis yang lulus.
                                    </p>
                                </div>
                            </label>
                        </div>

                        @error('is_critical')
                            <p class="mt-1 text-xs text-red-500 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

            </div>

            {{-- Tombol Aksi --}}
            <div class="mt-8 pt-5 border-t border-gray-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.qc-indicators.index') }}"
                    class="px-5 py-2.5 text-sm font-medium text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit"
                    class="bg-brand-orange text-white px-6 py-2.5 rounded-lg text-sm font-bold hover:bg-orange-700 shadow transition-colors">
                    Simpan 
                </button>
            </div>
        </form>
    </div>

    <script>
        // Highlight opsi radio yang dipilih
        document.querySelectorAll('input[name="is_critical"]').forEach(radio => {
            radio.addEventListener('change', function () {
                document.querySelectorAll('input[name="is_critical"]').forEach(r => {
                    const label = r.closest('label');
                    if (r.checked) {
                        label.classList.remove('border-gray-200', 'hover:bg-gray-50');
                        if (r.value === '1') {
                            label.classList.add('border-red-400', 'bg-red-50');
                        } else {
                            label.classList.add('border-yellow-400', 'bg-yellow-50');
                        }
                    } else {
                        label.classList.remove('border-red-400', 'bg-red-50', 'border-yellow-400', 'bg-yellow-50');
                        label.classList.add('border-gray-200', 'hover:bg-gray-50');
                    }
                });
            });
        });
    </script>
</x-admin-app-layout>