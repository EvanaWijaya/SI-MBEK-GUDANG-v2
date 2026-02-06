<x-admin-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Admin - List Kambing') }}
        </h2>
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 mt-10" x-data="{ open: false, kambing: null }">
        @if (session('success'))
            <div class="bg-green-500 text-white p-4 rounded-lg shadow-md mb-6">
                {{ session('success') }}
            </div>
        @endif
        <button class="mt-8 bg-brand-orange hover:bg-orange-700 p-3 rounded-md mb-2 text-white"><a
                href="{{ route('admin.tambahkambing') }}">Tambah Kambing</a></button>
        <div class=" flex flex-col">
            <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                    <div class="overflow-hidden ">
                        <table id="Listkambinga" class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                        ID</th>
                                    <th scope="col"
                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                        Pemilik</th>
                                    <th scope="col"
                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                        Nama</th>
                                    <th scope="col"
                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                        Jenis Kelamin</th>
                                    <th scope="col"
                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                        Umur</th>
                                    <th scope="col"
                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                        Berat</th>
                                    <th scope="col"
                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                        Status Vaksin</th>
                                    <th scope="col"
                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                        Dijual</th>
                                    <th scope="col"
                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                        Tanggal Dibuat</th>
                                    <th scope="col"
                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach ($kambings as $kb)
                                    <tr>
                                        <td
                                            class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                            {{ $kb->id }}
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
                                            $tahun = $umur->y > 0 ? $umur->y . ' Tahun' : '';
                                            $bulan = $umur->m > 0 ? $umur->m . ' Bulan' : '';
                                            $formatUmur = trim($tahun . ' ' . $bulan);

                                            // Jika umur kosong semua (0 tahun 0 bulan), tampilkan "-" atau pesan lainnya
                                            if ($formatUmur === '') {
                                                $formatUmur = '-';
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
                                        <td
                                            class="text-sm text-gray-500  uppercase justify-center text-center
                                             @if ($kb->for_sale === 'yes') bg-green-500 text-white
                                             @elseif($kb->for_sale === 'no') bg-red-500 text-white @endif">
                                            {{ $kb->for_sale }}
                                        </td>

                                        </td>
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-500 sm:pl-6">
                                            {{ $kb->created_at->format('Y-m-d') }}
                                        </td>

                                        <td class="whitespace-nowrap py-4 text-sm text-gray-500 px-2   ">
                                            <a href="{{ route('admin.kambing.show', $kb->id) }}">
                                                <button type="button"
                                                    class="bg-blue-700 text-white p-2 rounded-md shadow-md hover:bg-blue-800">
                                                    <svg class="w-6 h-6 text-gray-800 dark:text-white"
                                                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                        width="24" height="24" fill="none"
                                                        viewBox="0 0 24 24">
                                                        <path stroke="currentColor" stroke-linecap="round"
                                                            stroke-linejoin="round" stroke-width="2"
                                                            d="m11.5 11.5 2.071 1.994M4 10h5m11 0h-1.5M12 7V4M7 7V4m10 3V4m-7 13H8v-2l5.227-5.292a1.46 1.46 0 0 1 2.065 2.065L10 17Zm-5 3h14a1 1 0 0 0 1-1V7a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1Z" />
                                                    </svg>

                                                </button></a>

                                            <button type="button"
                                                onclick="openModal('deleteModal-{{ $kb->id }}')"
                                                class="bg-red-600 px-2 py-2 rounded-md text-white">
                                                <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    fill="none" viewBox="0 0 24 24">
                                                    <path stroke="currentColor" stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="2"
                                                        d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z" />
                                                </svg>
                                            </button>

                                            <!-- Modal -->
                                            <div id="deleteModal-{{ $kb->id }}"
                                                class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
                                                <div
                                                    class="bg-white rounded-lg overflow-hidden shadow-xl max-w-md w-full p-6">
                                                    <h2 class="text-lg font-bold mb-4">Konfirmasi Hapus</h2>
                                                    <p class="mb-4">Apakah Anda yakin ingin menghapus item ini?</p>
                                                    <div class="flex justify-end">
                                                        <button type="button"
                                                            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded mr-2"
                                                            onclick="closeModal('deleteModal-{{ $kb->id }}')">Batal</button>
                                                        <form method="POST"
                                                            action="{{ route('admin.kambing.destroy', $kb->id) }}"
                                                            style="display: inline;">
                                                            @csrf
                                                            @method('delete')
                                                            <button type="submit"
                                                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">Hapus</button>
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
                            {{ $kambings->links('pagination::tailwind') }}
                        </div>
                        {{-- <div class="p-3 bg-gray-100 text-white">{{ $kambings->links() }}</div> --}}
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        {{-- <div x-show="open" id="poin"
            class="fixed z-50 inset-0 flex items-center justify-center  bg-black bg-opacity-50">
            <div class="bg-white p-4 rounded-lg w-1/3 max-h-screen overflow-y-auto">
                <h2 class="text-xl font-bold mb-4">Edit Kambing</h2>
                <form method="POST" :action="'{{ route('admin.kambings.update', '') }}/' + kambing.id"
                    enctype="multipart/form-data">

                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Nama Kambing</label>
                        <input type="text" name="name" x-model="kambing.name"
                            class="mt-1 px-3 py-2 border border-gray-300 rounded-md w-full">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Age</label>
                        <input type="text" name="age" x-model="kambing.age"
                            class="mt-1 px-3 py-2 border border-gray-300 rounded-md w-full">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Pemilik</label>
                        <select name="user_id" x-model="kambing.user_id"
                            class="mt-1 px-3 py-2 border border-gray-300 rounded-md w-full">
                            <option value="" disabled selected>Pilih Pemilik</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Goat Type</label>
                        <input type="text" name="type_goat" x-model="kambing.type_goat"
                            class="mt-1 px-3 py-2 border border-gray-300 rounded-md w-full">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Weight</label>
                        <input type="text" name="weight" x-model="kambing.weight"
                            class="mt-1 px-3 py-2 border border-gray-300 rounded-md w-full">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Vaccine Status</label>
                        <input type="text" name="faksin_status" x-model="kambing.faksin_status"
                            class="mt-1 px-3 py-2 border border-gray-300 rounded-md w-full">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Health Status</label>
                        <input type="text" name="healt_status" x-model="kambing.healt_status"
                            class="mt-1 px-3 py-2 border border-gray-300 rounded-md w-full">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Image</label>
                        <input type="file" name="image"
                            class="mt-1 px-3 py-2 border border-gray-300 rounded-md w-full">
                    </div>
                    <div class="flex justify-end">
                        <button type="button" @click="open = false"
                            class="bg-gray-500 text-white px-4 py-2 rounded-md">Cancel</button>
                        <button type="submit" class="bg-blue-700 text-white px-4 py-2 rounded-md ml-2">Save</button>
                    </div>
                </form>
            </div>
        </div> --}}
    </div>

    <!-- Popup Image -->
    <div id="imagePopup" class="fixed inset-0 hidden items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-4 rounded-lg">
            <img id="popupImage" src="" class="w-96 max-h-full rounded-md">
            <button onclick="hideImagePopup()" class="mt-4 bg-gray-500 text-white px-4 py-2 rounded-md">Close</button>
        </div>
    </div>

    {{-- TESTING  --}}




    <!-- Other scripts -->
    {{-- <link rel="stylesheet" href="https://cdn.datatables.net/2.1.2/css/dataTables.dataTables.css" />

    <script src="https://cdn.datatables.net/2.1.2/js/dataTables.js"></script>

    <script>
        $(document).ready(function() {
            $('#Listkambinga').DataTable({
                // pageLength: 5,
                // lengthMenu: [, 10, 25, 50],
                paging: true,
                searching: true,
                // info: true,
                autoWidth: false,
                // responsive: true,
            });
        });
    </script> --}}


    <script>
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        function showImagePopup(src) {
            document.getElementById('popupImage').src = src;
            document.getElementById('imagePopup').style.display = 'flex';
        }

        function hideImagePopup() {
            document.getElementById('imagePopup').style.display = 'none';
        }
    </script>



</x-admin-app-layout>
