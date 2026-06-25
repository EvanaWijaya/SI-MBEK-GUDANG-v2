<x-admin-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Admin - Tambah Domba') }}
        </h2>
    </x-slot>
    <div class="container mx-auto mt-10">

        <!-- Success and Error Messages -->
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
                    <div>
                        <div class="mb-4">
                            <label for="user_id" class="block text-sm font-bold mb-2">Nama Pemilik</label>
                            <select name="user_id" id="user_id"
                                class="w-full mt-2 p-2 border text-black border-gray-700 focus:ring-orange-400  focus:border-orange-400 rounded"
                                required>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="name" class="block text-sm font-bold mb-2">Nama Domba</label>
                            <input type="text" name="name" id="name"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:ring-orange-400  focus:border-orange-400 focus:outline-none focus:shadow-outline"
                                required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" x-model="domba.tanggal_lahir"
                                max="{{ date('Y-m-d') }}"
                                class="mt-1 px-3 py-2 border border-gray-700 focus:ring-orange-400  focus:border-orange-400  rounded-md w-full"
                                required>
                        </div>

                       <div class="mb-4">
    <label class="block text-sm font-bold mb-2">Upload Gambar</label>

    <div id="image-container">
        <div class="image-input-row mb-2">
            <input
                type="file"
                name="images[]"
                accept="image/*"
                class="image-input block w-full text-sm text-gray-500
                       file:mr-4 file:py-2 file:px-4 file:rounded-full
                       file:border-0 file:text-sm file:font-semibold
                       file:bg-orange-50 file:text-orange-700
                       hover:file:bg-orange-100"
            >
        </div>
    </div>

    <button
        type="button"
        id="add-image-btn"
        class="mt-2 inline-flex items-center text-orange-700 bg-orange-50
               hover:bg-orange-100 font-semibold text-sm px-4 py-2 rounded-full border-0">
        + Tambah Foto
    </button>

    <small class="text-gray-500 block mt-1">
        Maksimal 10 gambar. JPG, JPEG, PNG. Maks 2 MB per file.
    </small>

    <div id="preview-container" class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-4"></div>

    @error('images')
        <span class="text-red-500 text-sm">{{ $message }}</span>
    @enderror
    @error('images.*')
        <span class="text-red-500 text-sm">{{ $message }}</span>
    @enderror
