<section class="bg-white p-6 rounded-lg shadow-sm space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Informasi Profil') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Perbarui foto, nama, dan kontak akun Anda.') }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="profile_picture" :value="__('Foto Profil')" />
            <x-text-input id="profile_picture" name="profile_picture" type="file" class="mt-1 block w-full focus:ring-orange-400 focus:border-orange-400" accept="image/*" />
            <x-input-error class="mt-2" :messages="$errors->get('profile_picture')" />
        </div>

        <div>
            <x-input-label for="name" :value="__('Nama')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full focus:ring-orange-400 focus:border-orange-400" :value="old('name', $user->name)" required autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="address" :value="__('Alamat')" />
            <x-text-input id="address" name="address" type="text" class="mt-1 block w-full focus:ring-orange-400 focus:border-orange-400" :value="old('address', $user->address)" required autocomplete="street-address" />
            <x-input-error class="mt-2" :messages="$errors->get('address')" />
        </div>

        <div>
            <x-input-label for="phone_number" :value="__('No. Telepon')" />
            <x-text-input id="phone_number" name="phone_number" type="tel" class="mt-1 block w-full focus:ring-orange-400 focus:border-orange-400" :value="old('phone_number', $user->phone_number)" required autocomplete="tel" />
            <x-input-error class="mt-2" :messages="$errors->get('phone_number')" />
        </div>
        
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full focus:ring-orange-400 focus:border-orange-400" :value="old('email', $user->email)" required autocomplete="email" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Email Anda belum terverifikasi.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                            {{ __('Kirim ulang verifikasi.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('Link verifikasi telah dikirim ulang ke email Anda.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="bg-brand-orange hover:bg-orange-700 focus:ring-orange-500">
                {{ __('Simpan') }}
            </x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Berhasil disimpan.') }}</p>
            @endif
        </div>
    </form>
</section>