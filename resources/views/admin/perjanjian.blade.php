<x-admin-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl  leading-tight">
            {{ __('Admin - Perjanjian') }}
        </h2>
    </x-slot>

    <div x-data="{ open: false }" class="p-6">
        <!-- Button Tambah Perjanjian -->
        <div class="mb-4 mt-10 ps-a8">
            <button @click="open = true" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Tambah Perjanjian
            </button>
        </div>

        <!-- List Perjanjian -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg ">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Goat ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Annual Offspring</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    {{-- @foreach ($perjanjians as $perjanjian)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $perjanjian->user_id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $perjanjian->goat_id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $perjanjian->start_date }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $perjanjian->end_date }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $perjanjian->annual_offspring }}</td>
                    </tr>
                    @endforeach --}}
                </tbody>
            </table>
        </div>

        <!-- Popup Form -->
        {{-- <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75" style="display: none;">
            <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
                <span @click="open = false" class="absolute top-5 right-5 text-gray-700 text-3xl cursor-pointer">&times;</span>
                <form action="/" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="user_id" class="block text-gray-700 font-bold mb-2">User ID</label>
                        <input type="number" id="user_id" name="user_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>
                    <div class="mb-4">
                        <label for="goat_id" class="block text-gray-700 font-bold mb-2">Goat ID</label>
                        <input type="number" id="goat_id" name="goat_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>
                    <div class="mb-4">
                        <label for="start_date" class="block text-gray-700 font-bold mb-2">Start Date</label>
                        <input type="date" id="start_date" name="start_date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>
                    <div class="mb-4">
                        <label for="end_date" class="block text-gray-700 font-bold mb-2">End Date</label>
                        <input type="date" id="end_date" name="end_date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>
                    <div class="mb-4">
                        <label for="annual_offspring" class="block text-gray-700 font-bold mb-2">Annual Offspring</label>
                        <input type="number" id="annual_offspring" name="annual_offspring" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div> --}}
                    {{-- <div class="mb-4">
                        <label for="create_at" class="block text-gray-700 font-bold mb-2">Created At</label>
                        <input type="datetime-local" id="create_at" name="create_at" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>
                    <div class="mb-4">
                        <label for="update_at" class="block text-gray-700 font-bold mb-2">Updated At</label>
                        <input type="datetime-local" id="update_at" name="update_at" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div> --}}
                    {{-- <div class="flex items-center justify-between">
                        <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Simpan Perjanjian
                        </button>
                        <button @click="open = false"  type="submit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Close
                        </button>
                    </div>
                </form>
            </div>
        </div> --}}
    </div>
</x-admin-app-layout>
