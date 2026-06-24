<x-owner-app-layout>
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
                                @forelse ($user->dombas as $db)
                                    <option value="{{ $db->id }}">{{ $db->id }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-6 py-4">{{ $user->dombas->count() }}</td>
                        <td class="px-6 py-4">{{ $user->address }}</td>
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
