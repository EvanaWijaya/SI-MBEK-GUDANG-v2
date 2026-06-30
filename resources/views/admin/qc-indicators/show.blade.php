<x-admin-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-black leading-tight">
            Detail Indikator QC: {{ $qcIndicator->name }}
        </h2>
    </x-slot>

    <div class="container mx-auto mt-10 px-4" x-data="{ editMode: false }">

        @if(session('success'))
            <div class="mb-6 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm font-semibold">
                <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-sm">
                <span class="font-bold block mb-1">Terjadi kesalahan:</span>
                <ul class="list-disc pl-5 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Buka edit mode otomatis jika ada error validasi --}}
        @if($errors->any())
            <script>
                document.addEventListener('alpine:init', () => {
                    Alpine.store('autoEdit', true);
                });
            </script>
        @endif

        <form action="{{ route('admin.qc-indicators.update', $qcIndicator->id) }}" method="POST"
            x-init="{{ $errors->any() ? 'editMode = true' : '' }}">
            @csrf
            @method('PUT')

            <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-200">

                {{-- Header Card --}}
                <div class="p-6 bg-brand-orange text-white flex justify-between items-center flex-wrap gap-3">
                    <h3 class="text-lg font-bold text-white">Informasi Indikator Pengecekan Kualitas</h3>
                    <div class="flex gap-2 flex-wrap">
                        {{-- Mode Lihat --}}
                        <button type="button" x-show="!editMode" @click="editMode = true"
                            class="bg-orange-300 text-white px-4 py-1.5 rounded text-sm font-bold shadow hover:bg-orange-400 transition-colors">
                            Edit Data
                        </button>
                        <a href="{{ route('admin.qc-indicators.index') }}" x-show="!editMode"
                            class="bg-white text-brand-orange px-4 py-1.5 rounded text-sm font-bold shadow hover:bg-gray-100 transition-colors">
                            Kembali
                        </a>

                        {{-- Mode Edit --}}
                        <button type="button" x-show="editMode" @click="editMode = false"
                            class="bg-orange-300 text-white px-4 py-1.5 rounded text-sm font-bold shadow hover:bg-orange-400 transition-colors">
                            Batal
                        </button>
                        <button type="submit" x-show="editMode"
                            class="bg-white text-brand-orange px-4 py-1.5 rounded text-sm font-bold shadow hover:bg-gray-100 transition-colors">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>

                {{-- Content --}}
                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8 text-sm">

                    {{-- Kolom Kiri: Nama & Tipe --}}
                    <div class="space-y-6">

                        {{-- Nama --}}
                        <div>
                            <p class="text-xs text-black font-bold uppercase mb-1">Nama Indikator</p>
                            {{-- View mode --}}
                            <p x-show="!editMode" class="text-lg text-gray-800 font-semibold">{{ $qcIndicator->name }}</p>
                            {{-- Edit mode --}}
                            <div x-show="editMode">
                                <input type="text" name="name" value="{{ old('name', $qcIndicator->name) }}"
                                    class="w-full border {{ $errors->has('name') ? 'border-red-400 bg-red-50 text-red-900' : 'border-gray-300' }} rounded-lg p-2.5 text-sm focus:ring-orange-500 focus:outline-none text-black">
                                @error('name')
                                    <p class="mt-1 text-xs text-red-500 font-semibold">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Tipe Indikator --}}
                        <div>
                            <p class="text-xs text-black font-bold uppercase mb-1">Tipe Indikator</p>
                            {{-- View mode --}}
                            <div x-show="!editMode">
                                @if($qcIndicator->is_critical)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-bold bg-red-100 text-red-700">
                                        <span class="w-2 h-2 rounded-full bg-red-500 flex-shrink-0"></span>
                                        Kritis (Wajib Lulus)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-bold bg-yellow-100 text-yellow-700">
                                        <span class="w-2 h-2 rounded-full bg-yellow-400 flex-shrink-0"></span>
                                        Non-Kritis (Akumulasi %)
                                    </span>
                                @endif
                            </div>
                            {{-- Edit mode --}}
                            <div x-show="editMode">
                                <select name="is_critical"
                                    class="w-full border {{ $errors->has('is_critical') ? 'border-red-400 bg-red-50 text-red-900' : 'border-gray-300' }} rounded-lg p-2.5 text-sm focus:ring-orange-500 focus:outline-none text-black">
                                    <option value="1" {{ old('is_critical', (int)$qcIndicator->is_critical) == 1 ? 'selected' : '' }}>
                                        Kritis (Wajib Lulus)
                                    </option>
                                    <option value="0" {{ old('is_critical', (int)$qcIndicator->is_critical) == 0 ? 'selected' : '' }}>
                                        Non-Kritis (Akumulasi %)
                                    </option>
                                </select>
                                @error('is_critical')
                                    <p class="mt-1 text-xs text-red-500 font-semibold">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Kolom Kanan: Status --}}
                    <div>
                        {{-- Status Aktif --}}
                        <div>
                            <p class="text-xs text-black font-bold uppercase mb-1">Status Pengecekan</p>
                            {{-- View mode --}}
                            <div x-show="!editMode">
                                @if($qcIndicator->is_active)
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-bold bg-green-100 text-green-700">
                                        Aktif
                                    </span>
                                    <p class="text-xs text-gray-400 mt-2 leading-relaxed">Indikator ini sedang digunakan dan muncul di formulir pengecekan kualitas produksi.</p>
                                @else
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-bold bg-gray-100 text-gray-500">
                                        Nonaktif
                                    </span>
                                    <p class="text-xs text-gray-400 mt-2 leading-relaxed">Indikator ini dimatikan dan tidak akan muncul di formulir pengecekan kualitas produksi.</p>
                                @endif
                            </div>
                            {{-- Edit mode --}}
                            <div x-show="editMode">
                                <select name="is_active"
                                    class="w-full border {{ $errors->has('is_active') ? 'border-red-400 bg-red-50 text-red-900' : 'border-gray-300' }} rounded-lg p-2.5 text-sm focus:ring-orange-500 focus:outline-none text-black">
                                    <option value="1" {{ old('is_active', (int)$qcIndicator->is_active) == 1 ? 'selected' : '' }}>
                                        Aktif
                                    </option>
                                    <option value="0" {{ old('is_active', (int)$qcIndicator->is_active) == 0 ? 'selected' : '' }}>
                                        Nonaktif
                                    </option>
                                </select>
                                @error('is_active')
                                    <p class="mt-1 text-xs text-red-500 font-semibold">{{ $message }}</p>
                                @enderror
                                <p class="mt-2 text-xs text-gray-400">Pilih "Nonaktif" jika tidak mau indikator ini ditampilkan saat ngejalanin proses pengecekan kualitas.</p>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Footer Info --}}
                <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex justify-between text-xs text-gray-400">
                    <span>Dibuat pada: {{ $qcIndicator->created_at->format('d M Y H:i') }}</span>
                    <span>Terakhir diperbarui: {{ $qcIndicator->updated_at->format('d M Y H:i') }}</span>
                </div>
            </div>
        </form>
    </div>
</x-admin-app-layout>