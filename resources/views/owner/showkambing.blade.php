<x-owner-app-layout>
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
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 font-semibold">
                    {{ session('success') }}
                </div>
            @endif

            <div class="dashboard-section overflow-hidden">
                <div class="brand-orange p-5 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="flex items-center w-full md:w-auto">
                        <a href="{{ route('owner.listkambing') }}" class="text-white hover:text-orange-200 font-medium flex items-center mr-3">
                            <svg class="w-5 h-5 mr-1" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                <path stroke="#FFFFFF" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12l4-4m-4 4 4 4" />
                            </svg>
                        </a>
                        <h3 class="text-lg font-medium text-white">Detail Kambing ID: {{ $kambings->id }}</h3>
                    </div>
                    <div class="flex gap-2 w-full md:w-auto justify-end">
                        <a href="{{ route('owner.kambing.monitoring', $kambings->id) }}"
                            class="bg-white text-brand-orange px-4 py-2 rounded-md shadow hover:bg-gray-100 flex items-center font-semibold">
                            Monitoring
                        </a>
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
                            {{-- STRUKTUR GRID FOTO TERBARU: 1 BESAR, SISANYA KECIL --}}
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

        <div id="imagePopup" class="fixed inset-0 hidden items-center justify-center bg-black bg-opacity-80 z-[100]" onclick="hideImagePopup()">
            <div class="relative max-w-4xl w-full p-4 flex justify-center" onclick="event.stopPropagation()">
                <button onclick="hideImagePopup()" class="absolute top-0 right-0 text-white bg-red-500 hover:bg-red-600 rounded-full p-2 transform translate-x-2 -translate-y-2 shadow-lg transition-colors">&times;</button>
                <img id="popupImage" src="" class="max-h-[85vh] w-auto object-contain rounded-lg shadow-2xl">
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
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

        function tandaiHapusFoto(mediaId) {
            document.getElementById('foto-lama-' + mediaId).remove();
            let input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'hapus_media[]';
            input.value = mediaId;
            document.getElementById('wrapper-hapus-media').appendChild(input);
        }

        // Jalankan Script Tambah Input Baris Foto Secara Dinamis
        (function () {
            const containerEdit = document.getElementById('image-container-edit');
            const addBtnEdit = document.getElementById('add-image-btn-edit');
            const previewContainerEdit = document.getElementById('preview-container-edit');

            if(addBtnEdit) {
                addBtnEdit.addEventListener('click', () => {
                    if (containerEdit.querySelectorAll('input[type=file]').length >= 10) {
                        alert('Maksimal 10 gambar');
                        return;
                    }
                    const wrapper = document.createElement('div');
                    wrapper.classList.add('image-input-row', 'mb-2');
                    wrapper.innerHTML = `
                        <div class="flex gap-2 items-center mt-1">
                            <input type="file" name="images[]" accept="image/*" class="image-input-edit block w-full text-sm text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                            <button type="button" class="remove-image-edit shrink-0 px-2.5 py-1 bg-red-500 text-white text-xs rounded-full hover:bg-red-600">Hapus</button>
                        </div>`;
                    containerEdit.appendChild(wrapper);
                });

                containerEdit.addEventListener('click', (e) => {
                    if (e.target.classList.contains('remove-image-edit')) {
                        e.target.closest('.image-input-row').remove();
                        renderPreviewEdit();
                    }
                });

                containerEdit.addEventListener('change', renderPreviewEdit);

                function renderPreviewEdit() {
                    previewContainerEdit.innerHTML = '';
                    document.querySelectorAll('.image-input-edit').forEach(input => {
                        if (!input.files.length) return;
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            previewContainerEdit.innerHTML += `
                                <div class="border rounded p-1 shadow-sm bg-white">
                                    <img src="${e.target.result}" class="w-full h-16 object-cover rounded">
                                </div>`;
                        };
                        reader.readAsDataURL(input.files[0]);
                    });
                }
            }
        })();

        // Logika Dropdown Form manual
        const statusSelect = document.getElementById('faksin_status');
        const jenisWrapper = document.getElementById('jenis_vaksin_wrapper');
        const jenisSelect = document.getElementById('jenis_vaksin');
        const hiddenInputVaksin = document.getElementById('faksin_status_final');

        function updateVaksin() {
            if (statusSelect.value === "Aktif") {
                jenisWrapper.style.display = "block";
                hiddenInputVaksin.value = jenisSelect.value || ''; 
            } else {
                jenisWrapper.style.display = "none";
                hiddenInputVaksin.value = statusSelect.value;
            }
        }
        statusSelect.addEventListener('change', updateVaksin);
        jenisSelect.addEventListener('change', updateVaksin);
        updateVaksin();

        const healthSelect = document.getElementById('health_status_select');
        const healthCustom = document.getElementById('health_status_custom');
        const healthHidden = document.getElementById('health_status_final_input');

        function toggleOtherHealthStatus(el) {
            if (el.value === "Lainnya") {
                healthCustom.classList.remove("hidden");
                healthHidden.value = healthCustom.value; 
            } else {
                healthCustom.classList.add("hidden");
                healthHidden.value = el.value; 
            }
        }
        healthCustom.addEventListener("input", function() {
            healthHidden.value = this.value;
        });
        toggleOtherHealthStatus(healthSelect);
    </script>
    @endpush
</x-owner-app-layout>