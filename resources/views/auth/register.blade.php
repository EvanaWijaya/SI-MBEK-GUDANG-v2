<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-[#FEF1DC] px-4 py-8 overflow-y-auto">
        <div class="flex flex-col md:flex-row bg-white shadow-lg rounded-[20px] overflow-hidden w-full max-w-4xl">

            <!-- Left: Register Form -->
            <div class="w-full md:w-1/2 p-4 sm:p-6 bg-[#FFF9EE] shadow-xl ring-1 ring-orange-200">
                <!-- Logo & Title -->
                <div class="flex items-center mb-4">
                    <img src="{{ asset('logo/logosiembek.png') }}" alt="Logo SI MBEK" class="h-8 w-8 me-2">
                    <span class="font-bold text-lg">SI MBEK</span>
                </div>

                <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4">Daftar</h2>

                <!-- Form Start -->
                <form method="POST" action="{{ route('register') }}" autocomplete="off">
                    @csrf

                    <div class="space-y-3 text-xs">
                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('Nama Lengkap')" class="text-xs" />
                            <x-text-input id="name"
                                class="mt-1 block w-full py-1.5 text-xs focus:ring-orange-400 focus:border-orange-400"
                                type="text" name="name" :value="old('name')" placeholder="Nama Lengkap" required
                                autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-1 text-xs" />
                        </div>

                        <!-- Email -->
                        <div>
                            <x-input-label for="email" :value="__('Email')" class="text-xs" />
                            <x-text-input id="email"
                                class="mt-1 block w-full py-1.5 text-xs focus:ring-orange-400 focus:border-orange-400"
                                type="email" name="email" :value="old('email')" placeholder="contoh@gmail.com" required
                                autocomplete="off" />
                            <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs" />
                        </div>

                        <!-- Alamat -->
                        <div>
                            <x-input-label for="alamat" :value="__('Alamat')" class="text-xs" />
                            <x-text-input id="alamat"
                                class="mt-1 block w-full py-1.5 text-xs focus:ring-orange-400 focus:border-orange-400"
                                type="text" name="alamat" :value="old('alamat')" placeholder="Alamat lengkap"
                                required />
                            <x-input-error :messages="$errors->get('alamat')" class="mt-1 text-xs" />
                        </div>

                        <!-- No Telepon -->
                        <div>
                            <x-input-label for="no_telepon" :value="__('No Telepon')" class="text-xs" />
                            <x-text-input id="no_telepon"
                                class="mt-1 block w-full py-1.5 text-xs focus:ring-orange-400 focus:border-orange-400"
                                type="number" name="no_telepon" :value="old('no_telepon')" placeholder="08XXXXXXXXXX"
                                required />
                            <x-input-error :messages="$errors->get('no_telepon')" class="mt-1 text-xs" />
                        </div>

                        <!-- Provinsi -->
                        <div class="mt-4">
                            <x-input-label for="provinsi" value="Provinsi" />
                            <x-text-input id="provinsi" name="provinsi" type="text" class="block mt-1 w-full"
                                value="{{ old('provinsi') }}" required />
                            <x-input-error :messages="$errors->get('provinsi')" class="mt-2" />
                        </div>

                        <!-- Kota -->
                        <div class="mt-4">
                            <x-input-label for="kota" value="Kota / Kabupaten" />
                            <x-text-input id="kota" name="kota" type="text" class="block mt-1 w-full"
                                value="{{ old('kota') }}" required />
                            <x-input-error :messages="$errors->get('kota')" class="mt-2" />
                        </div>


                        <!-- Password -->
                        <div>
                            <x-input-label for="password" :value="__('Kata Sandi')" class="text-xs" />
                            <x-text-input id="password"
                                class="mt-1 block w-full py-1.5 text-xs focus:ring-orange-400 focus:border-orange-400"
                                type="password" name="password" placeholder="Kata Sandi" required autocomplete="off" />
                            <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />
                            <span class="fas fa-eye" id="icon-pw"></span>
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <x-input-label for="password_confirmation" :value="__('Konfirmasi Kata Sandi')"
                                class="text-xs" />
                            <x-text-input id="password_confirmation"
                                class="mt-1 block w-full py-1.5 text-xs focus:ring-orange-400 focus:border-orange-400"
                                type="password" name="password_confirmation" placeholder="Ulangi Kata Sandi" required />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-xs" />
                            <span class="fas fa-eye" id="icon-pw"></span>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="mt-3">
                        <x-primary-button
                            class="w-full bg-brand-orange hover:bg-orange-700 text-white text-sm py-1.5 justify-center">
                            {{ __('Daftar') }}
                        </x-primary-button>
                    </div>

                    <!-- Already have account -->
                    <div class="text-center text-xs text-gray-600 mt-4">
                        Sudah punya akun?
                        <a href="{{ route('login') }}"
                            class="text-[#E28700] font-semibold hover:text-[#cf7700]">Masuk</a>
                    </div>
                </form>
            </div>

            <!-- Right: Goat Image -->
            <div class="hidden md:flex relative z-10 bg-[#FFF3E4] p-10 items-center justify-center w-full md:w-1/2">
                <div class="w-64 h-64 bg-orange-300 rounded-full shadow-xl relative z-10"></div>
                <img src="{{ asset('logo/kambing6 1.png') }}" alt="Kambing Raman Farm"
                    class="absolute w-80 h-auto z-20 -translate-y-6" />
            </div>
        </div>
    </div>
    <script>
        // Simpan password setelah user submit form
        document.querySelector('form').addEventListener('submit', function () {
            const email = document.querySelector('#email').value;
            const password = document.querySelector('#password').value;

            localStorage.setItem('autofill_email', email);
        });
    </script>

</x-guest-layout>