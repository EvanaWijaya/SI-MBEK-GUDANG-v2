<x-admin-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Admin - List Domba') }}
        </h2>
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 mt-10" x-data="{ open: false, domba: null }">
        @if (session('success'))
            <div class="bg-green-500 text-white p-4 rounded-lg shadow-md mb-6">
                {{ session('success') }}
            </div>
        @endif
        
        <button class="mt-8 bg-brand-orange hover:bg-orange-700 p-3 rounded-md mb-2 text-white">
            <a href="{{ route('admin.tambahdomba') }}">Tambah Domba</a>
        </button>
        
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
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-center text-sm font-semibold text-gray-900 sm:pl-6">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach ($dombas as $db)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                            {{ $db->id }}
                                        </td>
                                        
                                        {{-- LOGIKA MENGAMBIL FOTO DARI TABEL MEDIA --}}
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-500 sm:pl-6">
                                            @php
                                                // Prioritaskan gambar primary, kalau tidak ada ambil file pertama
                                                $firstImage = $db->primaryImage ?? $db->media->first();
                                            @endphp

                                            @if($firstImage)
                                                <img src="{{ $firstImage->url }}" 
                                                     alt="Foto {{ $db->name }}" 
                                                     class="h-12 w-12 object-cover rounded-md cursor-pointer hover:opacity-80 transition-opacity border border-gray-200"
                                                     onclick="showImagePopup('{{ $firstImage->url }}')">
                                            @elseif ($db->image)
                                                {{-- Fallback untuk data lama --}}
                                                <img src="{{ asset('storage/' . $db->image) }}" 
                                                     alt="Foto {{ $db->name }}" 
                                                     class="h-12 w-12 object-cover rounded-md cursor-pointer hover:opacity-80 transition-opacity border border-gray-200"
                                                     onclick="showImagePopup('{{ asset('storage/' . $db->image) }}')">
                                            @else
                                                <div class="h-12 w-12 bg-gray-100 border border-gray-200 rounded-md flex items-center justify-center text-[10px] text-gray-400">
                                                    Kosong
                                                </div>
                                            @endif
                                        </td>

                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-500 sm:pl-6">
                                            {{ $db->user ? $db->user->name : '-' }}
                                        </td>
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-500 sm:pl-6">
                                            <span class="font-bold text-gray-800">{{ $db->name }}</span>
                                        </td>
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-500 sm:pl-6">
                                            {{ $db->jenis_kelamin }}
                                        </td>
                                        @php
                                            $umur = \Carbon\Carbon::parse($db->tanggal_lahir)->diff(now());
                                            $tahun = $umur->y > 0 ? $umur->y . ' Tahun ' : '';
                                            $bulan = $umur->m > 0 ? $umur->m . ' Bulan ' : '';
                                            $hari  = $umur->d > 0 ? $umur->d . ' Hari' : '';
                                            $formatUmur = trim($tahun . $bulan . $hari) ?: 'Baru lahir';
                                        @endphp
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-500 sm:pl-6">
                                            {{ $formatUmur }}
                                        </td>
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-500 sm:pl-6">
                                            {{ $db->weight }} Kg
                                        </td>
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-500 sm:pl-6">
                                            {{ $db->faksin_status }}
                                        </td>
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-center sm:pl-6">
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold text-white
                                                 @if ($db->for_sale === 'yes') bg-green-500 
                                                 @elseif($db->for_sale === 'no') bg-red-500 @endif">
                                                {{ strtoupper($db->for_sale) }}
                                            </span>
                                        </td>

                                        <td class="whitespace-nowrap py-4 text-sm text-gray-500 px-2 flex justify-center gap-2 items-center h-full mt-2 border-none">
                                            <a href="{{ route('admin.domba.show', $db->id) }}">
                                                <button type="button" class="bg-blue-600 text-white p-2 rounded-md shadow-md hover:bg-blue-700 transition-colors" title="Lihat Detail">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m11.5 11.5 2.071 1.994M4 10h5m11 0h-1.5M12 7V4M7 7V4m10 3V4m-7 13H8v-2l5.227-5.292a1.46 1.46 0 0 1 2.065 2.065L10 17Zm-5 3h14a1 1 0 0 0 1-1V7a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1Z" /></svg>
                                                </button>
                                            </a>

                                            <button type="button" onclick="openModal('deleteModal-{{ $db->id }}')"
                                                class="bg-red-500 p-2 rounded-md text-white shadow-md hover:bg-red-600 transition-colors">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z" /></svg>
                                            </button>

                                            <div id="deleteModal-{{ $db->id }}" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
                                                <div class="bg-white rounded-lg overflow-hidden shadow-xl max-w-sm w-full p-6 text-center">
                                                    <h2 class="text-xl font-bold mb-2 text-gray-800">Konfirmasi Hapus</h2>
                                                    <p class="mb-6 text-gray-600 whitespace-normal">Apakah Anda yakin ingin menghapus <b>{{ $db->name }}</b>?</p>
                                                    <div class="flex justify-center gap-3">
                                                        <button type="button" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold px-4 py-2 rounded-lg transition-colors"
                                                            onclick="closeModal('deleteModal-{{ $db->id }}')">Batal</button>
                                                        <form method="POST" action="{{ route('admin.domba.destroy', $db->id) }}">
                                                            @csrf
                                                            @method('delete')
                                                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded-lg transition-colors">Hapus</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        <div class="mt-4">
                            {{ $dombas->links('pagination::tailwind') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="imagePopup" class="fixed inset-0 bg-black bg-opacity-80 hidden z-[100] items-center justify-center p-4">
        <div class="relative max-w-4xl w-full">
            <button onclick="hideImagePopup()" class="absolute -top-10 right-0 text-white hover:text-gray-300">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            <img id="popupImage" src="" class="max-h-[85vh] mx-auto rounded-lg shadow-2xl object-contain">
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
</x-admin-app-layout>