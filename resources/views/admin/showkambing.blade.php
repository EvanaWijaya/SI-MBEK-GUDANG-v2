<x-admin-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Admin - Detail Kambing') }}
        </h2>
    </x-slot>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Poppins', sans-serif; background-color: #f9fafb; }
        .brand-orange { background-color: #e58609; }
        .text-brand-orange { color: #e58609; }
        .dashboard-section {
            background-color: white; border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        }
        .info-card {
            background-color: white; border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); padding: 1.5rem; margin-bottom: 1.5rem;
        }
        .status-badge {
            display: inline-block; padding: 0.25rem 0.75rem; border-radius: 9999px;
            font-weight: 600; text-transform: uppercase; font-size: 0.75rem;
        }
        .status-yes { background-color: #dcfce7; color: #166534; }
        .status-no { background-color: #fee2e2; color: #b91c1c; }
    </style>

    <div class="min-h-screen flex flex-col bg-gray-50" x-data="{ open: false, forSale: '{{ $kambings->for_sale ?? 'no' }}' }">
        <main class="max-w-5xl mx-auto py-8 w-full px-4">
            
            {{-- Notifikasi Sukses --}}
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 font-semibold">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Notifikasi Error Backend (Kalau ditolak dari server) --}}
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 font-semibold">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="dashboard-section overflow-hidden">
                <div class="brand-orange p-5 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="flex items-center w-full md:w-auto">
                        <a href="{{ route('admin.listkambing') }}" class="text-white hover:text-orange-200 font-medium flex items-center mr-3">
                            <svg class="w-5 h-5 mr-1" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                <path stroke="#FFFFFF" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12l4-4m-4 4 4 4" />
                            </svg>
                        </a>
                        <h3 class="text-lg font-medium text-white">Detail Kambing ID: {{ $kambings->id }}</h3>
                    </div>
                    <div class="flex gap-2 w-full md:w-auto justify-end">
                        <a href="{{ route('admin.kambing.monitoring', $kambings->id) }}"
                            class="bg-white text-brand-orange px-4 py-2 rounded-md shadow hover:bg-gray-100 flex items-center font-semibold">
                            Monitoring
                        </a>
                        <button class="bg-white text-brand-orange px-4 py-2 rounded-md shadow hover:bg-gray-100 flex items-center font-semibold"
                            @click="open = true">
                            Edit Data
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <div class="info-card">
                                <h4 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">Informasi Utama</h4>
                                <div class="space-y-4">
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Nama</span>
                                        <span class="text-gray-600">:</span>
                                        <span class="text-gray-800 font-medium ml-2">{{ $kambings->name }}</span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Pemilik</span>
                                        <span class="text-gray-600">:</span>
                                        <span class="text-gray-800 font-medium ml-2">{{ $kambings->user ? $kambings->user->name : '-' }}</span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Tanggal Lahir</span>
                                        <span class="text-gray-600">:</span>
                                        <span class="text-gray-800 font-medium ml-2">{{ \Carbon\Carbon::parse($kambings->tanggal_lahir)->format('d M Y') }}</span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Umur Sekarang</span>
                                        <span class="text-gray-600">:</span>
                                        <span class="text-gray-800 font-medium ml-2">{{ $kambings->hitungUmur() }}</span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Jenis & Kelamin</span>
                                        <span class="text-gray-600">:</span>
                                        <span class="text-gray-800 font-medium ml-2">{{ $kambings->type_goat }} ({{ $kambings->jenis_kelamin }})</span>
                                    </div>
                                </div>
                            </div>
                            <div class="info-card">
                                <h4 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">Informasi Berat</h4>
                                <div class="space-y-4">
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Berat Awal</span>
                                        <span class="text-gray-600">:</span>
                                        <span class="text-gray-800 font-medium ml-2">{{ $kambings->weight }} kg</span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Berat Sekarang</span>
                                        <span class="text-gray-600">:</span>
                                        <span class="text-gray-800 font-medium ml-2">{{ $kambings->weight_now }} kg</span>
                                    </div>
                                    @php $selisih = $kambings->weight_now - $kambings->weight; @endphp
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Perkembangan</span>
                                        <span class="text-gray-600">:</span>
                                        <span class="font-medium ml-2 {{ $selisih >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $selisih >= 0 ? '+' : '-' }}{{ abs($selisih) }} kg
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="info-card">
                                <h4 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">Foto Kambing</h4>
                                @php
                                    $mediaItems = $kambings->media;
                                    $primaryImage = $mediaItems->where('is_primary', true)->first() ?? $mediaItems->first();
                                    $otherImages = $mediaItems->reject(fn($m) => $primaryImage && $m->id === $primaryImage->id);
                                @endphp
                                
                                @if ($mediaItems->isNotEmpty())
                                    @if($primaryImage)
                                        <div class="relative mb-3 flex justify-center">
                                            <img src="{{ $primaryImage->url }}" class="w-full h-64 object-cover rounded-lg shadow border ring-2 ring-brand-orange cursor-pointer" onclick="showImagePopup('{{ $primaryImage->url }}')">
                                            <span class="absolute top-2 left-2 bg-brand-orange text-white text-xs font-bold px-3 py-1 rounded-full shadow">Utama</span>
                                        </div>
                                    @endif
                                    
                                    @if($otherImages->isNotEmpty())
                                        <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                                            @foreach($otherImages as $media)
                                                <img src="{{ $media->url }}" class="h-20 w-full object-cover rounded border cursor-pointer hover:opacity-80 transition" onclick="showImagePopup('{{ $media->url }}')" />
                                            @endforeach
                                        </div>
                                    @endif
                                @else
                                    <div class="flex flex-col items-center justify-center h-40 text-gray-400 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                                        Tidak ada foto
                                    </div>
                                @endif
                            </div>

                            <div class="info-card">
                                <h4 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">Status</h4>
                                <div class="space-y-4">
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Status Vaksin</span>
                                        <span class="text-gray-600">:</span>
                                        <span class="text-gray-800 font-medium ml-2">{{ $kambings->faksin_status }}</span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Kesehatan</span>
                                        <span class="text-gray-600">:</span>
                                        <span class="text-gray-800 font-medium ml-2">{{ $kambings->healt_status }}</span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Dijual?</span>
                                        <span class="text-gray-600">:</span>
                                        <span class="ml-2">
                                            @if ($kambings->for_sale === 'yes')
                                                <span class="status-badge status-yes">Ya</span>
                                            @else
                                                <span class="status-badge status-no">Tidak</span>
                                            @endif
                                        </span>
                                    </div>
                                    @if ($kambings->for_sale === 'yes')
                                        <div class="flex items-start">
                                            <span class="text-gray-600 font-medium w-1/3">Harga</span>
                                            <span class="text-gray-600">:</span>
                                            <span class="text-xl font-bold text-brand-orange ml-2">Rp {{ number_format($kambings->harga, 0, ',', '.') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        {{-- MODAL EDIT --}}
        <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 transition-opacity duration-300" x-cloak>
            <div class="bg-white rounded-lg shadow-xl w-full max-w-3xl mx-auto max-h-[90vh] overflow-y-auto" @click.outside="open = false">
                <div class="brand-orange p-4 rounded-t-lg sticky top-0 z-10 flex justify-between items-center">
                    <h2 class="text-xl font-bold text-white">Edit Data Kambing</h2>
                    <button @click="open = false" class="text-white hover:text-gray-200 text-2xl font-bold">&times;</button>
                </div>
                
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.kambings.update', $kambings->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kambing</label>
                                    <input type="text" name="name" value="{{ $kambings->name }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-brand-orange">
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                                    <input type="date" name="tanggal_lahir" value="{{ \Carbon\Carbon::parse($kambings->tanggal_lahir)->format('Y-m-d') }}" max="{{ date('Y-m-d') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-brand-orange">
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Pemilik</label>
                                    <select name="user_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-brand-orange">
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}" {{ $kambings->user_id == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis</label>
                                        <select name="type_goat" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-brand-orange">
                                            <option value="Etawa" {{ $kambings->type_goat == 'Etawa' ? 'selected' : '' }}>Etawa</option>
                                            <option value="Boer" {{ $kambings->type_goat == 'Boer' ? 'selected' : '' }}>Boer</option>
                                            <option value="Skeang" {{ $kambings->type_goat == 'Skeang' ? 'selected' : '' }}>Skeang</option>
                                            <option value="Saaren" {{ $kambings->type_goat == 'Saaren' ? 'selected' : '' }}>Saaren</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Kelamin</label>
                                        <select name="jenis_kelamin" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-brand-orange">
                                            <option value="Jantan" {{ $kambings->jenis_kelamin == 'Jantan' ? 'selected' : '' }}>Jantan</option>
                                            <option value="Betina" {{ $kambings->jenis_kelamin == 'Betina' ? 'selected' : '' }}>Betina</option>
                                        </select>
                                    </div>
                                </div>

                                @if($kambings->media->isNotEmpty())
                                    <div class="mb-4 bg-gray-50 p-3 rounded-lg border border-gray-200">
                                        <label class="block text-xs font-bold text-gray-700 mb-2">Foto Saat Ini (Klik untuk Hapus)</label>
                                        <div class="grid grid-cols-3 gap-2">
                                            @foreach($kambings->media as $media)
                                                <div class="relative group" id="foto-lama-{{ $media->id }}">
                                                    <img src="{{ $media->url }}" class="h-16 w-full object-cover rounded border">
                                                    <button type="button" onclick="tandaiHapusFoto({{ $media->id }})"
                                                        class="absolute inset-0 bg-red-600 bg-opacity-70 text-white text-[10px] font-bold flex items-center justify-center opacity-0 group-hover:opacity-100 transition rounded">
                                                        HAPUS
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div id="wrapper-hapus-media"></div>
                                    </div>
                                @endif
                            </div>

                            <div>
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Berat Awal</label>
                                        <input type="number" step="0.1" name="weight" value="{{ $kambings->weight }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-brand-orange">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Berat Sekarang</label>
                                        <input type="number" step="0.1" name="weight_now" value="{{ $kambings->weight_now }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-brand-orange">
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Status Vaksin</label>
                                    <select id="faksin_status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-brand-orange" required>
                                        <option value="Aktif" {{ $kambings->faksin_status != 'Tidak Aktif' ? 'selected' : '' }}>Aktif</option>
                                        <option value="Tidak Aktif" {{ $kambings->faksin_status == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                                    </select>
                                </div>

                                <div class="mb-4" id="jenis_vaksin_wrapper" style="display: {{ $kambings->faksin_status != 'Tidak Aktif' ? 'block' : 'none' }};">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Vaksin</label>
                                    <select id="jenis_vaksin" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-brand-orange">
                                        <option value="Vaksin PMK" {{ $kambings->faksin_status == 'Vaksin PMK' ? 'selected' : '' }}>Vaksin PMK</option>
                                        <option value="Vaksin Antraks" {{ $kambings->faksin_status == 'Vaksin Antraks' ? 'selected' : '' }}>Vaksin Antraks</option>
                                        <option value="Vaksin Brucellosis" {{ $kambings->faksin_status == 'Vaksin Brucellosis' ? 'selected' : '' }}>Vaksin Brucellosis</option>
                                    </select>
                                </div>
                                <input type="hidden" name="faksin_status" id="faksin_status_final" value="{{ $kambings->faksin_status }}">

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Status Kesehatan</label>
                                    <select id="health_status_select" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-brand-orange" required>
                                        <option value="Sehat" {{ $kambings->healt_status == 'Sehat' ? 'selected' : '' }}>Sehat</option>
                                        <option value="Tidak Sehat" {{ $kambings->healt_status == 'Tidak Sehat' ? 'selected' : '' }}>Tidak Sehat</option>
                                        <option value="Lainnya" {{ $kambings->healt_status != 'Sehat' && $kambings->healt_status != 'Tidak Sehat' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                    <input type="text" id="health_status_custom" placeholder="Jelaskan kondisi..." value="{{ $kambings->healt_status != 'Sehat' && $kambings->healt_status != 'Tidak Sehat' ? $kambings->healt_status : '' }}" class="mt-2 hidden w-full px-3 py-2 border border-gray-300 rounded-md" />
                                    <input type="hidden" name="healt_status" id="health_status_final_input" value="{{ $kambings->healt_status }}">
                                </div>

                                <div class="mb-4 p-4 border border-orange-200 rounded-lg bg-orange-50">
                                    <label class="block text-sm font-bold text-gray-800 mb-2">Untuk Dijual?</label>
                                    <select name="for_sale" x-model="forSale" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-brand-orange">
                                        <option value="yes">Ya, Dijual</option>
                                        <option value="no">Tidak</option>
                                    </select>
                                    <div class="mt-3" x-show="forSale === 'yes'">
                                        <label class="block text-xs font-bold text-gray-600 mb-1">Harga (Rp)</label>
                                        <input
                                            type="text"
                                            name="harga"
                                            value="{{ $kambings->harga ? 'Rp ' . number_format($kambings->harga, 0, ',', '.') : '' }}"
                                            oninput="formatRupiah(this)"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-400"
                                            placeholder="Masukkan harga">
                                    </div>
                                </div>

                                {{-- TAMBAH FOTO BARU --}}
                                <div class="mb-4">
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Tambah Foto Baru</label>
                                    <div id="image-container-edit">
                                        <div class="image-input-row mb-2">
                                            <input type="file" name="images[]" accept="image/*"
                                                   class="image-input-edit block w-full text-sm text-gray-500
                                                          file:mr-4 file:py-2 file:px-4 file:rounded-full
                                                          file:border-0 file:text-sm file:font-semibold
                                                          file:bg-orange-50 file:text-orange-700
                                                          hover:file:bg-orange-100">
                                        </div>
                                    </div>
                                    <button type="button" id="add-image-btn-edit"
                                            class="mt-1 inline-flex items-center text-orange-700 bg-orange-50
                                                   hover:bg-orange-100 font-semibold text-xs px-3 py-1.5 rounded-full
                                                   border-0 transition-colors">
                                        + Tambah Foto
                                    </button>
                                    <div class="mt-2 p-3 bg-orange-50 border border-orange-200 rounded-lg">
                                        <p class="text-xs text-orange-700 font-medium">• Maksimal total 10 foto per kambing</p>
                                        <p class="text-xs text-orange-700 font-medium">• Ukuran maksimal 2 MB per foto</p>
                                        <p class="text-xs text-orange-700 font-medium">• Format yang didukung: JPG, JPEG, PNG</p>
                                    </div>

                                    <p id="image-error-edit" class="mt-2 text-sm text-red-600 font-medium hidden"></p>

                                    <div id="preview-container-edit" class="grid grid-cols-3 gap-2 mt-3"></div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t border-gray-200 flex justify-end space-x-3">
                            <button type="button" @click="open = false" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100 font-semibold transition-colors">Batal</button>
                            <button id="submit-btn-edit" type="submit" class="px-6 py-2 bg-brand-orange text-white rounded-md hover:bg-orange-700 font-bold transition-colors shadow-md">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="imagePopup" class="fixed inset-0 hidden items-center justify-center bg-black bg-opacity-80 z-[100]" onclick="hideImagePopup()">
            <div class="relative max-w-4xl w-full p-4 flex justify-center" onclick="event.stopPropagation()">
                <button onclick="hideImagePopup()" class="absolute top-0 right-0 text-white bg-red-500 hover:bg-red-600 rounded-full p-2 transform translate-x-2 -translate-y-2 shadow-lg transition-colors">&times;</button>
                <img id="popupImage" src="" class="max-h-[85vh] w-auto object-contain rounded-lg shadow-2xl">
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // ── Popup Foto ──────────────────────────────────────────────────────────
        function showImagePopup(src) {
            document.getElementById('popupImage').src = src;
            const popup = document.getElementById('imagePopup');
            popup.classList.remove('hidden');
            popup.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function hideImagePopup() {
            const popup = document.getElementById('imagePopup');
            popup.classList.add('hidden');
            popup.classList.remove('flex');
            document.body.style.overflow = '';
        }

        // ── Hapus Foto Lama ─────────────────────────────────────────────────────
        function tandaiHapusFoto(mediaId) {
            document.getElementById('foto-lama-' + mediaId).remove();
            const input = document.createElement('input');
            input.type  = 'hidden';
            input.name  = 'hapus_media[]';
            input.value = mediaId;
            document.getElementById('wrapper-hapus-media').appendChild(input);
        }

        // ── Logika Utama (dijalankan setelah DOM siap) ──────────────────────────
        document.addEventListener('DOMContentLoaded', function () {

            // -- Upload & Preview Foto Baru --
            const containerEdit        = document.getElementById('image-container-edit');
            const addBtnEdit           = document.getElementById('add-image-btn-edit');
            const previewContainerEdit = document.getElementById('preview-container-edit');
            const errorBox             = document.getElementById('image-error-edit');
            const submitBtn            = document.getElementById('submit-btn-edit');
            const MAX_SIZE             = 2 * 1024 * 1024; // 2 MB

            function renderPreviewEdit() {
                if (!previewContainerEdit || !errorBox || !submitBtn) return;

                previewContainerEdit.innerHTML = '';
                let hasError     = false;
                let errorMessages = [];

                containerEdit.querySelectorAll('.image-input-edit').forEach(function (input) {
                    if (!input.files.length) return;

                    const file = input.files[0];

                    if (file.size > MAX_SIZE) {
                        hasError = true;
                        errorMessages.push(file.name + ' melebihi ukuran maksimal 2 MB');
                        input.value = '';
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function (e) {
                        previewContainerEdit.innerHTML += `
                            <div class="border rounded p-1 shadow-sm bg-white">
                                <img src="${e.target.result}" class="w-full h-16 object-cover rounded">
                            </div>`;
                    };
                    reader.readAsDataURL(file);
                });

                if (hasError) {
                    errorBox.innerHTML = errorMessages.join('<br>');
                    errorBox.classList.remove('hidden');
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                } else {
                    errorBox.innerHTML = '';
                    errorBox.classList.add('hidden');
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }

            if (addBtnEdit && containerEdit) {
                addBtnEdit.addEventListener('click', function () {
                    const fotoLama = document.querySelectorAll('[id^="foto-lama-"]').length;
                    const fotoBaru = containerEdit.querySelectorAll('input[type=file]').length;

                    if ((fotoLama + fotoBaru) >= 10) {
                        alert('Maksimal total 10 foto per kambing.');
                        return;
                    }

                    const wrapper = document.createElement('div');
                    wrapper.classList.add('image-input-row', 'mb-2');
                    wrapper.innerHTML = `
                        <div class="flex gap-2 items-center mt-1">
                            <input type="file" name="images[]" accept="image/*"
                                   class="image-input-edit block w-full text-sm text-gray-500
                                          file:mr-4 file:py-1.5 file:px-3 file:rounded-full
                                          file:border-0 file:text-xs file:font-semibold
                                          file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                            <button type="button"
                                    class="remove-image-edit shrink-0 px-2.5 py-1 bg-red-500 text-white text-xs rounded-full hover:bg-red-600">
                                Hapus
                            </button>
                        </div>`;
                    containerEdit.appendChild(wrapper);
                });

                containerEdit.addEventListener('click', function (e) {
                    if (e.target.classList.contains('remove-image-edit')) {
                        e.target.closest('.image-input-row').remove();
                        renderPreviewEdit();
                    }
                });

                containerEdit.addEventListener('change', function (e) {
                    if (!e.target.classList.contains('image-input-edit')) return;
                    renderPreviewEdit();
                });
            }

            // Validasi tambahan saat submit (double-check sebelum ke server)
            const form = document.querySelector('form[enctype="multipart/form-data"]');
            if (form) {
                form.addEventListener('submit', function (e) {
                    if (!containerEdit) return;
                    const inputs = containerEdit.querySelectorAll('.image-input-edit');
                    for (let input of inputs) {
                        if (!input.files.length) continue;
                        if (input.files[0].size > MAX_SIZE) {
                            alert('File "' + input.files[0].name + '" melebihi batas 2 MB. Upload dibatalkan.');
                            e.preventDefault();
                            return false;
                        }
                    }
                });
            }

            // -- Dropdown Vaksin --
            const statusSelect  = document.getElementById('faksin_status');
            const jenisWrapper  = document.getElementById('jenis_vaksin_wrapper');
            const jenisSelect   = document.getElementById('jenis_vaksin');
            const hiddenVaksin  = document.getElementById('faksin_status_final');

            function updateVaksin() {
                if (!statusSelect) return;
                if (statusSelect.value === 'Aktif') {
                    jenisWrapper.style.display = 'block';
                    hiddenVaksin.value = jenisSelect.value || '';
                } else {
                    jenisWrapper.style.display = 'none';
                    hiddenVaksin.value = statusSelect.value;
                }
            }

            if (statusSelect) {
                statusSelect.addEventListener('change', updateVaksin);
                jenisSelect.addEventListener('change', updateVaksin);
                updateVaksin();
            }

            // -- Dropdown Status Kesehatan --
            const healthSelect = document.getElementById('health_status_select');
            const healthCustom = document.getElementById('health_status_custom');
            const healthHidden = document.getElementById('health_status_final_input');

            function toggleOtherHealthStatus(el) {
                if (el.value === 'Lainnya') {
                    healthCustom.classList.remove('hidden');
                    healthHidden.value = healthCustom.value;
                } else {
                    healthCustom.classList.add('hidden');
                    healthHidden.value = el.value;
                }
            }

            if (healthSelect) {
                healthSelect.addEventListener('change', function () {
                    toggleOtherHealthStatus(this);
                });
                healthCustom.addEventListener('input', function () {
                    healthHidden.value = this.value;
                });
                toggleOtherHealthStatus(healthSelect);
            }
        });

        function formatRupiah(input) {

            let angka = input.value.replace(/[^,\d]/g, '');

            let split = angka.split(',');
            let sisa = split[0].length % 3;

            let rupiah = split[0].substr(0, sisa);

            let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] !== undefined
                ? rupiah + ',' + split[1]
                : rupiah;

            input.value = rupiah ? 'Rp ' + rupiah : '';
        }
    </script>
    @endpush
</x-admin-app-layout>