<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-[#FEF1DC] px-4">
        <div class="flex flex-col md:flex-row bg-white shadow-lg rounded-[20px] overflow-hidden w-full max-w-4xl">
            <!-- Left: Login Form -->
            <div class="w-full md:w-1/2 p-8 md:p-10 bg-[#FFF7EC] shadow-xl rounded-b-[20px] md:rounded-[20px_0_0_20px] ring-1 ring-orange-200">
                <!-- Logo & Title -->
                <div class="flex items-center mb-6">
                    <img src="{{ asset('logo/logosiembek.png') }}" alt="Logo SI MBEK" class="h-8 w-8 me-2">
                    <span class="font-bold text-lg">SI MBEK</span>
                </div>

                <h2 class="text-2xl md:text-3xl font-extrabold text-gray-800 mb-6 text-left">Masuk</h2>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" novalidate action="{{ route('login') }}">
                    @csrf

                    <!-- Email -->
                    <div class="mb-4">
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" class="mt-1 block w-full border-[#E28700] focus:ring-orange-400  focus:border-orange-400" type="email" name="email" placeholder="contoh@gmail.com" :value="session('email_autofill') ?? old('email')" required autofocus />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div class="mb-2">
                        <x-input-label for="password" :value="__('Kata Sandi')" />
                        <x-text-input id="password" class="mt-1 block w-full border-[#E28700] focus:ring-orange-400  focus:border-orange-400" type="password" name="password" placeholder="Kata Sandi" required />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        <span class="fas fa-eye" id="icon-pw"></span>
                    </div>

                    <!-- Forgot Password -->
                    <div class="text-right text-sm mb-4">
                        <a href="{{ route('password.request') }}" class="text-[#E28700] hover:text-[#cf7700]">Lupa Kata Sandi?</a>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="w-full py-2 px-4 bg-brand-orange hover:bg-orange-700 text-white rounded-md font-semibold shadow-sm">
                        Masuk
                    </button>

                    <!-- Register -->
                    <div class="text-center text-sm text-gray-600 mt-6">
                        Belum punya akun? <a href="{{ route('register') }}" class="text-[#E28700] font-semibold hover:text-[#cf7700]">Daftar</a>
                    </div>
                </form>
            </div>

            <!-- Right: Goat Image -->
            <div class="hidden md:flex relative bg-[#FFF3E4] p-10 items-center justify-center w-full md:w-1/2">
                <!-- Lingkaran orange -->
                <div class="w-48 md:w-64 h-48 md:h-64 bg-orange-300 rounded-full shadow-xl relative z-10"></div>

                <!-- Gambar kambing PNG keluar dari lingkaran -->
                <img src="{{ asset('logo/kambing6 1.png') }}" 
                     alt="Kambing Raman Farm" 
                     class="absolute w-52 md:w-80 h-auto z-20 -translate-y-4 md:-translate-y-6" />
            </div>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const emailInput = document.querySelector('#email');
        const passwordInput = document.querySelector('#password');

        const storedEmail = localStorage.getItem('autofill_email');
        const storedPassword = localStorage.getItem('autofill_password');

        if (storedEmail && storedPassword) {
            emailInput.value = storedEmail;
            passwordInput.value = storedPassword;

            // Bersihkan setelah digunakan
            localStorage.removeItem('autofill_email');
            localStorage.removeItem('autofill_password');
        }
    });
</script>

</x-guest-layout>