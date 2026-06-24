<x-owner-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Owner - List Kambing') }}
        </h2>
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 mt-10" x-data="{ open: false, kambing: null }">
        @if (session('success'))
            <div class="bg-green-500 text-white p-4 rounded-lg shadow-md mb-6">
                {{ session('success') }}
            </div>
        @endif
        
        <div class="flex flex-col">
            <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                    <div class="overflow-hidden">
                        <table id="Listkambinga" class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">ID</th>
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Foto</th>
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Pemilik</th>
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Nama</th>
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Jenis Kelamin</th>
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Umur</th>
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Berat</th>
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Status Vaksin</th>
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Dijual</th>
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Tanggal Dibuat</th>
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-center text-sm font-semibold text-gray-900 sm:pl-6">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach ($kambings as $kb)
                                    <tr>
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                            {{ $kb->id }}
                                        </td>
                                        
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-500 sm:pl-6">
                                            @php
                                                // Prioritaskan gambar primary, kalau tidak ada ambil file pertama
                                                $firstImage = $kb->primaryImage ?? $kb->media->first();
                                            @endphp

                                            @if($firstImage)
                                                <img src="{{ $firstImage->url }}" 
                                                     alt="Foto {{ $kb->name }}" 
                                                     class="h-12 w-12 object-cover rounded-md cursor-pointer hover:opacity-80 transition-opacity border border-gray-200"
                                                     onclick="showImagePopup('{{ $firstImage->url }}')">
                                            @elseif ($kb->image)
                                                {{-- Fallback (Cadangan) untuk data lama yang belum pakai tabel media --}}
                                                @php
                                                    $legacyImages = json_decode($kb->image, true);
                                                    $legacyFirst = is_array($legacyImages) && count($legacyImages) > 0 ? $legacyImages[0] : (is_string($kb->image) ? $kb->image : null);
                                                @endphp
                                                @if($legacyFirst)
                                                    <img src="{{ asset('storage/' . $legacyFirst) }}" 
                                                         alt="Foto {{ $kb->name }}" 
                                                         class="h-12 w-12 object-cover rounded-md cursor-pointer hover:opacity-80 transition-opacity border border-gray-200"
                                                         onclick="showImagePopup('{{ asset('storage/' . $legacyFirst) }}')">
                                                @else
                                                    <div class="h-12 w-12 bg-gray-100 border border-gray-200 rounded-md flex items-center justify-center text-[10px] text-gray-400">Kosong</div>
                                                @endif
                                            @else
                                                <div class="h-12 w-12 bg-gray-100 border border-gray-200 rounded-md flex items-center justify-center text-[10px] text-gray-400">
                                                    Kosong
                                                </div>
                                            @endif
                                        </td>

                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-500 sm:pl-6">
                                            {{ $kb->user ? $kb->user->name : '-' }}
                                        </td>
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-500 sm:pl-6">
                                            {{ $kb->name }}
                                        </td>
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-500 sm:pl-6">
                                            {{ $kb->jenis_kelamin }}
                                        </td>
                                        @php
                                            $umur = \Carbon\Carbon::parse($kb->tanggal_lahir)->diff(now());
                                            $tahun = $umur->y > 0 ? $umur->y . ' Tahun ' : '';
                                            $bulan = $umur->m > 0 ? $umur->m . ' Bulan ' : '';
                                            $hari  = $umur->d > 0 ? $umur->d . ' Hari' : '';
                                            $formatUmur = trim($tahun . $bulan . $hari);

                                            if ($formatUmur === '') {
                                                $formatUmur = 'Baru lahir';
                                            }
                                        @endphp
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-500 sm:pl-6">
                                            {{ $formatUmur }}
                                        </td>
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-500 sm:pl-6">
                                            {{ $kb->weight }} Kg
                                        </td>
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-500 sm:pl-6">
                                            {{ $kb->faksin_status }}
                                        </td>
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-center sm:pl-6">
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold text-white
                                                 @if ($kb->for_sale === 'yes') bg-green-500 
                                                 @elseif($kb->for_sale === 'no') bg-red-500 @endif">
                                                {{ strtoupper($kb->for_sale) }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-500 sm:pl-6">
                                            {{ $kb->created_at->format('Y-m-d') }}
                                        </td>

                                        <td class="whitespace-nowrap py-4 text-sm text-gray-500 px-2 flex justify-center gap-2 items-center h-full mt-2 border-none">
                                            <a href="{{ route('owner.kambing.show', $kb->id) }}">
                                                <button type="button" class="bg-blue-600 text-white p-2 rounded-md shadow-md hover:bg-blue-700 transition-colors">
                                                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m11.5 11.5 2.071 1.994M4 10h5m11 0h-1.5M12 7V4M7 7V4m10 3V4m-7 13H8v-2l5.227-5.292a1.46 1.46 0 0 1 2.065 2.065L10 17Zm-5 3h14a1 1 0 0 0 1-1V7a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1Z" />
                                                    </svg>
                                                </button>
                                            </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        <div class="mt-4">
                            {{ $kambings->links('pagination::tailwind') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="imagePopup" class="fixed inset-0 hidden items-center justify-center bg-black bg-opacity-80 z-50" onclick="hideImagePopup()">
        <div class="relative max-w-3xl w-full p-4 flex flex-col items-center" onclick="event.stopPropagation()">
            <button onclick="hideImagePopup()" class="absolute top-0 right-0 text-white bg-red-500 hover:bg-red-600 rounded-full p-1.5 transform translate-x-2 -translate-y-2 shadow-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            <img id="popupImage" src="" class="max-h-[85vh] w-auto object-contain rounded-lg shadow-2xl bg-white">
        </div>
    </div>

    @push('scripts')
    <script>
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        function showImagePopup(src) {
            document.getElementById('popupImage').src = src;
            document.getElementById('imagePopup').classList.remove('hidden');
            document.getElementById('imagePopup').classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function hideImagePopup() {
            document.getElementById('imagePopup').classList.add('hidden');
            document.getElementById('imagePopup').classList.remove('flex');
            document.body.style.overflow = '';
        }
    </script>
    @endpush
</x-owner-app-layout>