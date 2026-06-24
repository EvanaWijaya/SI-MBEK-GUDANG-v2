<x-admin-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">{{ __('Tambah Admin Baru') }}</h2>
    </x-slot>

    <div class="container mx-auto mt-10 px-4">
        
        {{-- Pesan error global di bagian atas tetap dipertahankan --}}
        @if ($errors->any())
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                <span class="font-bold block mb-1">Terjadi kesalahan input:</span>
                <ul class="list-disc pl-5 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.admins.store') }}" method="POST" novalidate class="bg-white shadow-md rounded-lg p-8 border border-gray-200">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- Kolom Kiri --}}
                <div>
                    {{-- Nama Lengkap --}}
                    <label class="block font-bold mb-2 {{ $errors->has('name') ? 'text-red-600' : 'text-gray-700' }}">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" 
                        class="w-full border {{ $errors->has('name') ? 'border-red-400 bg-red-50 text-red-900 focus:ring-red-500 focus:border-red-400' : 'border-gray-300 focus:ring-orange-500 focus:border-brand-orange' }} rounded p-2.5 text-sm transition-all" 
                        placeholder="Masukkan nama lengkap" required>
                    @error('name')
                        <p class="mt-1 text-xs text-red-500 font-semibold">{{ $message }}</p>
                    @enderror
                    
                    {{-- Alamat Email --}}
                    <label class="block font-bold mt-4 mb-2 {{ $errors->has('email') ? 'text-red-600' : 'text-gray-700' }}">Alamat Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" 
                        class="w-full border {{ $errors->has('email') ? 'border-red-400 bg-red-50 text-red-900 focus:ring-red-500 focus:border-red-400' : 'border-gray-300 focus:ring-orange-500 focus:border-brand-orange' }} rounded p-2.5 text-sm transition-all" 
                        placeholder="admin@contoh.com" required>
                    @error('email')
                        <p class="mt-1 text-xs text-red-500 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kolom Kanan --}}
                <div>
                    {{-- Password Sementara --}}
                    <label class="block font-bold mb-2 {{ $errors->has('password') ? 'text-red-600' : 'text-gray-700' }}">Kata Sandi Sementara</label>
                    <input type="password" name="password" autocomplete="new-password" 
                        class="w-full border {{ $errors->has('password') ? 'border-red-400 bg-red-50 text-red-900 focus:ring-red-500 focus:border-red-400' : 'border-gray-300 focus:ring-orange-500 focus:border-brand-orange' }} rounded p-2.5 text-sm transition-all" 
                        placeholder="Minimal 8 karakter" required>
                    <p class="text-xs text-gray-400 mt-1 mb-3">Admin baru akan diminta untuk mengubah kata sandi saat login pertama kali.</p>
                    @error('password')
                        <p class="mt-1 text-xs text-red-500 font-semibold">{{ $message }}</p>
                    @enderror
                    
                    {{-- Konfirmasi Password --}}
                    <label class="block font-bold mb-2 {{ $errors->has('password') ? 'text-red-600' : 'text-gray-700' }}">Konfirmasi Kata Sandi</label>
                    <input type="password" name="password_confirmation" autocomplete="new-password" 
                        class="w-full border {{ $errors->has('password') ? 'border-red-400 bg-red-50 text-red-900 focus:ring-red-500 focus:border-red-400' : 'border-gray-300 focus:ring-orange-500 focus:border-brand-orange' }} rounded p-2.5 text-sm transition-all" 
                        placeholder="Ketik ulang password" required>
                </div>
            </div>
            
            <div class="mt-8 pt-4 border-t border-gray-100 flex items-center">
                <button type="submit" class="bg-brand-orange text-white px-6 py-2.5 rounded font-bold hover:bg-orange-700 shadow transition-colors">Simpan</button>
                <a href="{{ route('admin.admins.index') }}" class="ml-4 text-gray-600 hover:text-gray-900 font-medium transition-colors">Batal</a>
            </div>
        </form>
    </div>
</x-admin-app-layout>