<x-admin-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl  leading-tight">
            {{ __('Admin - Dashboard') }}
        </h2>
    </x-slot>

    <div class="w-full bg-gray-500 p-6">
        <div class="container mx-auto p-8">
            <div class="w-full mx-auto bg-white rounded shadow p-8">
                <div class="mx-16 py-4 px-8 text-black text-xl font-bold border-b border-gray-500">Student Application</div>

                <div class="container mx-auto mt-10">
                    <div class="max-w-4xl mx-auto bg-white p-5 rounded-md shadow-md">
                        <h2 class="text-2xl font-semibold mb-6">Add New Goat</h2>

                        @if (session('success'))
                            <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('admin.goats.save') }}" method="POST">
                            @csrf

                            <div class="grid grid-cols-2 gap-6">
                                <div class="col-span-1">
                                    <div class="mb-4">
                                        <label for="user_id" class="block text-gray-700">User</label>
                                        <select name="user_id" id="user_id"
                                            class="w-full mt-2 p-2 border border-gray-300 rounded" required>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('user_id')
                                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="mb-4">
                                        <label for="name" class="block text-gray-700">Nama Kambing</label>
                                        <input type="text" name="name" id="name"
                                            class="w-full mt-2 p-2 border border-gray-300 rounded" required>
                                        @error('name')
                                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="mb-4">
                                        <label for="age" class="block text-gray-700">Umur</label>
                                        <input type="number" name="age" id="age"
                                            class="w-full mt-2 p-2 border border-gray-300 rounded" required>
                                        @error('age')
                                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-span-1">
                                    <div class="mb-4">
                                        <label for="weight" class="block text-gray-700">Berat</label>
                                        <input type="number" step="0.01" name="weight" id="weight"
                                            class="w-full mt-2 p-2 border border-gray-300 rounded" required>
                                        @error('weight')
                                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="mb-4">
                                        <label for="health_status" class="block text-gray-700">Health Status</label>
                                        <input type="text" name="health_status" id="health_status"
                                            class="w-full mt-2 p-2 border border-gray-300 rounded" required>
                                        @error('health_status')
                                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="mb-4">
                                        <label for="for_sale" class="block text-gray-700">For Sale</label>
                                        <select name="for_sale" id="for_sale"
                                            class="w-full mt-2 p-2 border border-gray-300 rounded" required>
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                        @error('for_sale')
                                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end mt-6">
                                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Add Goat</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-app-layout>
