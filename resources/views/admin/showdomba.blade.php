<x-admin-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Admin - List Domba') }}
        </h2>
    </x-slot>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9fafb;
        }

        .brand-orange { background-color: #e58609; }
        .text-brand-orange { color: #e58609; }
        .border-brand-orange { border-color: #e58609; }

        .stat-card {
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03);
        }

        .dashboard-section {
            background-color: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03);
            transition: box-shadow 0.3s ease;
        }

        .dashboard-section:hover {
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
        }

        .info-card {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
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

        .status-yes { background-color: #dcfce7; color: #166534; }
        .status-no  { background-color: #fee2e2; color: #b91c1c; }

        @media (max-width: 768px) {
            .dashboard-section { border-radius: 8px; }
            .info-card { padding: 1rem; }
        }
    </style>

    <div class="min-h-screen flex flex-col bg-gray-50"
         x-data="{ open: false, forSale: '{{ $dombas->for_sale ?? 'no' }}' }">
        <main class="max-w-5xl mx-auto py-8 w-full px-4">

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 font-semibold">
                    {{ session('success') }}
                </div>
            @endif

            <div class="dashboard-section overflow-hidden">
                {{-- ===== HEADER ===== --}}
                <div class="brand-orange p-5 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="flex items-center w-full md:w-auto">
                        <a href="{{ route('admin.listdomba') }}"
                           class="text-white hover:text-orange-200 font-medium flex items-center mr-3">
                            <svg class="w-5 h-5 mr-1" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                <path stroke="#FFFFFF" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M5 12h14M5 12l4-4m-4 4 4 4"/>
                            </svg>
                        </a>
                        <h3 class="text-lg font-medium text-white">Detail Domba ID: {{ $dombas->id }}</h3>
                    </div>
                    <div class="flex gap-2 w-full md:w-auto justify-end">
                        <a href="{{ route('admin.domba.monitoring', $dombas->id) }}"
                           class="bg-white text-brand-orange px-4 py-2 rounded-md shadow hover:bg-gray-100 flex items-center font-semibold">
                            <svg class="w-5 h-5 mr-1" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4 4.5V19a1 1 0 0 0 1 1h15M7 14l4-4 4 4 5-5m0 0h-3.207M20 9v3.207"/>
                            </svg>
                            Monitoring
                        </a>
                        <button class="bg-white text-brand-orange px-4 py-2 rounded-md shadow hover:bg-gray-100 flex items-center font-semibold"
                                @click="open = true">
                            <svg class="w-5 h-5 mr-1" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="m14.3 4.8 2.9 2.9M7 7H4a1 1 0 0 0-1 1v10c0 .6.4 1 1 1h11c.6 0 1-.4 1-1v-4.5m2.4-10a2 2 0 0 1 0 3l-6.8 6.8L8 14l.7-3.6 6.9-6.8a2 2 0 0 1 2.8 0Z"/>
                            </svg>
                            Edit
                        </button>
                    </div>
                </div>

                {{-- ===== DETAIL VIEW ===== --}}
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        {{-- Kolom Kiri --}}
                        <div>
                            <div class="info-card">
                                <h4 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">Informasi Utama</h4>
                                <div class="space-y-4">
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Nama</span>
                                        <span class="text-gray-600">:</span>
                                        <span class="text-gray-800 font-medium ml-2">{{ $dombas->name }}</span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Pemilik</span>
                                        <span class="text-gray-600">:</span>
                                        <span class="text-gray-800 font-medium ml-2">{{ $dombas->user ? $dombas->user->name : '-' }}</span>
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
                                            <span class="text-gray-800 font-medium ml-2">{{ $dombas->umurAwal() }}</span>
                                        </div>
                                    @endif
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Umur Sekarang</span>
                                        <span class="text-gray-600">:</span>
                                        <span class="text-gray-800 font-medium ml-2">{{ $dombas->hitungUmur() }}</span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Jenis</span>
                                        <span class="text-gray-600">:</span>
                                        <span class="text-gray-800 font-medium ml-2">{{ $dombas->type_domba }}</span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Jenis Kelamin</span>
                                        <span class="text-gray-600">:</span>
                                        <span class="text-gray-800 font-medium ml-2">{{ $dombas->jenis_kelamin }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="info-card">
                                <h4 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">Informasi Berat</h4>
                                <div class="space-y-4">
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Berat Awal</span>
                                        <span class="text-gray-600">:</span>
                                        <span class="text-gray-800 font-medium ml-2">{{ $dombas->weight }} kg</span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Berat Sekarang</span>
                                        <span class="text-gray-600">:</span>
                                        <span class="text-gray-800 font-medium ml-2">{{ $dombas->weight_now }} kg</span>
                                    </div>
                                    @php $selisih = $dombas->weight_now - $dombas->weight; @endphp
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

                        {{-- Kolom Kanan --}}
                        <div>
                            <div class="info-card">
                                <h4 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">Foto Domba</h4>
                                @php
                                    $mediaItems   = $dombas->media;
                                    $primaryImage = $mediaItems->where('is_primary', true)->first() ?? $mediaItems->first();
                                    $otherImages  = $mediaItems->reject(fn($m) => $primaryImage && $m->id === $primaryImage->id);
                                @endphp

                                @if ($mediaItems->isNotEmpty())
                                    @if ($primaryImage)
                                        <div class="relative mb-3">
                                            <img src="{{ $primaryImage->url }}" loading="lazy" alt="Foto Utama"
                                                 class="w-full h-64 object-cover rounded-lg shadow-sm cursor-pointer border ring-2 ring-brand-orange"
                                                 onclick="showImagePopup('{{ $primaryImage->url }}')">
                                            <span class="absolute top-2 left-2 bg-brand-orange text-white text-xs font-bold px-3 py-1 rounded-full shadow">Utama</span>
                                        </div>
                                    @endif
                                    @if ($otherImages->isNotEmpty())
                                        <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                                            @foreach ($otherImages as $media)
                                                <img src="{{ $media->url }}" loading="lazy"
                                                     alt="Foto domba {{ $loop->iteration }}"
                                                     class="w-full h-20 object-cover rounded-md shadow-sm cursor-pointer border hover:opacity-80 transition"
                                                     onclick="showImagePopup('{{ $media->url }}')">
                                            @endforeach
                                        </div>
                                    @endif
                                @else
                                    @if ($dombas->image)
                                        <img src="{{ asset('storage/' . $dombas->image) }}" loading="lazy" alt="gambar domba"
                                             class="w-full h-64 object-cover rounded-lg shadow-sm cursor-pointer ring-2 ring-brand-orange"
                                             onclick="showImagePopup('{{ asset('storage/' . $dombas->image) }}')"/>
                                    @else
                                        <div class="flex flex-col items-center justify-center h-40 text-gray-400 bg-gray-50 rounded-lg border border-dashed">
                                            <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M14 8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span class="text-sm">Belum ada foto</span>
                                        </div>
                                    @endif
                                @endif
                            </div>

                            <div class="info-card">
                                <h4 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">Status</h4>
                                <div class="space-y-4">
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Status Vaksin</span>
                                        <span class="text-gray-600">:</span>
                                        <span class="text-gray-800 font-medium ml-2">{{ $dombas->faksin_status }}</span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Status Kesehatan</span>
                                        <span class="text-gray-600">:</span>
                                        <span class="text-gray-800 font-medium ml-2">{{ $dombas->healt_status }}</span>
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
                                        <span class="text-gray-800 font-medium ml-2">{{ $dombas->created_at->format('d M Y') }}</span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Terakhir Diperbarui</span>
                                        <span class="text-gray-600">:</span>
                                        <span class="text-gray-800 font-medium ml-2">{{ $dombas->updated_at->format('d M Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        {{-- ===== MODAL EDIT ===== --}}
        <div x-show="open"
             x-transition
             class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60"
             x-cloak>
            <div class="bg-white rounded-lg shadow-xl w-full max-w-3xl mx-auto max-h-[90vh] overflow-y-auto"
                 @click.outside="open = false">

                <div class="brand-orange p-4 rounded-t-lg sticky top-0 z-10 flex justify-between items-center">
                    <h2 class="text-xl font-bold text-white">Edit Data Domba</h2>
                    <button @click="open = false" class="text-white hover:text-gray-200 text-2xl font-bold">&times;</button>
                </div>

                <div class="p-6">
                    <form method="POST"
                          action="{{ route('admin.dombas.update', ['domba' => $dombas->id]) }}"
                          enctype="multipart/form-data"
                          novalidate>
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- ===== KOLOM KIRI ===== --}}
                            <div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Domba</label>
                                    <input type="text" name="name" value="{{ $dombas->name }}" required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-400">
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                                    <input type="date" name="tanggal_lahir"
                                           value="{{ \Carbon\Carbon::parse($dombas->tanggal_lahir)->format('Y-m-d') }}"
                                           max="{{ date('Y-m-d') }}" required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-400">
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Pemilik</label>
                                    <select name="user_id" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-400">
                                        <option value="" disabled>Pilih Pemilik</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}" {{ $dombas->user_id == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Domba</label>
                                        <select name="type_domba" required
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-400">
                                            <option value="Garut"       {{ $dombas->type_domba == 'Garut'       ? 'selected' : '' }}>Garut</option>
                                            <option value="Ekor Gemuk"  {{ $dombas->type_domba == 'Ekor Gemuk'  ? 'selected' : '' }}>Ekor Gemuk</option>
                                            <option value="Ekor Tipis"  {{ $dombas->type_domba == 'Ekor Tipis'  ? 'selected' : '' }}>Ekor Tipis</option>
                                            <option value="Texel"       {{ $dombas->type_domba == 'Texel'       ? 'selected' : '' }}>Texel</option>
                                            <option value="Dorper"      {{ $dombas->type_domba == 'Dorper'      ? 'selected' : '' }}>Dorper</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                                        <select name="jenis_kelamin" required
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-400">
                                            <option value="Jantan" {{ $dombas->jenis_kelamin == 'Jantan' ? 'selected' : '' }}>Jantan</option>
                                            <option value="Betina" {{ $dombas->jenis_kelamin == 'Betina' ? 'selected' : '' }}>Betina</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- FOTO LAMA --}}
                                @if ($dombas->media->isNotEmpty())
                                    <div class="mb-4 bg-gray-50 p-3 rounded-lg border border-gray-200">
                                        <label class="block text-xs font-bold text-gray-700 mb-2">Foto Saat Ini (Klik untuk Hapus)</label>
                                        <div class="grid grid-cols-3 gap-2">
                                            @foreach ($dombas->media as $media)
                                                <div class="relative group" id="foto-lama-{{ $media->id }}">
                                                    <img src="{{ $media->url }}"
                                                         class="h-16 w-full object-cover rounded border">
                                                    <button type="button"
                                                            onclick="tandaiHapusFoto({{ $media->id }})"
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

                            {{-- ===== KOLOM KANAN ===== --}}
                            <div>
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Berat Awal</label>
                                        <input type="number" step="0.1" name="weight" value="{{ $dombas->weight }}" required
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-400">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Berat Sekarang</label>
                                        <input type="number" step="0.1" name="weight_now" value="{{ $dombas->weight_now }}" required
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-400">
                                    </div>
                                </div>

                                {{-- STATUS VAKSIN --}}
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Status Vaksin</label>
                                    <select id="faksin_status"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-400"
                                            required>
                                        <option value="Aktif"      {{ $dombas->faksin_status != 'Tidak Aktif' ? 'selected' : '' }}>Aktif</option>
                                        <option value="Tidak Aktif"{{ $dombas->faksin_status == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                                    </select>
                                </div>

                                <div class="mb-4" id="jenis_vaksin_wrapper"
                                     style="display: {{ $dombas->faksin_status != 'Tidak Aktif' ? 'block' : 'none' }};">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Vaksin</label>
                                    <select id="jenis_vaksin"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-400">
                                        <option value="Vaksin PMK"        {{ $dombas->faksin_status == 'Vaksin PMK'        ? 'selected' : '' }}>Vaksin PMK</option>
                                        <option value="Vaksin Antraks"    {{ $dombas->faksin_status == 'Vaksin Antraks'    ? 'selected' : '' }}>Vaksin Antraks</option>
                                        <option value="Vaksin Brucellosis"{{ $dombas->faksin_status == 'Vaksin Brucellosis'? 'selected' : '' }}>Vaksin Brucellosis</option>
                                    </select>
                                </div>
                                <input type="hidden" name="faksin_status" id="faksin_status_final"
                                       value="{{ $dombas->faksin_status }}">

                                {{-- STATUS KESEHATAN --}}
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Status Kesehatan</label>
                                    <select id="health_status_select"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-400"
                                            onchange="toggleOtherHealthStatus(this)" required>
                                        <option value="Sehat"       {{ $dombas->healt_status == 'Sehat'       ? 'selected' : '' }}>Sehat</option>
                                        <option value="Tidak Sehat" {{ $dombas->healt_status == 'Tidak Sehat' ? 'selected' : '' }}>Tidak Sehat</option>
                                        <option value="Lainnya"     {{ $dombas->healt_status != 'Sehat' && $dombas->healt_status != 'Tidak Sehat' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                    <input type="text" id="health_status_custom"
                                           placeholder="Jelaskan kondisi..."
                                           value="{{ $dombas->healt_status != 'Sehat' && $dombas->healt_status != 'Tidak Sehat' ? $dombas->healt_status : '' }}"
                                           class="mt-2 hidden w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-400"/>
                                    <input type="hidden" name="healt_status" id="health_status_final"
                                           value="{{ $dombas->healt_status }}">
                                </div>

                                {{-- UNTUK DIJUAL --}}
                                <div class="mb-4 p-4 border border-orange-200 rounded-lg bg-orange-50">
                                    <label class="block text-sm font-bold text-gray-800 mb-2">Untuk Dijual?</label>
                                    <select name="for_sale" x-model="forSale"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-400">
                                        <option value="yes">Ya, Dijual</option>
                                        <option value="no">Tidak</option>
                                    </select>
                                    <div class="mt-3" x-show="forSale === 'yes'">
                                        <label class="block text-xs font-bold text-gray-600 mb-1">Harga (Rp)</label>
                                        <input type="number" name="harga" value="{{ $dombas->harga }}"
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

                                    {{-- INFO LIMIT --}}
                                    <div class="mt-2 p-3 bg-orange-50 border border-orange-200 rounded-lg">
                                        <p class="text-xs text-orange-700 font-medium">• Maksimal total 10 foto per domba</p>
                                        <p class="text-xs text-orange-700 font-medium">• Ukuran maksimal 2 MB per foto</p>
                                        <p class="text-xs text-orange-700 font-medium">• Format yang didukung: JPG, JPEG, PNG</p>
                                    </div>

                                    <div id="preview-container-edit" class="grid grid-cols-3 gap-2 mt-3"></div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t border-gray-200 flex justify-end space-x-3">
                            <button type="button" @click="open = false"
                                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100 font-semibold transition-colors">
                                Batal
                            </button>
                            <button type="submit"
                                    class="px-6 py-2 bg-brand-orange text-white rounded-md hover:bg-orange-700 font-bold transition-colors shadow-md">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- IMAGE POPUP --}}
        <div id="imagePopup"
             class="fixed inset-0 hidden items-center justify-center bg-black bg-opacity-80 z-[100]"
             onclick="hideImagePopup()">
            <div class="relative max-w-4xl w-full p-4 flex justify-center" onclick="event.stopPropagation()">
                <button onclick="hideImagePopup()"
                        class="absolute top-0 right-0 text-white bg-red-500 hover:bg-red-600 rounded-full p-2
                               transform translate-x-2 -translate-y-2 shadow-lg transition-colors">
                    &times;
                </button>
                <img id="popupImage" src="" class="max-h-[85vh] w-auto object-contain rounded-lg shadow-2xl">
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // ── Image popup ──────────────────────────────────────────────────────
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

        // ── Tandai foto lama untuk dihapus ───────────────────────────────────
        function tandaiHapusFoto(mediaId) {
            document.getElementById('foto-lama-' + mediaId).remove();
            const input = document.createElement('input');
            input.type  = 'hidden';
            input.name  = 'hapus_media[]';
            input.value = mediaId;
            document.getElementById('wrapper-hapus-media').appendChild(input);
        }

        // ── Tambah baris foto baru secara dinamis ────────────────────────────
        (function () {
            const containerEdit     = document.getElementById('image-container-edit');
            const addBtnEdit        = document.getElementById('add-image-btn-edit');
            const previewContainerEdit = document.getElementById('preview-container-edit');

            if (!addBtnEdit) return;

            addBtnEdit.addEventListener('click', () => {
                const fotoLama = document.querySelectorAll('[id^="foto-lama-"]').length;
                const fotoBaru = containerEdit.querySelectorAll('input[type=file]').length;

                if ((fotoLama + fotoBaru) >= 10) {
                    alert('Maksimal total 10 foto per domba.');
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
                                      file:bg-orange-50 file:text-orange-700
                                      hover:file:bg-orange-100">
                        <button type="button"
                                class="remove-image-edit shrink-0 px-2.5 py-1 bg-red-500 text-white text-xs rounded-full hover:bg-red-600">
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

            containerEdit.addEventListener('change', (e) => {
                if (!e.target.classList.contains('image-input-edit')) return;
                const file = e.target.files[0];
                if (!file) return;

                // Validasi ukuran
                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran foto "' + file.name + '" melebihi 2 MB. Silakan pilih foto lain.');
                    e.target.value = '';
                    return;
                }

                renderPreviewEdit();
            });

            function renderPreviewEdit() {
                previewContainerEdit.innerHTML = '';
                containerEdit.querySelectorAll('.image-input-edit').forEach(input => {
                    if (!input.files.length) return;
                    const reader = new FileReader();
                    reader.onload = (ev) => {
                        previewContainerEdit.innerHTML += `
                            <div class="border rounded p-1 shadow-sm bg-white">
                                <img src="${ev.target.result}" class="w-full h-16 object-cover rounded">
                            </div>`;
                    };
                    reader.readAsDataURL(input.files[0]);
                });
            }
        })();

        // ── Vaksin status ────────────────────────────────────────────────────
        const statusSelect   = document.getElementById('faksin_status');
        const jenisWrapper   = document.getElementById('jenis_vaksin_wrapper');
        const jenisSelect    = document.getElementById('jenis_vaksin');
        const hiddenVaksin   = document.getElementById('faksin_status_final');

        function updateVaksin() {
            if (statusSelect.value === 'Aktif') {
                jenisWrapper.style.display = 'block';
                hiddenVaksin.value = jenisSelect.value || '';
            } else {
                jenisWrapper.style.display = 'none';
                hiddenVaksin.value = statusSelect.value;
            }
        }
        statusSelect.addEventListener('change', updateVaksin);
        jenisSelect.addEventListener('change', updateVaksin);
        updateVaksin();

        // ── Status kesehatan ─────────────────────────────────────────────────
        const healthSelect = document.getElementById('health_status_select');
        const healthCustom = document.getElementById('health_status_custom');
        const healthHidden = document.getElementById('health_status_final');

        function toggleOtherHealthStatus(el) {
            if (el.value === 'Lainnya') {
                healthCustom.classList.remove('hidden');
                healthHidden.value = healthCustom.value;
            } else {
                healthCustom.classList.add('hidden');
                healthHidden.value = el.value;
            }
        }
        healthCustom.addEventListener('input', function () {
            healthHidden.value = this.value;
        });
        toggleOtherHealthStatus(healthSelect);
    </script>
    @endpush
</x-admin-app-layout>