<x-admin-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Admin - Tambah Domba') }}
        </h2>
    </x-slot>

    <div class="container mx-auto mt-10">

        {{-- Success and Error Messages --}}
        @if (session('success'))
            <div class="bg-green-500 text-white p-4 rounded-lg shadow-md mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-500 text-white p-4 rounded-lg shadow-md mb-6">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="w-full mx-auto">
            <form action="{{ route('admin.tambahdomba.save') }}" method="POST" novalidate enctype="multipart/form-data"
                class="bg-white shadow-lg rounded-lg p-8 mb-6 border border-gray-300">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- ===================== KOLOM KIRI ===================== --}}
                    <div>

                        {{-- Nama Pemilik --}}
                        <div class="mb-4">
                            <label for="user_id" class="block text-sm font-bold mb-2">Nama Pemilik</label>
                            <select name="user_id" id="user_id"
                                class="w-full mt-2 p-2 border text-black border-gray-700 focus:ring-orange-400 focus:border-orange-400 rounded"
                                required>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Nama Domba --}}
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-bold mb-2">Nama Domba</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:ring-orange-400 focus:border-orange-400 focus:outline-none focus:shadow-outline"
                                required>
                            @error('name')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tanggal Lahir --}}
                        <div class="mb-4">
                            <label class="block text-sm font-bold mb-2">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}"
                                max="{{ date('Y-m-d') }}"
                                class="mt-1 px-3 py-2 border border-gray-700 focus:ring-orange-400 focus:border-orange-400 rounded-md w-full"
                                required>
                            @error('tanggal_lahir')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Upload Gambar --}}
                        <div class="mb-4">
                            <label class="block text-sm font-bold mb-2">Upload Gambar</label>

                            <div id="image-container">
                                <div class="image-input-row mb-2">
                                    <input type="file" name="images[]" accept="image/*" class="image-input block w-full text-sm text-gray-500
                                               file:mr-4 file:py-2 file:px-4 file:rounded-full
                                               file:border-0 file:text-sm file:font-semibold
                                               file:bg-orange-50 file:text-orange-700
                                               hover:file:bg-orange-100">
                                </div>
                            </div>

                            <button type="button" id="add-image-btn" class="mt-2 inline-flex items-center text-orange-700 bg-orange-50
                                       hover:bg-orange-100 font-semibold text-sm px-4 py-2 rounded-full border-0">
                                + Tambah Foto
                            </button>

                            <small class="text-gray-500 block mt-1">
                                Maksimal 10 gambar. JPG, JPEG, PNG. Maks 2 MB per file.
                            </small>

                            <p id="image-error" class="mt-2 text-sm text-red-600 font-medium hidden"></p>

                            <div id="preview-container" class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-4"></div>

                            @error('images')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                            @error('images.*')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>{{-- END KOLOM KIRI --}}

                    {{-- ===================== KOLOM KANAN ===================== --}}
                    <div>

                        {{-- Jenis Domba --}}
                        <div class="mb-4">
                            <label for="type_domba" class="block text-sm font-bold mb-2">Jenis Domba</label>
                            <select name="type_domba" id="type_domba"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:ring-orange-400 focus:border-orange-400 focus:outline-none focus:shadow-outline"
                                required>
                                <option value="">-- Pilih Jenis --</option>
                                <option value="Garut" {{ old('type_domba') == 'Garut' ? 'selected' : '' }}>Garut</option>
                                <option value="Ekor Gemuk" {{ old('type_domba') == 'Ekor Gemuk' ? 'selected' : '' }}>Ekor
                                    Gemuk</option>
                                <option value="Ekor Tipis" {{ old('type_domba') == 'Ekor Tipis' ? 'selected' : '' }}>Ekor
                                    Tipis</option>
                                <option value="Texel" {{ old('type_domba') == 'Texel' ? 'selected' : '' }}>Texel</option>
                                <option value="Dorper" {{ old('type_domba') == 'Dorper' ? 'selected' : '' }}>Dorper
                                </option>
                            </select>
                            @error('type_domba')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Jenis Kelamin --}}
                        <div class="mb-4">
                            <label for="jenis_kelamin" class="block text-sm font-bold mb-2">Jenis Kelamin</label>
                            <select name="jenis_kelamin" id="jenis_kelamin"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:ring-orange-400 focus:border-orange-400 focus:outline-none focus:shadow-outline"
                                required>
                                <option value="">-- Pilih Jenis --</option>
                                <option value="Jantan" {{ old('jenis_kelamin') == 'Jantan' ? 'selected' : '' }}>Jantan
                                </option>
                                <option value="Betina" {{ old('jenis_kelamin') == 'Betina' ? 'selected' : '' }}>Betina
                                </option>
                            </select>
                            @error('jenis_kelamin')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Berat --}}
                        <div class="mb-4">
                            <label for="weight" class="block text-sm font-bold mb-2">Berat Kg</label>
                            <input type="number" step="0.01" name="weight" id="weight" value="{{ old('weight') }}"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:ring-orange-400 focus:border-orange-400 focus:outline-none focus:shadow-outline"
                                required>
                            @error('weight')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Status Vaksin --}}
                        <div class="mb-4">
                            <label for="faksin_status" class="block text-sm font-bold mb-2">Status Vaksin</label>
                            <select id="faksin_status"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:ring-orange-400 focus:border-orange-400 focus:outline-none focus:shadow-outline"
                                required>
                                <option value="">-- Pilih Status --</option>
                                <option value="Aktif" {{ old('faksin_status') == 'Aktif' ? 'selected' : '' }}>Aktif
                                </option>
                                <option value="Tidak Aktif" {{ old('faksin_status') == 'Tidak Aktif' ? 'selected' : '' }}>
                                    Tidak Aktif</option>
                            </select>
                        </div>

                        {{-- Jenis Vaksin (muncul jika status = Aktif) --}}
                        <div class="mb-4" id="jenis_vaksin_wrapper" style="display: none;">
                            <label for="jenis_vaksin" class="block text-sm font-bold mb-2">Jenis Vaksin</label>
                            <select id="jenis_vaksin"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:ring-orange-400 focus:border-orange-400 focus:outline-none focus:shadow-outline">
                                <option value="">-- Pilih Jenis Vaksin --</option>
                                <option value="Vaksin PMK" {{ old('faksin_status') == 'Vaksin PMK' ? 'selected' : '' }}>
                                    Vaksin PMK</option>
                                <option value="Vaksin Antraks" {{ old('faksin_status') == 'Vaksin Antraks' ? 'selected' : '' }}>Vaksin Antraks</option>
                                <option value="Vaksin Brucellosis" {{ old('faksin_status') == 'Vaksin Brucellosis' ? 'selected' : '' }}>Vaksin Brucellosis</option>
                            </select>
                        </div>

                        {{-- Hidden input untuk faksin_status yang dikirim ke server --}}
                        <input type="hidden" name="faksin_status" id="faksin_status_hidden">

                        {{-- Status Kesehatan --}}
                        <div class="mb-4">
                            <label for="health_status_select" class="block text-sm font-bold mb-2">Status
                                Kesehatan</label>

                            <select id="health_status_select"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:ring-orange-400 focus:border-orange-400 focus:outline-none focus:shadow-outline"
                                required>
                                <option value="">-- Pilih Status --</option>
                                <option value="Sehat" {{ old('healt_status') == 'Sehat' ? 'selected' : '' }}>Sehat
                                </option>
                                <option value="Tidak Sehat" {{ old('healt_status') == 'Tidak Sehat' ? 'selected' : '' }}>
                                    Tidak Sehat</option>
                                <option value="Lainnya" {{ old('healt_status') && !in_array(old('healt_status'), ['Sehat', 'Tidak Sehat']) ? 'selected' : '' }}>
                                    Lainnya
                                </option>
                            </select>

                            <input type="text" id="health_status_custom" placeholder="Jelaskan kondisi"
                                value="{{ old('healt_status') && !in_array(old('healt_status'), ['Sehat', 'Tidak Sehat']) ? old('healt_status') : '' }}"
                                class="mt-2 hidden shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:ring-orange-400 focus:border-orange-400 focus:outline-none focus:shadow-outline">

                            <input type="hidden" name="healt_status" id="health_status_final">

                            @error('healt_status')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>{{-- END KOLOM KANAN --}}

                </div>{{-- END GRID --}}

                {{-- Tombol Aksi — di luar grid agar full width --}}
                <div class="mt-6 pt-4 border-t border-gray-100">
                    <button id="submit-btn" type="submit"
                        class="bg-brand-orange text-white px-6 py-2.5 rounded-lg font-bold hover:bg-orange-700 shadow transition-colors">
                        Simpan Produk
                    </button>
                    <a href="{{ route('admin.listdomba') }}" class="ml-4 text-gray-600 hover:text-gray-900 font-medium">
                        Batal
                    </a>
                </div>

            </form>
        </div>
    </div>

    {{-- ===================== SCRIPTS ===================== --}}
    <script>
        // ── Upload Gambar ────────────────────────────────────────────────────────────
        (function () {
            const container = document.getElementById('image-container');
            const addBtn = document.getElementById('add-image-btn');

            addBtn.addEventListener('click', () => {
                if (container.querySelectorAll('input[type=file]').length >= 10) {
                    alert('Maksimal 10 gambar');
                    return;
                }
                const wrapper = document.createElement('div');
                wrapper.classList.add('image-input-row', 'mb-2');
                wrapper.innerHTML = `
                <div class="flex gap-2 items-center">
                    <input type="file" name="images[]" accept="image/*"
                        class="image-input block w-full text-sm text-gray-500
                               file:mr-4 file:py-2 file:px-4 file:rounded-full
                               file:border-0 file:text-sm file:font-semibold
                               file:bg-orange-50 file:text-orange-700
                               hover:file:bg-orange-100">
                    <button type="button"
                        class="remove-image shrink-0 px-3 py-1.5 bg-red-500
                               text-white text-sm rounded-full hover:bg-red-600">
                        Hapus
                    </button>
                </div>`;
                container.appendChild(wrapper);
                renderPreview();
            });

            container.addEventListener('click', (e) => {
                if (e.target.classList.contains('remove-image')) {
                    e.target.closest('.image-input-row').remove();
                    renderPreview();
                }
            });

            container.addEventListener('change', renderPreview);

            function renderPreview() {
                const previewContainer = document.getElementById('preview-container');
                const errorBox = document.getElementById('image-error');
                const submitBtn = document.getElementById('submit-btn');
                if (!previewContainer || !errorBox || !submitBtn) return;

                previewContainer.innerHTML = '';
                const maxSize = 2 * 1024 * 1024;
                let hasError = false;
                let errorMessages = [];

                document.querySelectorAll('.image-input').forEach(input => {
                    if (!input.files.length) return;
                    const file = input.files[0];
                    if (file.size > maxSize) {
                        hasError = true;
                        errorMessages.push(`${file.name} melebihi ukuran maksimal 2 MB`);
                        input.value = '';
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        previewContainer.innerHTML += `
                        <div class="border rounded p-1">
                            <img src="${e.target.result}" class="w-full h-28 object-cover rounded">
                        </div>`;
                    };
                    reader.readAsDataURL(file);
                });

                if (hasError) {
                    errorBox.innerHTML = errorMessages.join('<br>');
                    errorBox.classList.remove('hidden');
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                } else {
                    errorBox.innerHTML = '';
                    errorBox.classList.add('hidden');
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }
        })();

        // ── Status Vaksin ────────────────────────────────────────────────────────────
        (function () {
            const statusSelect = document.getElementById('faksin_status');
            const jenisWrapper = document.getElementById('jenis_vaksin_wrapper');
            const jenisSelect = document.getElementById('jenis_vaksin');
            const hiddenInput = document.getElementById('faksin_status_hidden');

            function updateVaksin() {
                if (statusSelect.value === 'Aktif') {
                    jenisWrapper.style.display = 'block';
                    hiddenInput.value = jenisSelect.value;
                } else {
                    jenisWrapper.style.display = 'none';
                    hiddenInput.value = statusSelect.value;
                }
            }

            statusSelect.addEventListener('change', updateVaksin);
            jenisSelect.addEventListener('change', updateVaksin);
            updateVaksin(); // inisialisasi (agar old() tetap muncul)
        })();

        // ── Status Kesehatan ─────────────────────────────────────────────────────────
        (function () {
            const healthSelect = document.getElementById('health_status_select');
            const healthCustom = document.getElementById('health_status_custom');
            const healthHidden = document.getElementById('health_status_final');

            function updateHealthStatus() {
                if (healthSelect.value === 'Lainnya') {
                    healthCustom.classList.remove('hidden');
                    healthHidden.value = healthCustom.value;
                } else {
                    healthCustom.classList.add('hidden');
                    healthHidden.value = healthSelect.value;
                }
            }

            healthSelect.addEventListener('change', updateHealthStatus);
            healthCustom.addEventListener('input', function () {
                healthHidden.value = this.value;
            });
            updateHealthStatus(); // inisialisasi (agar old() tetap muncul)
        })();
    </script>

</x-admin-app-layout>