</div>
</div>
                        <div class="mb-4">
                            <label for="type_domba" class="block text-sm font-bold mb-2">Jenis Domba</label>
                            <select name="type_domba" id="type_domba"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:ring-orange-400  focus:border-orange-400 focus:outline-none focus:shadow-outline"
                                required>
                                <option value="">-- Pilih Jenis --</option>
                                <option value="Garut">Garut</option>
                                <option value="Ekor Gemuk">Ekor Gemuk</option>
                                <option value="Ekor Tipis">Ekor Tipis</option>
                                <option value="Texel">Texel</option>
                                <option value="Dorper">Dorper</option>
                            </select>
                            @error('type_domba')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="jenis_kelamin" class="block text-sm font-bold mb-2">Jenis Kelamin</label>
                            <select name="jenis_kelamin" id="jenis_kelamin"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:ring-orange-400  focus:border-orange-400 focus:outline-none focus:shadow-outline"
                                required>
                                <option value="">-- Pilih Jenis --</option>
                                <option value="Jantan">Jantan</option>
                                <option value="Betina">Betina</option>
                            </select>
                            @error('jenis_kelamin')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="weight" class="block text-sm font-bold mb-2">Berat Kg</label>
                            <input type="number" step="0.01" name="weight" id="weight"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:ring-orange-400  focus:border-orange-400 focus:outline-none focus:shadow-outline"
                                required>
                        </div>

                        <div>
                            <!-- Pilih Status -->
                            <div class="mb-4">
                                <label for="faksin_status" class="block text-sm font-bold mb-2">Status Vaksin</label>
                                <select id="faksin_status"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:ring-orange-400 focus:border-orange-400 focus:outline-none focus:shadow-outline"
                                    required>
                                    <option value="">-- Pilih Status --</option>
                                    <option value="Aktif" {{ old('faksin_status') == 'Aktif' ? 'selected' : '' }}>
                                        Aktif</option>
                                    <option value="Tidak Aktif"
                                        {{ old('faksin_status') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif
                                    </option>
                                </select>
                            </div>

                            <!-- Pilih Jenis Vaksin (muncul kalau status = Aktif) -->
                            <div class="mb-4" id="jenis_vaksin_wrapper" style="display: none;">
                                <label for="jenis_vaksin" class="block text-sm font-bold mb-2">Jenis Vaksin</label>
                                <select id="jenis_vaksin"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:ring-orange-400 focus:border-orange-400 focus:outline-none focus:shadow-outline">
                                    <option value="">-- Pilih Jenis Vaksin --</option>
                                    <option value="Vaksin PMK"
                                        {{ old('faksin_status') == 'Vaksin PMK' ? 'selected' : '' }}>Vaksin PMK</option>
                                    <option value="Vaksin Antraks"
                                        {{ old('faksin_status') == 'Vaksin Antraks' ? 'selected' : '' }}>Vaksin Antraks</option>
                                    <option value="Vaksin Brucellosis"
                                        {{ old('faksin_status') == 'Vaksin Brucellosis' ? 'selected' : '' }}>Vaksin Brucellosis</option>
                                </select>
                            </div>

                            <!-- Hidden input yang dikirim ke server -->
                            <input type="hidden" name="faksin_status" id="faksin_status_hidden">

                        </div>

                        <script>
                            const statusSelect = document.getElementById('faksin_status');
                            const jenisWrapper = document.getElementById('jenis_vaksin_wrapper');
                            const jenisSelect = document.getElementById('jenis_vaksin');
                            const hiddenInput = document.getElementById('faksin_status_hidden');

                            function updateHiddenInput() {
                                if (statusSelect.value === "Aktif") {
                                    jenisWrapper.style.display = "block";
                                    hiddenInput.value = jenisSelect.value; // hanya simpan jenis vaksin
                                } else {
                                    jenisWrapper.style.display = "none";
                                    hiddenInput.value = statusSelect.value; // simpan "Tidak Aktif"
                                }
                            }

                            statusSelect.addEventListener('change', updateHiddenInput);
                            jenisSelect.addEventListener('change', updateHiddenInput);

                            // jalankan pertama kali (agar old() tetap muncul)
                            updateHiddenInput();
                        </script>



                        <div class="mb-4">
                            <label for="health_status" class="block text-sm font-bold mb-2">Status Kesehatan</label>

                            <!-- Dropdown -->
                            <select id="health_status_select"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:ring-orange-400 focus:border-orange-400 focus:outline-none focus:shadow-outline"
                                onchange="toggleOtherHealthStatus(this)" required>
                                <option value="">-- Pilih Status --</option>
                                <option value="Sehat" {{ old('healt_status') == 'Sehat' ? 'selected' : '' }}>Sehat
                                </option>
                                <option value="Tidak Sehat"
                                    {{ old('healt_status') == 'Tidak Sehat' ? 'selected' : '' }}>Tidak Sehat</option>
                                <option value="Lainnya"
                                    {{ old('healt_status') != 'Sehat' && old('healt_status') != 'Tidak Sehat' && old('healt_status') ? 'selected' : '' }}>
                                    Lainnya</option>
                            </select>

                            <!-- Input teks custom -->
                            <input type="text" id="health_status_custom" placeholder="Jelaskan kondisi"
                                value="{{ old('healt_status') != 'Sehat' && old('healt_status') != 'Tidak Sehat' ? old('healt_status') : '' }}"
                                class="mt-2 hidden shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:ring-orange-400 focus:border-orange-400 focus:outline-none focus:shadow-outline" />

                            <!-- Hidden input yang akan menyimpan nilai final -->
                            <input type="hidden" name="healt_status" id="health_status_final">
                        </div>
                        <script>
                            const healthSelect = document.getElementById('health_status_select');
                            const healthCustom = document.getElementById('health_status_custom');
                            const healthHidden = document.getElementById('health_status_final');

                            function toggleOtherHealthStatus(el) {
                                if (el.value === "Lainnya") {
                                    healthCustom.classList.remove("hidden");
                                    healthHidden.value = healthCustom.value; // simpan input custom
                                } else {
                                    healthCustom.classList.add("hidden");
                                    healthHidden.value = el.value; // simpan langsung dari dropdown
                                }
                            }

                            // Saat user mengetik di input custom, update hidden input
                            healthCustom.addEventListener("input", function() {
                                healthHidden.value = this.value;
                            });

                            // Jalankan pertama kali (agar old()/edit data tetap muncul)
                            toggleOtherHealthStatus(healthSelect);
                        </script>


                        <div class="mt-4 pt-4 border-t border-gray-100">
                <button type="submit" class="bg-brand-orange text-white px-6 py-2.5 rounded-lg font-bold hover:bg-orange-700 shadow transition-colors">
                    Simpan Produk
                </button>
                <a href="{{ route('admin.listdomba') }}" class="ml-4 text-gray-600 hover:text-gray-900 font-medium">Batal</a>
            </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

   <script>
        (function () {
            const container        = document.getElementById('image-container');
            const addBtn           = document.getElementById('add-image-btn');
            const previewContainer = document.getElementById('preview-container');

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
            });

            container.addEventListener('click', (e) => {
                if (e.target.classList.contains('remove-image')) {
                    e.target.closest('.image-input-row').remove();
                    renderPreview();
                }
            });

            container.addEventListener('change', renderPreview);

            function renderPreview() {
                previewContainer.innerHTML = '';
                document.querySelectorAll('.image-input').forEach(input => {
                    if (!input.files.length) return;
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        previewContainer.innerHTML += `
                            <div class="border rounded p-1">
                                <img src="${e.target.result}"
                                     class="w-full h-28 object-cover rounded">
                            </div>`;
                    };
                    reader.readAsDataURL(input.files[0]);
                });
            }
        })();

        function toggleOtherHealthStatus(select) {
            const customInput = document.getElementById('health_status_custom');
            const finalInput = document.getElementById('health_status_final');

            if (select.value === 'Lainnya') {
                customInput.classList.remove('hidden');
                customInput.value = '';
                finalInput.value = ''; // kosongkan dulu
                customInput.addEventListener('input', function() {
                    finalInput.value = this.value;
                });
            } else {
                customInput.classList.add('hidden');
                finalInput.value = select.value;
            }
        }
    </script>
</x-admin-app-layout>
