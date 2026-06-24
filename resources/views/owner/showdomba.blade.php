<x-owner-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Owner - List Domba') }}
        </h2>
    </x-slot>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9fafb;
        }

        .brand-orange {
            background-color: #e58609;
        }

        .hover\:brand-orange-dark:hover {
            background-color: #d97b08;
        }

        .text-brand-orange {
            color: #e58609;
        }

        .border-brand-orange {
            border-color: #e58609;
        }

        .stat-card {
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        }

        .dashboard-section {
            background-color: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            transition: box-shadow 0.3s ease;
        }

        .dashboard-section:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .user-card {
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
        }

        .user-card:hover {
            border-color: #e58609;
            transform: translateY(-3px);
        }

        .header-gradient {
            background: linear-gradient(135deg, #FFF 0%, #FFEDD5 100%);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .info-card {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
        }

        .status-yes {
            background-color: #dcfce7;
            color: #166534;
        }

        .status-no {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        /* Responsive tweaks */
        @media (max-width: 768px) {
            .dashboard-section {
                border-radius: 8px;
            }

            .info-card {
                padding: 1rem;
            }
        }
    </style>

    <div class="min-h-screen flex flex-col bg-gray-50" x-data="{ open: false, for_sale: '{{ $dombas->for_sale }}' }">
        <main class="max-w-5xl mx-auto py-8 w-full">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <div class="dashboard-section overflow-hidden">
                <div class="brand-orange p-5 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="flex items-center w-full md:w-auto">
                        <a href="{{ route('owner.listdomba') }}"
                            class="text-white hover:text-orange-200 font-medium flex items-center mr-3">
                            <svg class="w-5 h-5 mr-1" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                <path stroke="#FFFFFF" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 12h14M5 12l4-4m-4 4 4 4" />
                            </svg>
                        </a>
                        <h3 class="text-lg font-medium text-white">
                            Detail Domba ID: {{ $dombas->id }}
                        </h3>
                    </div>
                    <div class="flex gap-2 w-full md:w-auto justify-end">
                        <a href="{{ route('owner.domba.monitoring', $dombas->id) }}"
                            class="bg-white text-brand-orange px-4 py-2 rounded-md shadow hover:bg-gray-100 flex items-center">
                            <svg class="w-5 h-5 mr-1" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 4.5V19a1 1 0 0 0 1 1h15M7 14l4-4 4 4 5-5m0 0h-3.207M20 9v3.207" />
                            </svg>
                            Monitoring
                        </a>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Informasi Utama -->
                        <div>
                            <div class="info-card">
                                <h4 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">
                                    Informasi Utama</h4>
                                <div class="space-y-4">
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Nama</span>
                                        <span class="text-gray-600">:</span>
                                        <span class="text-gray-800 font-medium ml-2">{{ $dombas->name }}</span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Pemilik</span>
                                        <span class="text-gray-600">:</span>
                                        <span class="text-gray-800 font-medium ml-2">
                                            {{ $dombas->user ? $dombas->user->name : '-' }}
                                        </span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Tanggal Lahir</span>
                                        <span class="text-gray-600">:</span>
                                        <span class="text-gray-800 font-medium ml-2">
                                            {{ \Carbon\Carbon::parse($dombas->tanggal_lahir)->format('d M Y') }}
                                        </span>
                                    </div>
                                    @if ($dombas->umurAwal())
                                        <div class="flex items-start">
                                            <span class="text-gray-600 font-medium w-1/3">Umur Awal</span>
                                            <span class="text-gray-600">:</span>
                                            <span class="text-gray-800 font-medium ml-2">
                                                {{ $dombas->umurAwal() }}
                                            </span>
                                        </div>
                                    @endif
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Umur Sekarang</span>
                                        <span class="text-gray-600">:</span>
                                        <span class="text-gray-800 font-medium ml-2">
                                            {{ $dombas->hitungUmur() }}
                                        </span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Jenis</span>
                                        <span class="text-gray-600">:</span>
                                        <span class="text-gray-800 font-medium ml-2">
                                            {{ $dombas->type_domba }}
                                        </span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Jenis Kelamin</span>
                                        <span class="text-gray-600">:</span>
                                        <span class="text-gray-800 font-medium ml-2">
                                            {{ $dombas->jenis_kelamin }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <!-- Informasi Berat -->
                            <div class="info-card">
                                <h4 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">
                                    Informasi Berat</h4>
                                <div class="space-y-4">
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Berat Awal</span>
                                        <span class="text-gray-600">:</span>
                                        <span class="text-gray-800 font-medium ml-2">
                                            {{ $dombas->weight }} kg
                                        </span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Berat Sekarang</span>
                                        <span class="text-gray-600">:</span>
                                        <span class="text-gray-800 font-medium ml-2">
                                            {{ $dombas->weight_now }} kg
                                        </span>
                                    </div>
                                    @php
                                        $selisih = $dombas->weight_now - $dombas->weight;
                                    @endphp
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Perkembangan</span>
                                        <span class="text-gray-600">:</span>
                                        <span
                                            class="font-medium ml-2 {{ $selisih >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $selisih >= 0 ? '+' : '-' }}{{ abs($selisih) }} kg
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Foto dan Status -->
                        <div>
                           <div class="info-card">
                                <h4 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">
                                    Foto Domba
                                </h4>

                                @php 
                                    $mediaItems = $dombas->media; 
                                    // Cari foto primary, jika tidak ada ambil urutan pertama
                                    $primaryImage = $mediaItems->where('is_primary', true)->first() ?? $mediaItems->first();
                                    // Ambil sisa foto selain foto utama
                                    $otherImages = $mediaItems->reject(fn($m) => $primaryImage && $m->id === $primaryImage->id);
                                @endphp

                                @if ($mediaItems->isNotEmpty())
                                    @if($primaryImage)
                                        <div class="relative mb-3">
                                            <img src="{{ $primaryImage->url }}" loading="lazy" alt="Foto Utama"
                                                class="w-full h-64 object-cover rounded-lg shadow-sm cursor-pointer border ring-2 ring-brand-orange"
                                                onclick="showImagePopup('{{ $primaryImage->url }}')">
                                            <span class="absolute top-2 left-2 bg-brand-orange text-white text-xs font-bold px-3 py-1 rounded-full shadow">
                                                Utama
                                            </span>
                                        </div>
                                    @endif

                                    @if($otherImages->isNotEmpty())
                                        <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                                            @foreach ($otherImages as $media)
                                                <div class="relative">
                                                    <img src="{{ $media->url }}" loading="lazy" alt="Foto domba {{ $loop->iteration }}"
                                                        class="w-full h-20 object-cover rounded-md shadow-sm cursor-pointer border hover:opacity-80 transition"
                                                        onclick="showImagePopup('{{ $media->url }}')">
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                @else
                                    {{-- Fallback ke kolom image lama jika tabel media kosong --}}
                                    @if ($dombas->image)
                                        <img src="{{ asset('storage/' . $dombas->image) }}" loading="lazy" alt="gambar domba"
                                            class="w-full h-64 object-cover rounded-lg shadow-sm cursor-pointer ring-2 ring-brand-orange"
                                            onclick="showImagePopup('{{ asset('storage/' . $dombas->image) }}')" />
                                    @else
                                        <div class="flex flex-col items-center justify-center h-40 text-gray-400 bg-gray-50 rounded-lg border border-dashed">
                                            <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M14 8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span class="text-sm">Belum ada foto</span>
                                        </div>
                                    @endif
                                @endif
                            </div>
                            <!-- Informasi Status -->
                            <div class="info-card">
                                <h4 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">
                                    Status</h4>
                                <div class="space-y-4">
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Status Vaksin</span>
                                        <span class="text-gray-600">:</span>
                                        <span class="text-gray-800 font-medium ml-2">
                                            {{ $dombas->faksin_status }}
                                        </span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Status Kesehatan</span>
                                        <span class="text-gray-600">:</span>
                                        <span class="text-gray-800 font-medium ml-2">
                                            {{ $dombas->healt_status }}
                                        </span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Status Dijual</span>
                                        <span class="text-gray-600">:</span>
                                        <span class="ml-2">
                                            @if ($dombas->for_sale === 'yes')
                                                <span class="status-badge status-yes">Ya</span>
                                            @else
                                                <span class="status-badge status-no">Tidak</span>
                                            @endif
                                        </span>
                                    </div>
                                    @if ($dombas->for_sale === 'yes')
                                        <div class="flex items-start">
                                            <span class="text-gray-600 font-medium w-1/3">Harga</span>
                                            <span class="text-gray-600">:</span>
                                            <span class="text-xl font-bold text-brand-orange ml-2">
                                                Rp {{ number_format($dombas->harga, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    @endif
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Tanggal Dibuat</span>
                                        <span class="text-gray-600">:</span>
                                        <span class="text-gray-800 font-medium ml-2">
                                            {{ $dombas->created_at->format('d M Y') }}
                                        </span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Terakhir Diperbarui</span>
                                        <span class="text-gray-600">:</span>
                                        <span class="text-gray-800 font-medium ml-2">
                                            {{ $dombas->updated_at->format('d M Y') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        
        <script>
            function showImagePopup(src) {
                const popup = document.createElement('div');
                popup.className = 'fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-80 p-4';
                popup.innerHTML = `
                <div class="relative max-w-4xl w-full">
                    <button onclick="this.parentElement.parentElement.remove()" 
                        class="absolute top-4 right-4 text-white bg-black bg-opacity-50 rounded-full p-2 hover:bg-opacity-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    <img src="${src}" class="max-h-[90vh] w-auto mx-auto" alt="Preview">
                </div>
            `;
                document.body.appendChild(popup);
            }

            function toggleOtherHealthStatus(select) {
                const customInput = document.getElementById('health_status_custom');
                const finalInput = document.getElementById('health_status_final');

                if (select.value === 'Lainnya') {
                    customInput.classList.remove('hidden');
                    customInput.value = '';
                    finalInput.value = '';
                    customInput.addEventListener('input', function () {
                        finalInput.value = this.value;
                    });
                } else {
                    customInput.classList.add('hidden');
                    finalInput.value = select.value;
                }
            }
            (function () {
            const containerEdit        = document.getElementById('image-container-edit');
            const addBtnEdit           = document.getElementById('add-image-btn-edit');
            const previewContainerEdit = document.getElementById('preview-container-edit');

            if(addBtnEdit) {
                addBtnEdit.addEventListener('click', () => {
                    if (containerEdit.querySelectorAll('input[type=file]').length >= 10) {
                        alert('Maksimal 10 gambar tambahan');
                        return;
                    }
                    const wrapper = document.createElement('div');
                    wrapper.classList.add('image-input-row', 'mb-2');
                    wrapper.innerHTML = `
                        <div class="flex gap-2 items-center mt-2">
                            <input type="file" name="images[]" accept="image/*"
                                class="image-input-edit block w-full text-sm text-gray-500
                                       file:mr-4 file:py-2 file:px-4 file:rounded-full
                                       file:border-0 file:text-sm file:font-semibold
                                       file:bg-orange-50 file:text-orange-700
                                       hover:file:bg-orange-100">
                            <button type="button"
                                class="remove-image-edit shrink-0 px-3 py-1.5 bg-red-500
                                       text-white text-sm rounded-full hover:bg-red-600 transition-colors">
                                Hapus
                            </button>
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
                                <div class="border border-gray-200 rounded-lg p-1 shadow-sm">
                                    <img src="${e.target.result}"
                                         class="w-full h-24 object-cover rounded">
                                </div>`;
                        };
                        reader.readAsDataURL(input.files[0]);
                    });
                }
            }
        })();
        </script>
    </div>
</x-owner-app-layout>