<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Beranda') }} - {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Profile Card -->
            <div class="bg-white rounded-2xl shadow-lg p-8 mb-12 flex flex-col md:flex-row items-center gap-8">
                <!-- Profile Image -->
               <div class="shrink-0">
                    @php
                        // Deteksi gambar dari tabel media terlebih dahulu
                        $mediaPic = $user->primaryImage ?? $user->media->first();
                        
                        if ($mediaPic) {
                            $imgUrl = $mediaPic->url;
                        } elseif ($user->profile_picture) {
                            // Fallback jika masih ada data lama di kolom profile_picture
                            $imgUrl = asset('uploads/profilImage/' . $user->profile_picture);
                        } else {
                            // Gambar default
                            $imgUrl = asset('uploads/1721131815_default.png');
                        }
                    @endphp
                    
                    <img class="w-36 h-36 md:w-44 md:h-44 rounded-2xl object-cover border-4 border-brand-orange/30 shadow transition-all duration-300 hover:scale-105 bg-white"
                        src="{{ $imgUrl }}"
                        onerror="this.src='{{ asset('uploads/1721131815_default.png') }}'"
                        alt="Profile Image">
                </div>
                <!-- Profile Info -->
                <div class="flex-grow w-full">
                    <div class="mb-4">
                        <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-2 tracking-tight">
                            {{ $user->name }}</h1>
                        <div class="flex flex-col md:flex-row md:items-center gap-4 mb-3">
                            <div class="flex items-center text-gray-700">
                                <svg class="w-5 h-5 text-brand-orange mr-2" aria-hidden="true" fill="none"
                                    viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M18.427 14.768 17.2 13.542a1.733 1.733 0 0 0-2.45 0l-.613.613a1.732 1.732 0 0 1-2.45 0l-1.838-1.84a1.735 1.735 0 0 1 0-2.452l.612-.613a1.735 1.735 0 0 0 0-2.452L9.237 5.572a1.6 1.6 0 0 0-2.45 0c-3.223 3.2-1.702 6.896 1.519 10.117 3.22 3.221 6.914 4.745 10.12 1.535a1.601 1.601 0 0 0 0-2.456Z" />
                                </svg>
                                <span>{{ $user->phone_number ?? $user->no_telepon ?? '-' }}</span>
                            </div>
                            <div class="flex items-center text-gray-700">
                                <svg class="w-5 h-5 text-brand-orange mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M1.5 8.67v8.58a3 3 0 003 3h15a3 3 0 003-3V8.67l-8.928 5.493a3 3 0 01-3.144 0L1.5 8.67z" />
                                    <path
                                        d="M22.5 6.908V6.75a3 3 0 00-3-3h-15a3 3 0 00-3 3v.158l9.714 5.978a1.5 1.5 0 001.572 0L22.5 6.908z" />
                                </svg>
                                <span>{{ $user->email }}</span>
                            </div>
                        </div>
                        <div
                            class="inline-block bg-brand-orange/10 text-brand-orange px-5 py-2 rounded-full text-sm font-semibold shadow-sm">
                            Anggota sejak {{ $user->created_at->format('F Y') }}
                        </div>
                    </div>
                    <!-- Stats -->
                    <div class="flex flex-wrap gap-4 mt-6">
                        <!-- Total Ternak: full width on mobile, auto on desktop, max-w-xs biar tidak terlalu besar -->
                        <div
                            class="w-full md:w-auto md:max-w-xs bg-brand-orange/10 text-brand-orange px-5 py-3 rounded-xl flex items-center font-semibold shadow mb-2 md:mb-0">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M3.375 3C2.339 3 1.5 3.84 1.5 4.875v.75c0 1.036.84 1.875 1.875 1.875h17.25c1.035 0 1.875-.84 1.875-1.875v-.75C22.5 3.839 21.66 3 20.625 3H3.375z" />
                                <path fill-rule="evenodd"
                                    d="M3.087 9l.54 9.176A3 3 0 006.62 21h10.757a3 3 0 002.995-2.824L20.913 9H3.087zM12 10.5a.75.75 0 01.75.75v4.94l1.72-1.72a.75.75 0 111.06 1.06l-3 3a.75.75 0 01-1.06 0l-3-3a.75.75 0 111.06-1.06l1.72 1.72v-4.94a.75.75 0 01.75-.75z"
                                    clip-rule="evenodd" />
                            </svg>
                            Total Ternak: {{ $kambings->count() + $dombas->count() }}
                        </div>
                        <!-- Kambing & Domba: full width on mobile, inline on desktop, max-w-xs -->
                        <div class="w-full md:w-auto flex gap-4">
                            <a href="#kambing"
                                class="w-1/2 md:w-auto md:max-w-xs bg-white border border-brand-orange/30 text-brand-orange px-5 py-3 rounded-xl flex items-center font-semibold shadow hover:bg-brand-orange/10 transition">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12.75 12.75a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM7.5 15.75a.75.75 0 100-1.5.75.75 0 000 1.5zM8.25 17.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM9.75 15.75a.75.75 0 100-1.5.75.75 0 000 1.5zM10.5 17.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM12 15.75a.75.75 0 100-1.5.75.75 0 000 1.5zM12.75 17.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM14.25 15.75a.75.75 0 100-1.5.75.75 0 000 1.5zM15 17.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM16.5 15.75a.75.75 0 100-1.5.75.75 0 000 1.5zM15 12.75a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM16.5 13.5a.75.75 0 100-1.5.75.75 0 000 1.5z" />
                                    <path fill-rule="evenodd"
                                        d="M6.75 2.25A.75.75 0 017.5 3v1.5h9V3A.75.75 0 0118 3v1.5h.75a3 3 0 013 3v11.25a3 3 0 01-3 3H5.25a3 3 0 01-3-3V7.5a3 3 0 013-3H6V3a.75.75 0 01.75-.75zm13.5 9a1.5 1.5 0 00-1.5-1.5H5.25a1.5 1.5 0 00-1.5 1.5v7.5a1.5 1.5 0 001.5 1.5h13.5a1.5 1.5 0 001.5-1.5v-7.5z"
                                        clip-rule="evenodd" />
                                </svg>
                                Kambing: {{ $kambings->count() }}
                            </a>
                            <a href="#domba"
                                class="w-1/2 md:w-auto md:max-w-xs bg-white border border-brand-orange/30 text-brand-orange px-5 py-3 rounded-xl flex items-center font-semibold shadow hover:bg-brand-orange/10 transition">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12.75 12.75a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM7.5 15.75a.75.75 0 100-1.5.75.75 0 000 1.5zM8.25 17.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM9.75 15.75a.75.75 0 100-1.5.75.75 0 000 1.5zM10.5 17.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM12 15.75a.75.75 0 100-1.5.75.75 0 000 1.5zM12.75 17.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM14.25 15.75a.75.75 0 100-1.5.75.75 0 000 1.5zM15 17.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM16.5 15.75a.75.75 0 100-1.5.75.75 0 000 1.5zM15 12.75a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM16.5 13.5a.75.75 0 100-1.5.75.75 0 000 1.5z" />
                                    <path fill-rule="evenodd"
                                        d="M6.75 2.25A.75.75 0 017.5 3v1.5h9V3A.75.75 0 0118 3v1.5h.75a3 3 0 013 3v11.25a3 3 0 01-3 3H5.25a3 3 0 01-3-3V7.5a3 3 0 013-3H6V3a.75.75 0 01.75-.75zm13.5 9a1.5 1.5 0 00-1.5-1.5H5.25a1.5 1.5 0 00-1.5 1.5v7.5a1.5 1.5 0 001.5 1.5h13.5a1.5 1.5 0 001.5-1.5v-7.5z"
                                        clip-rule="evenodd" />
                                </svg>
                                Domba: {{ $dombas->count() }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kambing Section -->
            <div id="kambing" class="mb-16">
                <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
                    <div class="flex flex-wrap items-center justify-between mb-8 pb-4 border-b-2 border-brand-orange">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Daftar Kambing</h1>
                            <p class="text-gray-600">Total {{ $kambings->count() }} ekor kambing</p>
                        </div>
                        <span
                            class="inline-flex items-center px-4 py-2 rounded-full bg-brand-orange/10 text-brand-orange font-medium">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12.75 12.75a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM7.5 15.75a.75.75 0 100-1.5.75.75 0 000 1.5zM8.25 17.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM9.75 15.75a.75.75 0 100-1.5.75.75 0 000 1.5zM10.5 17.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM12 15.75a.75.75 0 100-1.5.75.75 0 000 1.5zM12.75 17.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM14.25 15.75a.75.75 0 100-1.5.75.75 0 000 1.5zM15 17.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM16.5 15.75a.75.75 0 100-1.5.75.75 0 000 1.5zM15 12.75a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM16.5 13.5a.75.75 0 100-1.5.75.75 0 000 1.5z" />
                                <path fill-rule="evenodd"
                                    d="M6.75 2.25A.75.75 0 017.5 3v1.5h9V3A.75.75 0 0118 3v1.5h.75a3 3 0 013 3v11.25a3 3 0 01-3 3H5.25a3 3 0 01-3-3V7.5a3 3 0 013-3H6V3a.75.75 0 01.75-.75zm13.5 9a1.5 1.5 0 00-1.5-1.5H5.25a1.5 1.5 0 00-1.5 1.5v7.5a1.5 1.5 0 001.5 1.5h13.5a1.5 1.5 0 001.5-1.5v-7.5z"
                                    clip-rule="evenodd" />
                            </svg>
                            Kambing
                        </span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        @foreach ($kambings as $kb)
                            <div
                                class="bg-gray-50 rounded-2xl p-6 shadow transition-all duration-300 hover:shadow-lg flex flex-col md:flex-row gap-6">
                                <!-- Image -->
                                <div class="md:w-2/5">
                                    <div class="aspect-[4/3] rounded-xl overflow-hidden">
                                        <@php
    $firstMedia = $kb->primaryImage ?? $kb->media->first();
    if ($firstMedia) {
        $imgPath = $firstMedia->url;
    } elseif (!empty($kb->image)) {
        $legacyImages = json_decode($kb->image, true);
        $legacyFirst = is_array($legacyImages) && count($legacyImages) > 0 ? $legacyImages[0] : $kb->image;
        $imgPath = asset('storage/' . $legacyFirst);
    } else {
        $imgPath = asset('uploads/default.png');
    }
@endphp
<img src="{{ $imgPath }}" class="w-full h-full object-cover transition-transform duration-300 hover:scale-110" alt="{{ $kb->name }}" onerror="this.src='{{ asset('uploads/default.png') }}'">
                                    </div>
                                </div>
                                <!-- Details -->
                                <div class="md:w-3/5 flex flex-col justify-between">
                                    <div>
                                        <div class="flex justify-between items-start mb-3">
                                            <h2 class="text-xl font-bold text-gray-800">{{ $kb->name }}</h2>
                                            <!-- Status Dijual -->
                                            <div class="text-right">
                                                @if ($kb->for_sale === 'yes')
                                                    <span
                                                        class="inline-flex items-center px-3 py-1 rounded-full bg-green-100 text-green-800 text-sm font-medium">
                                                        <svg class="w-4 h-4 mr-1" fill="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path
                                                                d="M10.464 8.746c.227-.18.497-.311.786-.394v2.795a2.252 2.252 0 01-.786-.393c-.394-.313-.546-.681-.546-1.004 0-.323.152-.691.546-1.004zM12.75 15.662v-2.824c.347.085.664.228.921.421.427.32.579.686.579.991 0 .305-.152.671-.579.991a2.534 2.534 0 01-.921.42z" />
                                                            <path fill-rule="evenodd"
                                                                d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zM12.75 6a.75.75 0 00-1.5 0v.816a3.836 3.836 0 00-1.72.756c-.712.566-1.112 1.35-1.112 2.178 0 .829.4 1.612 1.113 2.178.502.4 1.102.647 1.719.756v2.978a2.536 2.536 0 01-.921-.421l-.879-.66a.75.75 0 00-.9 1.2l.879.66c.533.4 1.169.645 1.821.75V18a.75.75 0 001.5 0v-.81a4.124 4.124 0 001.821-.749c.745-.559 1.179-1.344 1.179-2.191 0-.847-.434-1.632-1.179-2.191a4.122 4.122 0 00-1.821-.75V8.354c.29.082.559.213.786.393l.415.33a.75.75 0 00.933-1.175l-.415-.33a3.836 3.836 0 00-1.719-.755V6z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                        Dijual
                                                    </span>
                                                    <div class="mt-1 text-lg font-bold text-brand-orange">
                                                        Rp {{ number_format($kb->harga, 0, ',', '.') }}
                                                    </div>
                                                @else
                                                    <span
                                                        class="inline-flex items-center px-3 py-1 rounded-full bg-gray-100 text-gray-800 text-sm font-medium">
                                                        <svg class="w-4 h-4 mr-1" fill="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path fill-rule="evenodd"
                                                                d="M6.72 5.66l11.62 11.62A8.25 8.25 0 006.72 5.66zm10.56 12.68L5.66 6.72a8.25 8.25 0 0011.62 11.62zM5.105 5.106c3.807-3.808 9.98-3.808 13.788 0 3.808 3.807 3.808 9.98 0 13.788-3.807 3.808-9.98 3.808-13.788 0-3.808-3.807-3.808-9.98 0-13.788z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                        Tidak Dijual
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-3 mb-4">
                                            <div class="bg-white p-3 rounded-lg">
                                                <p class="text-xs text-gray-500">Jenis</p>
                                                <p class="font-medium">{{ $kb->type_goat }}</p>
                                            </div>
                                            <div class="bg-white p-3 rounded-lg">
                                                <p class="text-xs text-gray-500">Kelamin</p>
                                                <p class="font-medium">{{ $kb->jenis_kelamin }}</p>
                                            </div>
                                            <div class="bg-white p-3 rounded-lg">
                                                <p class="text-xs text-gray-500">Berat</p>
                                                <p class="font-medium">{{ $kb->weight }} kg</p>
                                            </div>
                                            <div class="bg-white p-3 rounded-lg">
                                                <p class="text-xs text-gray-500">Umur</p>
                                                <p class="font-medium">
                                                    @php
                                                        $umur = \Carbon\Carbon::parse($kb->tanggal_lahir)->diff(now());
                                                        $tahun = $umur->y > 0 ? $umur->y . ' Tahun' : '';
                                                        $bulan = $umur->m > 0 ? $umur->m . ' Bulan' : '';
                                                        $formatUmur = trim($tahun . ' ' . $bulan);
                                                        if ($formatUmur === '') {
                                                            $formatUmur = '-';
                                                        }
                                                    @endphp
                                                    {{ $formatUmur }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div class="bg-white p-3 rounded-lg">
                                                <p class="text-xs text-gray-500">Status Vaksin</p>
                                                <p
                                                    class="font-medium {{ Str::lower($kb->faksin_status) === 'aktif' ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ $kb->faksin_status }}
                                                </p>
                                            </div>

                                            <div class="bg-white p-3 rounded-lg">
                                                <p class="text-xs text-gray-500">Kesehatan</p>
                                                <p
                                                    class="font-medium {{ Str::lower($kb->healt_status) === 'sehat' ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ $kb->healt_status }}
                                                </p>
                                            </div>

                                            <div class="bg-white p-3 rounded-lg">
                                                <p class="text-xs text-gray-500">Tanggal Penitipan</p>
                                                <p class="font-medium">{{ $kb->created_at->format('d M Y') }}</p>
                                            </div>
                                            <div class="bg-white p-3 rounded-lg">
                                                <p class="text-xs text-gray-500">Lama Penitipan</p>
                                                <p class="font-medium">
                                                    @php
                                                        if ($kb->created_at) {
                                                            $totalHari = abs(now()->diffInDays($kb->created_at));
                                                            $tahun = floor($totalHari / 365);
                                                            $sisaHari = $totalHari % 365;
                                                            $bulan = floor($sisaHari / 30);
                                                            $hari = $sisaHari % 30;
                                                            $output = [];
                                                            if ($tahun > 0) {
                                                                $output[] = "{$tahun} tahun";
                                                            }
                                                            if ($bulan > 0) {
                                                                $output[] = "{$bulan} bulan";
                                                            }
                                                            if ($hari > 0) {
                                                                $output[] = "{$hari} hari";
                                                            }
                                                            echo implode(' ', $output);
                                                        }
                                                    @endphp
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Domba Section -->
            <div id="domba" class="mb-16">
                <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
                    <div class="flex flex-wrap items-center justify-between mb-8 pb-4 border-b-2 border-brand-orange">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Daftar Domba</h1>
                            <p class="text-gray-600">Total {{ $dombas->count() }} ekor domba</p>
                        </div>
                        <span
                            class="inline-flex items-center px-4 py-2 rounded-full bg-brand-orange/10 text-brand-orange font-medium">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12.75 12.75a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM7.5 15.75a.75.75 0 100-1.5.75.75 0 000 1.5zM8.25 17.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM9.75 15.75a.75.75 0 100-1.5.75.75 0 000 1.5zM10.5 17.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM12 15.75a.75.75 0 100-1.5.75.75 0 000 1.5zM12.75 17.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM14.25 15.75a.75.75 0 100-1.5.75.75 0 000 1.5zM15 17.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM16.5 15.75a.75.75 0 100-1.5.75.75 0 000 1.5zM15 12.75a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM16.5 13.5a.75.75 0 100-1.5.75.75 0 000 1.5z" />
                                <path fill-rule="evenodd"
                                    d="M6.75 2.25A.75.75 0 017.5 3v1.5h9V3A.75.75 0 0118 3v1.5h.75a3 3 0 013 3v11.25a3 3 0 01-3 3H5.25a3 3 0 01-3-3V7.5a3 3 0 013-3H6V3a.75.75 0 01.75-.75zm13.5 9a1.5 1.5 0 00-1.5-1.5H5.25a1.5 1.5 0 00-1.5 1.5v7.5a1.5 1.5 0 001.5 1.5h13.5a1.5 1.5 0 001.5-1.5v-7.5z"
                                    clip-rule="evenodd" />
                            </svg>
                            Domba
                        </span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        @foreach ($dombas as $db)
                            <div
                                class="bg-gray-50 rounded-2xl p-6 shadow transition-all duration-300 hover:shadow-lg flex flex-col md:flex-row gap-6">
                                <!-- Image -->
                                <div class="md:w-2/5">
                                    <div class="aspect-[4/3] rounded-xl overflow-hidden">
                                        @php
    $firstMedia = $db->primaryImage ?? $db->media->first();
    if ($firstMedia) {
        $imgPath = $firstMedia->url;
    } elseif (!empty($db->image)) {
        $legacyImages = json_decode($db->image, true);
        $legacyFirst = is_array($legacyImages) && count($legacyImages) > 0 ? $legacyImages[0] : $db->image;
        $imgPath = asset('storage/' . $legacyFirst);
    } else {
        $imgPath = asset('uploads/default.png');
    }
@endphp
<img src="{{ $imgPath }}" class="w-full h-full object-cover transition-transform duration-300 hover:scale-110" alt="{{ $db->name }}" onerror="this.src='{{ asset('uploads/default.png') }}'">
                                    </div>
                                </div>
                                <!-- Details -->
                                <div class="md:w-3/5 flex flex-col justify-between">
                                    <div>
                                        <div class="flex justify-between items-start mb-3">
                                            <h2 class="text-xl font-bold text-gray-800">{{ $db->name }}</h2>
                                            <!-- Status Dijual -->
                                            <div class="text-right">
                                                @if ($db->for_sale === 'yes')
                                                    <span
                                                        class="inline-flex items-center px-3 py-1 rounded-full bg-green-100 text-green-800 text-sm font-medium">
                                                        <svg class="w-4 h-4 mr-1" fill="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path
                                                                d="M10.464 8.746c.227-.18.497-.311.786-.394v2.795a2.252 2.252 0 01-.786-.393c-.394-.313-.546-.681-.546-1.004 0-.323.152-.691.546-1.004zM12.75 15.662v-2.824c.347.085.664.228.921.421.427.32.579.686.579.991 0 .305-.152.671-.579.991a2.534 2.534 0 01-.921.42z" />
                                                            <path fill-rule="evenodd"
                                                                d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zM12.75 6a.75.75 0 00-1.5 0v.816a3.836 3.836 0 00-1.72.756c-.712.566-1.112 1.35-1.112 2.178 0 .829.4 1.612 1.113 2.178.502.4 1.102.647 1.719.756v2.978a2.536 2.536 0 01-.921-.421l-.879-.66a.75.75 0 00-.9 1.2l.879.66c.533.4 1.169.645 1.821.75V18a.75.75 0 001.5 0v-.81a4.124 4.124 0 001.821-.749c.745-.559 1.179-1.344 1.179-2.191 0-.847-.434-1.632-1.179-2.191a4.122 4.122 0 00-1.821-.75V8.354c.29.082.559.213.786.393l.415.33a.75.75 0 00.933-1.175l-.415-.33a3.836 3.836 0 00-1.719-.755V6z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                        Dijual
                                                    </span>
                                                    <div class="mt-1 text-lg font-bold text-brand-orange">
                                                        Rp {{ number_format($db->harga, 0, ',', '.') }}
                                                    </div>
                                                @else
                                                    <span
                                                        class="inline-flex items-center px-3 py-1 rounded-full bg-gray-100 text-gray-800 text-sm font-medium">
                                                        <svg class="w-4 h-4 mr-1" fill="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path fill-rule="evenodd"
                                                                d="M6.72 5.66l11.62 11.62A8.25 8.25 0 006.72 5.66zm10.56 12.68L5.66 6.72a8.25 8.25 0 0011.62 11.62zM5.105 5.106c3.807-3.808 9.98-3.808 13.788 0 3.808 3.807 3.808 9.98 0 13.788-3.807 3.808-9.98 3.808-13.788 0-3.808-3.807-3.808-9.98 0-13.788z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                        Tidak Dijual
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-3 mb-4">
                                            <div class="bg-white p-3 rounded-lg">
                                                <p class="text-xs text-gray-500">Jenis</p>
                                                <p class="font-medium">{{ $db->type_domba }}</p>
                                            </div>
                                            <div class="bg-white p-3 rounded-lg">
                                                <p class="text-xs text-gray-500">Kelamin</p>
                                                <p class="font-medium">{{ $db->jenis_kelamin }}</p>
                                            </div>
                                            <div class="bg-white p-3 rounded-lg">
                                                <p class="text-xs text-gray-500">Berat</p>
                                                <p class="font-medium">{{ $db->weight }} kg</p>
                                            </div>
                                            <div class="bg-white p-3 rounded-lg">
                                                <p class="text-xs text-gray-500">Umur</p>
                                                <p class="font-medium">
                                                    @php
                                                        $umur = \Carbon\Carbon::parse($db->tanggal_lahir)->diff(now());
                                                        $tahun = $umur->y > 0 ? $umur->y . ' Tahun' : '';
                                                        $bulan = $umur->m > 0 ? $umur->m . ' Bulan' : '';
                                                        $formatUmur = trim($tahun . ' ' . $bulan);
                                                        if ($formatUmur === '') {
                                                            $formatUmur = '-';
                                                        }
                                                    @endphp
                                                    {{ $formatUmur }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div class="bg-white p-3 rounded-lg">
                                                <p class="text-xs text-gray-500">Status Vaksin</p>
                                                <p
                                                    class="font-medium {{ Str::lower($db->faksin_status) === 'aktif' ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ $db->faksin_status }}
                                                </p>
                                            </div>

                                            <div class="bg-white p-3 rounded-lg">
                                                <p class="text-xs text-gray-500">Kesehatan</p>
                                                <p
                                                    class="font-medium {{ Str::lower($db->healt_status) === 'sehat' ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ $db->healt_status }}
                                                </p>
                                            </div>

                                            <div class="bg-white p-3 rounded-lg">
                                                <p class="text-xs text-gray-500">Tanggal Penitipan</p>
                                                <p class="font-medium">{{ $db->created_at->format('d M Y') }}</p>
                                            </div>
                                            <div class="bg-white p-3 rounded-lg">
                                                <p class="text-xs text-gray-500">Lama Penitipan</p>
                                                <p class="font-medium">
                                                    @php
                                                        if ($db->created_at) {
                                                            $totalHari = abs(now()->diffInDays($db->created_at));
                                                            $tahun = floor($totalHari / 365);
                                                            $sisaHari = $totalHari % 365;
                                                            $bulan = floor($sisaHari / 30);
                                                            $hari = $sisaHari % 30;
                                                            $output = [];
                                                            if ($tahun > 0) {
                                                                $output[] = "{$tahun} tahun";
                                                            }
                                                            if ($bulan > 0) {
                                                                $output[] = "{$bulan} bulan";
                                                            }
                                                            if ($hari > 0) {
                                                                $output[] = "{$hari} hari";
                                                            }
                                                            echo implode(' ', $output);
                                                        }
                                                    @endphp
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if (session('status') === 'profile-updated')
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Profil berhasil diperbarui.',
            timer: 3000,
            showConfirmButton: false
        });
    </script>
@endif
