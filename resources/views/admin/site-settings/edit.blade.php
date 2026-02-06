@php
    $settings = App\Models\SiteSetting::first();
@endphp

<x-admin-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Admin - Pengaturan Situs') }}
        </h2>
    </x-slot>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-300 text-red-800 px-5 py-4 rounded-md shadow-sm mb-6">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li class="text-sm">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="bg-green-50 border border-green-300 text-green-800 px-5 py-4 rounded-md shadow-sm mt-6">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.site-settings.update') }}" enctype="multipart/form-data"
        class="space-y-10">
        @csrf
        @method('PUT')

        <!-- Header Settings -->
        <section class="bg-white p-8 rounded-xl shadow-md">
            <h2 class="text-3xl font-extrabold mb-6 text-orange-600">Pengaturan Header</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Logo Upload -->
                <div>
                    <label class="block mb-2 text-lg font-semibold text-gray-700">Logo Situs</label>
                    <input type="file" name="site_logo"
                        class="block w-full text-sm text-gray-500
                               file:mr-4 file:py-3 file:px-5
                               file:rounded-full file:border-0
                               file:text-sm file:font-semibold
                               file:bg-orange-100 file:text-orange-700
                               hover:file:bg-orange-200
                               focus:outline-none focus:ring-4 focus:ring-orange-300">
                    @php
                        // Normalisasi path site_logo untuk ditampilkan
                        $siteLogoPath = $settings->site_logo ?? '';
                        if ($siteLogoPath && !\Str::startsWith($siteLogoPath, ['http://', 'https://'])) {
                            // Hapus slash di depan jika ada
                            $siteLogoPath = ltrim($siteLogoPath, '/');
                            // Jika tidak diawali dengan storage/, tambahkan storage/
                            if (!\Str::startsWith($siteLogoPath, 'storage/')) {
                                $siteLogoPath = 'storage/' . $siteLogoPath;
                            }
                            $siteLogoUrl = asset($siteLogoPath);
                        } else {
                            $siteLogoUrl = $siteLogoPath;
                        }
                    @endphp
                    @if ($siteLogoUrl)
                        <div class="mt-3">
                            <img src="{{ $siteLogoUrl }}" class="w-24 h-24 object-contain rounded-lg shadow"
                                alt="Logo Saat Ini">
                        </div>
                    @endif
                </div>

                <!-- Nama Situs -->
                <div>
                    <label class="block mb-2 text-lg font-semibold text-gray-700">Nama Situs</label>
                    <input type="text" name="site_name" value="{{ $settings->site_name }}"
                        class="w-full px-5 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-200 ease-in-out text-gray-900 font-medium">
                </div>
            </div>
        </section>

        <!-- Bagian Konten -->
        <section class="bg-white p-8 rounded-xl shadow-md space-y-8">
            <h2 class="text-3xl font-extrabold mb-6 text-orange-600">Bagian Konten</h2>

            @foreach (['hero', 'why', 'cta', 'info'] as $section)
                <article
                    class="p-6 border border-gray-200 rounded-xl shadow-sm hover:shadow-lg transition-shadow duration-300">
                    <header class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-semibold text-gray-800 ">{{ $section }} Bagian</h3>
                        <label class="switch">
                            <input type="checkbox" name="sections[{{ $section }}][active]"
                                {{ data_get($settings, "sections.{$section}.active") ? 'checked' : '' }}>
                            <span class="slider round"></span>
                        </label>
                    </header>
                    <div class="space-y-6">
                        @if ($section === 'hero')
                            <div>
                                <label class="block mb-2 font-semibold text-gray-700">Judul</label>
                                <input type="text" name="sections[hero][title]"
                                    value="{{ data_get($settings, 'sections.hero.title') }}"
                                    class="w-full px-5 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-200 ease-in-out font-medium text-gray-900">
                            </div>
                            <div>
                                <label class="block mb-2 font-semibold text-gray-700">Subjudul</label>
                                <textarea name="sections[hero][subtitle]" rows="3"
                                    class="w-full px-5 py-3 border border-gray-300 rounded-xl resize-y focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-200 ease-in-out font-medium text-gray-900">{{ data_get($settings, 'sections.hero.subtitle') }}</textarea>
                            </div>
                            <div>
                                <label class="block mb-2 font-semibold text-gray-700">Deskripsi</label>
                                <textarea name="sections[hero][description]" rows="3"
                                    class="w-full px-5 py-3 border border-gray-300 rounded-xl resize-y focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-200 ease-in-out font-medium text-gray-900">{{ data_get($settings, 'sections.hero.description') }}</textarea>
                            </div>
                            <div>
                                <label class="block mb-2 font-semibold text-gray-700">Gambar Hero</label>

                                @php
                                    $heroImage = data_get($settings, 'sections.hero.image');
                                    $heroImageUrl = null;
                                    if ($heroImage) {
                                        $heroImageUrl = \Str::startsWith($heroImage, ['http://', 'https://'])
                                            ? $heroImage
                                            : \Storage::url($heroImage);
                                    }
                                @endphp

                                @if ($heroImageUrl)
                                    <div class="mb-2">
                                        <img src="{{ $heroImageUrl }}" alt="Gambar Hero"
                                            class="w-40 h-40 object-cover rounded border border-gray-300 shadow">
                                    </div>
                                @endif

                                <input type="file" name="hero_image"
                                    class="block w-full text-sm text-gray-500 rounded-md border border-gray-300 px-4 py-2 hover:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-500">
                            </div>
                        @elseif ($section === 'why')
                            <div class="space-y-6">
                                {{-- Judul --}}
                                <div>
                                    <label class="block mb-2 font-semibold text-gray-700">Judul Bagian</label>
                                    <input type="text" name="sections[why][heading]"
                                        value="{{ data_get($settings, 'sections.why.heading') }}"
                                        class="w-full px-5 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-200 ease-in-out font-medium text-gray-900">
                                </div>

                                {{-- Kontainer Item Why --}}
                                <div id="why-items-container" class="space-y-6">
                                    @php
                                        $whyItems = data_get($settings, 'sections.why.items', []);
                                        // Jika tidak ada item, buat satu item kosong
                                        if (count($whyItems) === 0) {
                                            $whyItems = [['title' => '', 'description' => '']];
                                        }
                                    @endphp

                                    @foreach ($whyItems as $index => $item)
                                        <div
                                            class="why-item p-5 bg-orange-50 rounded-xl border border-orange-200 shadow-inner space-y-3">
                                            <div class="flex justify-between items-center">
                                                <h4 class="text-lg font-semibold text-orange-700">Item
                                                    #{{ $index + 1 }}</h4>
                                                <button type="button"
                                                    class="delete-why-item-btn px-3 py-1 rounded-lg bg-red-600 hover:bg-red-700 text-white font-semibold transition duration-300 shadow">
                                                    Hapus
                                                </button>
                                            </div>

                                            <div>
                                                <label class="block mb-2 font-medium text-orange-800">Judul</label>
                                                <input type="text"
                                                    name="sections[why][items][{{ $index }}][title]"
                                                    value="{{ $item['title'] ?? '' }}"
                                                    class="w-full px-4 py-2 border border-orange-300 rounded-lg focus:ring-2 focus:ring-orange-600 focus:border-orange-600 transition duration-200 ease-in-out font-medium text-orange-900">
                                            </div>

                                            <div>
                                                <label class="block mb-2 font-medium text-orange-800">Deskripsi</label>
                                                <textarea name="sections[why][items][{{ $index }}][description]" rows="3"
                                                    class="w-full px-4 py-2 border border-orange-300 rounded-lg resize-y focus:ring-2 focus:ring-orange-600 focus:border-orange-600 transition duration-200 ease-in-out font-medium text-orange-900">{{ $item['description'] ?? '' }}</textarea>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Tombol Tambah Item --}}
                                <button type="button" id="add-why-item-btn"
                                    class="mt-4 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold shadow-lg transition duration-300">
                                    + Tambah Item
                                </button>
                                <script>
                                    document.addEventListener('DOMContentLoaded', () => {
                                        const whyItemsContainer = document.getElementById('why-items-container');
                                        const addWhyItemBtn = document.getElementById('add-why-item-btn');

                                        // Fungsi untuk menambah item baru
                                        addWhyItemBtn.addEventListener('click', () => {
                                            const itemCount = whyItemsContainer.querySelectorAll('.why-item').length;
                                            const newIndex = itemCount;

                                            const newItem = document.createElement('div');
                                            newItem.className =
                                                'why-item p-5 bg-orange-50 rounded-xl border border-orange-200 shadow-inner space-y-3';
                                            newItem.innerHTML = `
            <div class="flex justify-between items-center">
                <h4 class="text-lg font-semibold text-orange-700">Item #${newIndex + 1}</h4>
                <button type="button" class="delete-why-item-btn px-3 py-1 bg-red-500 text-white rounded-lg hover:bg-red-600 transition duration-200">
                    Hapus
                </button>
            </div>
            <div>
                <label class="block mb-2 font-medium text-orange-800">Judul</label>
                <input type="text" name="sections[why][items][${newIndex}][title]" class="w-full px-4 py-2 border border-orange-300 rounded-lg focus:ring-2 focus:ring-orange-600 focus:border-orange-600 transition duration-200">
            </div>
            <div>
                <label class="block mb-2 font-medium text-orange-800">Deskripsi</label>
                <textarea name="sections[why][items][${newIndex}][description]" rows="3" class="w-full px-4 py-2 border border-orange-300 rounded-lg resize-y focus:ring-2 focus:ring-orange-600 focus:border-orange-600 transition duration-200"></textarea>
            </div>
        `;

                                            whyItemsContainer.appendChild(newItem);

                                            // Tambahkan event listener untuk tombol hapus pada item baru
                                            newItem.querySelector('.delete-why-item-btn').addEventListener('click', function() {
                                                this.closest('.why-item').remove();
                                                updateWhyItemNumbers();
                                            });
                                        });

                                        // Fungsi untuk menghapus item
                                        whyItemsContainer.addEventListener('click', function(e) {
                                            if (e.target.classList.contains('delete-why-item-btn')) {
                                                e.target.closest('.why-item').remove();
                                                updateWhyItemNumbers();
                                            }
                                        });

                                        // Fungsi untuk update nomor item
                                        function updateWhyItemNumbers() {
                                            const items = whyItemsContainer.querySelectorAll('.why-item');
                                            items.forEach((item, index) => {
                                                item.querySelector('h4').textContent = `Item #${index + 1}`;

                                                // Update nama input
                                                const titleInput = item.querySelector('input[name^="sections[why][items]"]');
                                                const descTextarea = item.querySelector('textarea[name^="sections[why][items]"]');

                                                // Ubah index pada nama field
                                                titleInput.name = `sections[why][items][${index}][title]`;
                                                descTextarea.name = `sections[why][items][${index}][description]`;
                                            });
                                        }

                                        // Inisialisasi event listener untuk tombol hapus yang sudah ada
                                        document.querySelectorAll('.delete-why-item-btn').forEach(btn => {
                                            btn.addEventListener('click', function() {
                                                this.closest('.why-item').remove();
                                                updateWhyItemNumbers();
                                            });
                                        });
                                    });
                                </script>

                                {{-- Gambar Why --}}
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                                    @for ($j = 0; $j < 4; $j++)
                                        @php
                                            $imageName = data_get($settings, "sections.why.images.{$j}");
                                            $imageUrl = null;
                                            if ($imageName) {
                                                if (!\Str::startsWith($imageName, ['http://', 'https://'])) {
                                                    // Jika imageName diawali dengan 'sections/why/...' atau 'settings/...', gunakan Storage::url
                                                    $imageUrl = Storage::url($imageName);
                                                } else {
                                                    $imageUrl = $imageName;
                                                }
                                            }
                                        @endphp

                                        <div>
                                            <label class="block mb-2 font-semibold text-gray-700">Gambar
                                                #{{ $j + 1 }}</label>
                                            <input type="file" name="sections[why][images][{{ $j }}]"
                                                class="block w-full text-sm text-gray-500 rounded-md border border-gray-300 px-4 py-2 hover:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-500">

                                            @if ($imageUrl)
                                                <img src="{{ $imageUrl }}"
                                                    alt="Gambar Bagian Why {{ $j + 1 }}"
                                                    class="mt-3 w-full h-36 object-cover rounded-xl shadow-md" />
                                            @else
                                                <p class="mt-2 text-gray-500">Gambar tidak ditemukan.</p>
                                            @endif
                                        </div>
                                    @endfor
                                </div>
                            @elseif ($section === 'cta')
                                <div>
                                    <label class="block mb-2 font-semibold text-gray-700">Judul</label>
                                    <input type="text" name="sections[cta][heading]"
                                        value="{{ data_get($settings, 'sections.cta.heading') }}"
                                        class="w-full px-5 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-200 ease-in-out font-medium text-gray-900">
                                </div>
                                <div>
                                    <label class="block mb-2 font-semibold text-gray-700">Subjudul</label>
                                    <textarea name="sections[cta][subheading]" rows="3"
                                        class="w-full px-5 py-3 border border-gray-300 rounded-xl resize-y focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-200 ease-in-out font-medium text-gray-900">{{ data_get($settings, 'sections.cta.subheading') }}</textarea>
                                </div>
                            @elseif ($section === 'info')
                                <div>
                                    <label class="block mb-2 font-semibold text-gray-700">Judul Bagian</label>
                                    <input type="text" name="sections[info][title]"
                                        value="{{ data_get($settings, 'sections.info.title') }}"
                                        class="w-full px-5 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-200 ease-in-out font-medium text-gray-900">
                                </div>
                                <div>
                                    <label class="block mb-2 font-semibold text-gray-700">Subjudul Bagian</label>
                                    <textarea name="sections[info][subtitle]" rows="3"
                                        class="w-full px-5 py-3 border border-gray-300 rounded-xl resize-y focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-200 ease-in-out font-medium text-gray-900">{{ data_get($settings, 'sections.info.subtitle') }}</textarea>
                                </div>
                                {{-- Kontainer Info Boxes --}}
                                <div id="info-box-container" class="space-y-6">
                                    @php
                                        $infoBoxes = data_get($settings, 'sections.info.boxes', []);
                                        // Jika tidak ada box, buat satu box kosong
                                        if (count($infoBoxes) === 0) {
                                            $infoBoxes = [['title' => '', 'content' => '']];
                                        }
                                    @endphp

                                    @foreach ($infoBoxes as $index => $box)
                                        <div
                                            class="info-box p-5 bg-orange-50 rounded-lg border border-orange-200 shadow-inner space-y-3">
                                            <div class="flex justify-between items-center">
                                                <h4 class="text-lg font-semibold text-orange-700">Box
                                                    #{{ $index + 1 }}</h4>
                                                <button type="button"
                                                    class="delete-info-box-btn px-3 py-1 rounded-lg bg-red-600 hover:bg-red-700 text-white font-semibold transition duration-300 shadow">
                                                    Hapus
                                                </button>
                                            </div>
                                            <div>
                                                <label class="block mb-2 font-medium text-orange-800">Judul Box</label>
                                                <input type="text"
                                                    name="sections[info][boxes][{{ $index }}][title]"
                                                    value="{{ $box['title'] ?? '' }}"
                                                    class="w-full px-4 py-2 border border-orange-300 rounded
                                                                                                    class="w-full
                                                    px-4 py-2 border border-orange-300 rounded-lg focus:ring-2
                                                    focus:ring-orange-600 focus:border-orange-600 transition
                                                    duration-200 ease-in-out font-medium text-orange-900">
                                            </div>
                                            <div>
                                                <label class="block mb-2 font-medium text-orange-800">Konten Box (HTML
                                                    diperbolehkan)</label>
                                                <textarea name="sections[info][boxes][{{ $index }}][content]" rows="3"
                                                    class="w-full px-4 py-2 border border-orange-300 rounded-lg resize-y focus:ring-2 focus:ring-orange-600 focus:border-orange-600 transition duration-200 ease-in-out font-medium text-orange-900">{{ $box['content'] ?? '' }}</textarea>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Tombol Tambah Box --}}
                                <button type="button" id="add-info-box-btn"
                                    class="mt-4 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold shadow-lg transition duration-300">
                                    + Tambah Box
                                </button>
                                <script>
                                    document.addEventListener('DOMContentLoaded', () => {
                                        // Untuk Info Section Boxes
                                        const infoBoxContainer = document.getElementById('info-box-container');
                                        const addInfoBoxBtn = document.getElementById('add-info-box-btn');

                                        if (addInfoBoxBtn) {
                                            addInfoBoxBtn.addEventListener('click', () => {
                                                const boxCount = infoBoxContainer.querySelectorAll('.info-box').length;
                                                const newIndex = boxCount;

                                                const newBox = document.createElement('div');
                                                newBox.className =
                                                    'info-box p-5 bg-orange-50 rounded-lg border border-orange-200 shadow-inner space-y-3';
                                                newBox.innerHTML = `
                <div class="flex justify-between items-center">
                    <h4 class="text-lg font-semibold text-orange-700">Box #${newIndex + 1}</h4>
                    <button type="button" class="delete-info-box-btn px-3 py-1 bg-red-500 text-white rounded-lg hover:bg-red-600 transition duration-200">
                        Hapus
                    </button>
                </div>
                <div>
                    <label class="block mb-2 font-medium text-orange-800">Judul Box</label>
                    <input type="text" name="sections[info][boxes][${newIndex}][title]" class="w-full px-4 py-2 border border-orange-300 rounded-lg focus:ring-2 focus:ring-orange-600 focus:border-orange-600 transition duration-200 ease-in-out font-medium text-orange-900">
                </div>
                <div>
                    <label class="block mb-2 font-medium text-orange-800">Konten Box (HTML diperbolehkan)</label>
                    <textarea name="sections[info][boxes][${newIndex}][content]" rows="3" class="w-full px-4 py-2 border border-orange-300 rounded-lg resize-y focus:ring-2 focus:ring-orange-600 focus:border-orange-600 transition duration-200 ease-in-out font-medium text-orange-900"></textarea>
                </div>
            `;

                                                infoBoxContainer.appendChild(newBox);

                                                // Tambahkan event listener untuk tombol hapus pada box baru
                                                newBox.querySelector('.delete-info-box-btn').addEventListener('click', function() {
                                                    this.closest('.info-box').remove();
                                                    updateInfoBoxNumbers();
                                                });
                                            });
                                        }

                                        // Fungsi untuk menghapus box info
                                        infoBoxContainer.addEventListener('click', function(e) {
                                            if (e.target.classList.contains('delete-info-box-btn')) {
                                                e.target.closest('.info-box').remove();
                                                updateInfoBoxNumbers();
                                            }
                                        });

                                        // Fungsi untuk update nomor box info
                                        function updateInfoBoxNumbers() {
                                            const boxes = infoBoxContainer.querySelectorAll('.info-box');
                                            boxes.forEach((box, index) => {
                                                box.querySelector('h4').textContent = `Box #${index + 1}`;

                                                // Update nama input
                                                const titleInput = box.querySelector('input[name^="sections[info][boxes]"]');
                                                const contentTextarea = box.querySelector('textarea[name^="sections[info][boxes]"]');

                                                // Ubah index pada nama field
                                                titleInput.name = `sections[info][boxes][${index}][title]`;
                                                contentTextarea.name = `sections[info][boxes][${index}][content]`;
                                            });
                                        }

                                        // Inisialisasi event listener untuk tombol hapus yang sudah ada
                                        document.querySelectorAll('.delete-info-box-btn').forEach(btn => {
                                            btn.addEventListener('click', function() {
                                                this.closest('.info-box').remove();
                                                updateInfoBoxNumbers();
                                            });
                                        });
                                    });
                                </script>
                        @endif
                    </div>
                </article>
            @endforeach

            <div class="mt-8 text-right">
                <button type="submit"
                    class="bg-brand-orange hover:bg-orange-700 px-7 py-3 rounded-xl text-white font-semibold shadow-lg transition duration-300">
                    Simpan Pengaturan
                </button>
            </div>
        </section>

        <!-- Pengaturan Halaman Tentang -->
        <section class="bg-white p-8 rounded-xl shadow-md space-y-6">
            <header class="flex justify-between items-center mb-6">
                <h2 class="text-3xl font-extrabold text-orange-600">Pengaturan Halaman Tentang</h2>
                <label class="switch">
                    <input type="checkbox" name="about_page[active]"
                        {{ $settings->about_page['active'] ?? true ? 'checked' : '' }}>
                    <span class="slider round"></span>
                </label>
            </header>

            <div id="sections-container" class="space-y-8">
                <div>
                    <label class="block mb-2 font-semibold text-gray-700">Judul Halaman</label>
                    <input type="text" name="about_page[title]"
                        value="{{ $settings->about_page['title'] ?? 'Tentang Kami' }}"
                        class="w-full px-5 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-900 font-medium transition duration-200 ease-in-out">
                </div>

                <div>
                    <label class="block mb-2 font-semibold text-gray-700">Subjudul Halaman</label>
                    <input type="text" name="about_page[subtitle]"
                        value="{{ $settings->about_page['subtitle'] ?? 'Raman Farm' }}"
                        class="w-full px-5 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-900 font-medium transition duration-200 ease-in-out">
                </div>

                @foreach ($settings->about_page['sections'] ?? [] as $index => $section)
                    <article
                        class="section-block border border-gray-200 p-6 rounded-xl bg-orange-50 shadow-inner space-y-5">
                        <h3 class="text-xl font-semibold mb-4 text-orange-700">Bagian {{ $index + 1 }}</h3>

                        <div>
                            <label class="block mb-2 font-medium text-orange-800">Judul Bagian</label>
                            <input type="text" name="about_page[sections][{{ $index + 1 }}][title]"
                                value="{{ $section['title'] }}"
                                class="w-full px-4 py-3 border border-orange-300 rounded-lg focus:ring-2 focus:ring-orange-600 focus:border-orange-600 transition duration-200 font-medium text-orange-900">
                        </div>

                        <div>
                            <label class="block mb-2 font-medium text-orange-800">Konten</label>
                            <textarea name="about_page[sections][{{ $index + 1 }}][content]" rows="5"
                                class="w-full px-4 py-3 border border-orange-300 rounded-lg resize-y focus:ring-2 focus:ring-orange-600 focus:border-orange-600 transition duration-200 font-medium text-orange-900">{{ $section['content'] ?? '' }}</textarea>
                        </div>

                        <div>
                            <label class="block mb-2 font-medium text-orange-800">Item (satu per baris, gunakan
                                &lt;strong&gt; untuk teks tebal)</label>
                            <textarea name="about_page[sections][{{ $index + 1 }}][items]" rows="5"
                                class="w-full px-4 py-3 border border-orange-300 rounded-lg resize-y focus:ring-2 focus:ring-orange-600 focus:border-orange-600 transition duration-200 font-medium text-orange-900">{{ isset($section['items']) ? (is_array($section['items']) ? implode("\n", $section['items']) : $section['items']) : '' }}</textarea>
                        </div>

                        <button type="button"
                            class="delete-section-btn px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white font-semibold transition duration-300 shadow">
                            Hapus Bagian
                        </button>
                    </article>
                @endforeach
            </div>

            <button type="button" id="add-section-btn"
                class="mt-6 px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold shadow-lg transition duration-300">
                Tambah Bagian
            </button>
        </section>

        <template id="section-template">
            <article class="section-block border border-gray-200 p-6 rounded-xl bg-orange-50 shadow-inner space-y-5">
                <h3 class="text-xl font-semibold mb-4 text-orange-700">Bagian __DISPLAY_INDEX__</h3>
                <div>
                    <label class="block mb-2 font-medium text-orange-800">Judul Bagian</label>
                    <input type="text" name="about_page[sections][__INDEX__][title]"
                        class="w-full px-4 py-3 border border-orange-300 rounded-lg focus:ring-2 focus:ring-orange-600 focus:border-orange-600 font-medium text-orange-900 transition duration-200">
                </div>
                <div>
                    <label class="block mb-2 font-medium text-orange-800">Konten</label>
                    <textarea name="about_page[sections][__INDEX__][content]" rows="5"
                        class="w-full px-4 py-3 border border-orange-300 rounded-lg resize-y focus:ring-2 focus:ring-orange-600 focus:border-orange-600 font-medium text-orange-900 transition duration-200"></textarea>
                </div>
                <div>
                    <label class="block mb-2 font-medium text-orange-800">Item (satu per baris, gunakan &lt;strong&gt;
                        untuk teks tebal)</label>
                    <textarea name="about_page[sections][__INDEX__][items]" rows="5"
                        class="w-full px-4 py-3 border border-orange-300 rounded-lg resize-y focus:ring-2 focus:ring-orange-600 focus:border-orange-600 font-medium text-orange-900 transition duration-200"></textarea>
                </div>
                <button type="button"
                    class="delete-section-btn px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white font-semibold transition duration-300 shadow">
                    Hapus Bagian
                </button>
            </article>
        </template>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const container = document.getElementById('sections-container');
                const template = document.getElementById('section-template').innerHTML;
                const addBtn = document.getElementById('add-section-btn');

                addBtn.addEventListener('click', () => {
                    const currentCount = container.querySelectorAll('.section-block').length;
                    const newIndex = currentCount + 1;

                    const newHtml = template
                        .replace(/__INDEX__/g, newIndex)
                        .replace(/__DISPLAY_INDEX__/g, newIndex);

                    container.insertAdjacentHTML('beforeend', newHtml);
                });

                container.addEventListener('click', e => {
                    if (e.target.classList.contains('delete-section-btn')) {
                        const sectionBlock = e.target.closest('.section-block');
                        if (sectionBlock) sectionBlock.remove();
                    }
                });
            });
        </script>

        <!-- Pengaturan Footer -->
        <section class="bg-white p-8 rounded-xl shadow-md space-y-8">
            <h2 class="text-3xl font-extrabold mb-6 text-orange-600">Pengaturan Footer</h2>

            <!-- Media Sosial -->
            <div class="space-y-4">
                <h3 class="text-xl font-semibold text-gray-800">Media Sosial</h3>
                @foreach (['twitter', 'facebook', 'instagram'] as $social)
                    <div class="flex items-center gap-3">
                        <label class="switch">
                            <input type="checkbox" name="social[{{ $social }}][active]"
                                {{ $settings->social[$social]['active'] ?? false ? 'checked' : '' }}>
                            <span class="slider round"></span>
                        </label>
                        <span class=" text-gray-700 font-medium w-24">{{ $social }}</span>
                        <input type="text" name="social[{{ $social }}][url]"
                            value="{{ $settings->social[$social]['url'] }}"
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-200 ease-in-out text-gray-900 font-medium">
                    </div>
                @endforeach
            </div>

            <!-- Informasi Kontak -->
            <div class="space-y-4">
                <h3 class="text-xl font-semibold text-gray-800">Informasi Kontak</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block mb-2 font-semibold text-gray-700">Alamat</label>
                        <textarea name="contact[address]" rows="3"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl resize-y focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-200 ease-in-out font-medium text-gray-900">{{ $settings->contact['address'] ?? '' }}</textarea>
                    </div>
                    <div>
                        <label class="block mb-2 font-semibold text-gray-700">Nomor Telepon</label>
                        <input type="text" name="contact[phone]" value="{{ $settings->contact['phone'] ?? '' }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-200 ease-in-out font-medium text-gray-900">
                    </div>
                    <div>
                        <label class="block mb-2 font-semibold text-gray-700">Email</label>
                        <input type="email" name="contact[email]" value="{{ $settings->contact['email'] ?? '' }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-200 ease-in-out font-medium text-gray-900">
                    </div>
                </div>
            </div>

            <!-- Pengaturan Peta -->
            <div>
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-xl font-semibold text-gray-800">Embed Peta</h3>
                    <label class="switch">
                        <input type="checkbox" name="map[active]"
                            {{ !empty($settings->map['active']) ? 'checked' : '' }}> <span
                            class="slider round"></span>
                    </label>
                </div>
                <textarea name="map[embed_code]" rows="5"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl resize-y focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-200 ease-in-out font-medium text-gray-900">{{ $settings->map['embed_code'] }}</textarea>
            </div>
        </section>

        <div class="text-right p-4 mt-8">
            <button type="submit"
                class="px-8 py-3 bg-brand-orange rounded-xl text-white font-bold shadow-lg hover:bg-orange-700 transition duration-300">
                Simpan Pengaturan
            </button>
        </div>
    </form>

    <style>
        /* Toggle switch */
        .switch {
            position: relative;
            display: inline-block;
            width: 56px;
            height: 30px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
            position: absolute;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            inset: 0;
            background-color: #d1d5db;
            /* Tailwind gray-300 */
            border-radius: 9999px;
            transition: background-color 0.3s ease;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            border-radius: 9999px;
            transition: transform 0.3s ease;
            box-shadow: 0 1px 3px rgb(0 0 0 / 0.1);
        }

        input:checked+.slider {
            background-color: #ea7517;
            /* Tailwind orange-600 */
        }

        input:checked+.slider:before {
            transform: translateX(26px);
        }

        .slider.round {
            border-radius: 9999px;
        }

        .slider.round:before {
            border-radius: 9999px;
        }
    </style>
</x-admin-app-layout>
