<x-admin-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight text-brand-orange">
            {{ __('Admin - List Penitip') }}
        </h2>
    </x-slot>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9fafb;
        }
        
        .table-header {
            background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
        }

        .table-row:hover {
            background-color: #fff2e6;
            transition: background-color 0.3s ease;
        }

        .message-success {
            background-color: #4ade80; /* Green */
            color: white;
        }

        /* Add any additional custom styles here */
    </style>

    <div class="container overflow-x-auto p-7 my-10">
        @if (session('success'))
            <div class="message-success p-4 rounded-lg shadow-md mb-6">
                {{ session('success') }}
            </div>
        @endif

        <table id="Penitipkambing" class="w-full text-sm text-left text-black border-2 rounded shadow-lg overflow-hidden">
            <thead class="text-xs text-black uppercase table-header">
                <tr>
                    <th scope="col" class="px-6 py-3">Profil</th>
                    <th scope="col" class="px-6 py-3">Nama</th>
                    <th scope="col" class="px-6 py-3">Email</th>
                    <!-- Tambah kolom Domba -->
                    <th scope="col" class="px-6 py-3">ID Kambing</th>
                    <th scope="col" class="px-6 py-3">JLh Kambing</th>
                    <th scope="col" class="px-6 py-3">ID Domba</th>
                    <th scope="col" class="px-6 py-3">JLh Domba</th>
                    <th scope="col" class="px-6 py-3">Alamat</th>
                    <th scope="col" class="px-6 py-3">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($users as $user)
                    <tr class="table-row">
                        <td class="px-2 py-4">
                            <img src="{{ $user->profile_picture ? asset('uploads/profilImage/' . $user->profile_picture) : asset('uploads/profilImage/default.png') }}"
                                loading="lazy"
                                alt="{{ $user->name }}" class="h-20 w-20 object-cover object-center rounded-full" />
                        </td>
                        <td class="px-6 py-4">{{ $user->name }}</td>
                        <td class="px-6 py-4">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            <select class="form-select rounded-md">
                                @foreach ($user->kambings as $kb)
                                    <option value="{{ $kb->id }}">{{ $kb->id }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-6 py-4">{{ $user->kambings->count() }}</td>
                        <td class="px-6 py-4">
                            <select class="form-select rounded-md">
                                @foreach ($user->domba ?? [] as $db)
                                    <option value="{{ $db->id }}">{{ $db->id }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-6 py-4">{{ $user->domba->count() }}</td>
                        <td class="px-6 py-4">{{ $user->alamat }}</td>
                        <td class="px-6 py-4">
                            <button type="button" onclick="openModal('deleteModal-{{ $user->id }}')"
                                class="bg-red-600 px-2 py-2 rounded-md text-white">
                                <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                        viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z" />
                                    </svg>

                            </button>
                             <!-- Modal -->
                            <div id="deleteModal-{{ $user->id }}"
                                class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
                                <div class="bg-white rounded-lg overflow-hidden shadow-xl max-w-md w-full p-6">
                                    <h2 class="text-lg font-bold mb-4">Konfirmasi Hapus</h2>
                                    <p class="mb-4">Apakah Anda yakin ingin menghapus pengguna ini?</p>
                                    <div class="flex justify-end">
                                        <button type="button"
                                            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded mr-2"
                                            onclick="closeModal('deleteModal-{{ $user->id }}')">Batal</button>
                                        <form method="POST" action="{{ route('admin.profile.destroyuser', $user->id) }}"
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

        {{ $users->links() }}
    </div>

   <script>
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }
    </script>
</x-admin-app-layout>
