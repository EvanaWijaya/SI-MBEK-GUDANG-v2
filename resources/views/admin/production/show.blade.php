<x-admin-app-layout>
    <div class="p-6 max-w-4xl mx-auto">

        {{-- Header --}}
        <div class="flex items-center gap-3 mb-8">
            <a href="{{ route('admin.productions.index') }}"
                class="p-2 rounded-lg hover:bg-gray-100 text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div class="flex-1">
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-2xl font-bold text-gray-800">Detail Produksi ID {{ $production->id }}</h1>
                    @if($production->status === 'progress')
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Diproses
                        </span>
                    @elseif($production->status === 'completed')
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Selesai
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                            Ditolak
                        </span>
                    @endif
                </div>
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ $production->product->product_name ?? '-' }} — {{ $production->formula->formula_name ?? '-' }}
                </p>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mb-6 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm font-semibold">
                <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('warning'))
            <div class="mb-6 flex items-center gap-3 bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg text-sm">
                <svg class="w-5 h-5 text-yellow-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                {{ session('warning') }}
            </div>
        @endif
        @if($errors->any())
            <div class="mb-6 flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm shadow-sm">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                <div>
                    <span class="font-bold block mb-0.5">Terjadi kesalahan pemrosesan data:</span>
                    <ul class="list-disc list-inside space-y-0.5 text-xs text-red-700">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- ── STEP INDICATOR ── --}}
        @php
            $qcSudahDilakukan = !is_null($production->qc_status) && $production->qc_status !== 'pending';
            $qcLayak          = $production->qc_status === 'passed';
            $qcTidakLayak     = $production->qc_status === 'failed';
            $sudahSelesai     = $production->status === 'completed';
            $sudahRejected    = $production->status === 'rejected';
        @endphp

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 mb-6">
            <div class="flex items-center">
                {{-- Step 1 --}}
                <div class="flex items-center gap-2.5 flex-1 min-w-0">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 bg-orange-500 text-white">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold text-gray-700">Produksi Dibuat</p>
                        <p class="text-xs text-gray-400 truncate">{{ \Carbon\Carbon::parse($production->production_date)->format('d M Y') }}</p>
                    </div>
                </div>

                <svg class="w-5 h-5 text-gray-300 flex-shrink-0 mx-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>

                {{-- Step 2 --}}
                <div class="flex items-center gap-2.5 flex-1 min-w-0">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0
                        {{ $qcSudahDilakukan ? ($qcLayak ? 'bg-green-500 text-white' : 'bg-red-500 text-white') : 'bg-blue-500 text-white' }}">
                        @if($qcSudahDilakukan)
                            @if($qcLayak)
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            @else
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            @endif
                        @else
                            <span class="text-xs font-bold">2</span>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold text-gray-770">Quality Control</p>
                        @if($qcLayak)
                            <p class="text-xs text-green-600 font-medium">Layak ({{ $production->qc_percentage }}%)</p>
                        @elseif($qcTidakLayak)
                            <p class="text-xs text-red-600 font-medium">Tidak Layak</p>
                        @else
                            <p class="text-xs text-blue-500 font-medium">Perlu dilakukan</p>
                        @endif
                    </div>
                </div>

                <svg class="w-5 h-5 text-gray-300 flex-shrink-0 mx-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>

                {{-- Step 3 --}}
                @php $step3Rejected = $sudahRejected || $qcTidakLayak; @endphp
                <div class="flex items-center gap-2.5 flex-1 min-w-0">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0
                        {{ $sudahSelesai ? 'bg-green-500 text-white' : ($step3Rejected ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-400') }}">
                        @if($sudahSelesai)
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        @elseif($step3Rejected)
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        @else
                            <span class="text-xs font-bold">3</span>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold text-gray-700">Selesai / Ditolak</p>
                        @if($sudahSelesai)
                            <p class="text-xs text-green-600 font-medium">Stok bertambah ✓</p>
                        @elseif($step3Rejected)
                            <p class="text-xs text-red-600 font-medium">Produksi ditolak</p>
                        @elseif($qcLayak)
                            <p class="text-xs text-orange-500 font-medium">Siap diselesaikan</p>
                        @else
                            <p class="text-xs text-gray-400">Menunggu QC</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">

            {{-- ── INFO PRODUKSI + KOMPOSISI ── --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Informasi Produksi</h3>
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between py-1.5 border-b border-gray-50">
                            <dt class="text-gray-400">Produk</dt>
                            <dd class="font-semibold text-gray-800">{{ $production->product->product_name ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-gray-50">
                            <dt class="text-gray-400">Kode Produk</dt>
                            <dd class="font-mono text-xs text-gray-700">{{ $production->product->product_code ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-gray-50">
                            <dt class="text-gray-400">Formula</dt>
                            <dd class="text-gray-700 text-right">{{ $production->formula->formula_name ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-gray-50">
                            <dt class="text-gray-400">Qty Produksi</dt>
                            <dd class="font-bold text-gray-800">{{ number_format($production->production_quantity) }} kg</dd>
                        </div>
                        <div class="flex justify-between py-1.5">
                            <dt class="text-gray-400">Tanggal Produksi</dt>
                            <dd class="text-gray-700">{{ \Carbon\Carbon::parse($production->production_date)->format('d M Y') }}</dd>
                        </div>
                        @if($production->expiration_date)
                        <div class="flex justify-between py-1.5 border-t border-gray-50">
                            <dt class="text-gray-400">Expired Date</dt>
                            <dd class="text-gray-700">{{ \Carbon\Carbon::parse($production->expiration_date)->format('d M Y') }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Komposisi Bahan (Formula)</h3>
                    @php
                        $hasMaterials = $production->formula
                            && method_exists($production->formula, 'materials')
                            && $production->formula->materials->isNotEmpty();
                    @endphp
                    @if($hasMaterials)
                        <div class="space-y-1">
                            @foreach($production->formula->materials as $material)
                            @php $kebutuhan = $production->production_quantity * ($material->pivot->percentage / 100); @endphp
                            <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0 text-sm">
                                <span class="text-gray-700">{{ $material->material_name }}</span>
                                <div class="text-right">
                                    <span class="font-semibold text-gray-800">{{ number_format($kebutuhan, 2) }} {{ $material->unit }}</span>
                                    <span class="text-xs text-gray-400 ml-1">({{ $material->pivot->percentage }}%)</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-6 text-center text-gray-400">
                            <svg class="w-8 h-8 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="text-xs">Formula ini belum memiliki bahan baku.</p>
                            <a href="{{ route('admin.formula.edit', $production->formula_id) }}" class="mt-2 text-xs text-orange-500 hover:underline">
                                Edit formula untuk tambah bahan →
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ── FORM 1: INPUT QUALITY CONTROL (QC) ── --}}
            @if($production->status === 'progress' && !$qcSudahDilakukan)
            <div class="bg-white rounded-xl border border-blue-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 bg-blue-50 border-b border-blue-200 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    <h3 class="text-sm font-bold text-blue-800">Langkah 2 — Input Quality Control (QC)</h3>
                </div>
                
                <form action="{{ route('admin.qc.store', $production) }}" method="POST" novalidate>
                    @csrf
                    <div class="px-5 py-5 space-y-5">

                        {{-- Input Threshold --}}
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wide mb-1.5 {{ $errors->has('threshold') ? 'text-red-600' : 'text-gray-500' }}">
                                Ambang Kelulusan Non-Kritis <span class="text-gray-400 font-normal">(min 70%, maks 90%)</span>
                            </label>
                            <div class="relative w-40">
                                <input type="number" name="threshold" min="70" max="90" value="{{ old('threshold', 80) }}" required
                                    class="w-full border {{ $errors->has('threshold') ? 'border-red-400 bg-red-50 text-red-900 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-400' }} rounded-lg pl-3 pr-7 py-2.5 text-sm focus:outline-none transition-all">
                                <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-400">%</span>
                            </div>
                            @error('threshold')
                                <p class="mt-1 text-xs text-red-500 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        @if($qcIndicators->count() > 0)
                            {{-- Indikator Kritis --}}
                            @if($qcIndicators->where('is_critical', true)->count() > 0)
                            <div>
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="w-2.5 h-2.5 rounded-full bg-red-500 flex-shrink-0"></span>
                                    <p class="text-xs font-bold uppercase tracking-wide text-gray-700">Indikator Kritis</p>
                                    <span class="text-[10px] font-bold text-red-600 bg-red-50 border border-red-100 px-2 py-0.5 rounded-full uppercase">Wajib Lulus</span>
                                </div>
                                <div class="space-y-2">
                                    @foreach($qcIndicators->where('is_critical', true) as $indicator)
                                    @php $hasError = $errors->has("indicators.{$indicator->id}"); @endphp
                                    <div class="flex items-center justify-between border rounded-lg px-4 py-3 transition-all {{ $hasError ? 'border-red-400 bg-red-50/60 shadow-sm' : 'bg-red-50/40 border-red-100' }}">
                                        <span class="text-sm font-medium {{ $hasError ? 'text-red-900 font-bold' : 'text-gray-700' }}">{{ $indicator->name }}</span>
                                        <div class="flex items-center gap-5">
                                            <label class="flex items-center gap-1.5 cursor-pointer select-none">
                                                <input type="radio" name="indicators[{{ $indicator->id }}]" value="passed" class="accent-green-600" required
                                                    {{ old("indicators.{$indicator->id}") === 'passed' ? 'checked' : '' }}>
                                                <span class="text-xs font-bold text-green-700">Lulus</span>
                                            </label>
                                            <label class="flex items-center gap-1.5 cursor-pointer select-none">
                                                <input type="radio" name="indicators[{{ $indicator->id }}]" value="failed" class="accent-red-600"
                                                    {{ old("indicators.{$indicator->id}") === 'failed' ? 'checked' : '' }}>
                                                <span class="text-xs font-bold text-red-700">Gagal</span>
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            {{-- Indikator Non-Kritis --}}
                            @if($qcIndicators->where('is_critical', false)->count() > 0)
                            <div>
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="w-2.5 h-2.5 rounded-full bg-yellow-400 flex-shrink-0"></span>
                                    <p class="text-xs font-bold uppercase tracking-wide text-gray-700">Indikator Non-Kritis</p>
                                    <span class="text-[10px] font-bold text-yellow-700 bg-yellow-50 border border-yellow-100 px-2 py-0.5 rounded-full uppercase">Akumulasi %</span>
                                </div>
                                <div class="space-y-2">
                                    @foreach($qcIndicators->where('is_critical', false) as $indicator)
                                    @php $hasError = $errors->has("indicators.{$indicator->id}"); @endphp
                                    <div class="flex items-center justify-between border rounded-lg px-4 py-3 transition-all {{ $hasError ? 'border-red-400 bg-red-50/60 shadow-sm' : 'bg-yellow-50/40 border-yellow-100' }}">
                                        <span class="text-sm font-medium {{ $hasError ? 'text-red-900 font-bold' : 'text-gray-700' }}">{{ $indicator->name }}</span>
                                        <div class="flex items-center gap-5">
                                            <label class="flex items-center gap-1.5 cursor-pointer select-none">
                                                <input type="radio" name="indicators[{{ $indicator->id }}]" value="passed" class="accent-green-600" required
                                                    {{ old("indicators.{$indicator->id}") === 'passed' ? 'checked' : '' }}>
                                                <span class="text-xs font-bold text-green-700">Lulus</span>
                                            </label>
                                            <label class="flex items-center gap-1.5 cursor-pointer select-none">
                                                <input type="radio" name="indicators[{{ $indicator->id }}]" value="failed" class="accent-red-600"
                                                    {{ old("indicators.{$indicator->id}") === 'failed' ? 'checked' : '' }}>
                                                <span class="text-xs font-bold text-red-700">Gagal</span>
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        @else
                            <div class="flex items-center gap-3 bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg text-sm">
                                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                Belum ada indikator QC aktif. Tambahkan indikator QC terlebih dahulu di pengaturan.
                            </div>
                        @endif

                        {{-- Input Catatan QC --}}
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wide text-gray-500 mb-1.5">Catatan QC <span class="text-gray-400 font-normal">(opsional)</span></label>
                            <textarea name="notes" rows="2" placeholder="Tambahkan deskripsi atau catatan temuan hasil QC lapangan..."
                                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm text-black focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 resize-none">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <div class="px-5 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                        <button type="submit" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2.5 rounded-lg text-sm shadow transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            Simpan Hasil Rekam QC
                        </button>
                    </div>
                </form>
            </div>
            @endif

            {{-- ── HASIL REKAM QC (Jika Sudah Dilakukan) ── --}}
            @if($qcSudahDilakukan)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">Hasil Quality Control</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="text-center p-4 rounded-xl {{ $qcLayak ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }}">
                        <p class="text-xs text-gray-500 mb-1.5">Status Akhir QC</p>
                        <p class="text-xl font-bold {{ $qcLayak ? 'text-green-700' : 'text-red-700' }}">
                            {{ $qcLayak ? 'LAYAK' : 'TIDAK LAYAK' }}
                        </p>
                    </div>
                    <div class="text-center p-4 rounded-xl bg-gray-50 border border-gray-200">
                        <p class="text-xs text-gray-500 mb-1.5">Persentase Non-Kritis</p>
                        <p class="text-xl font-bold text-gray-800">
                            {{ $production->qc_percentage !== null ? $production->qc_percentage.'%' : '-' }}
                        </p>
                    </div>
                    <div class="text-center p-4 rounded-xl bg-gray-50 border border-gray-200">
                        <p class="text-xs text-gray-500 mb-1.5">Ambang Batas (Threshold)</p>
                        <p class="text-xl font-bold text-gray-800">
                            {{ $production->qc_threshold !== null ? $production->qc_threshold.'%' : '-' }}
                        </p>
                    </div>
                </div>
            </div>
            @endif

            {{-- ── FORM 2: SELESAIKAN & INPUT EXPIRED DATE ── --}}
            @if($production->status === 'progress' && $qcLayak)
            <div class="bg-green-50 border border-green-200 rounded-xl p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-green-800 text-sm">QC Lulus! Input Expired Date untuk Menyelesaikan.</p>
                        <p class="text-xs text-green-600 mt-0.5">
                            Tanggal kadaluarsa wajib diisi agar batch produk tercatat dengan benar ke dalam sistem gudang.
                        </p>
                    </div>
                </div>

                <form action="{{ route('admin.productions.selesai', $production) }}" method="POST" novalidate class="space-y-4">
                    @csrf @method('PUT')
                    
                    <div class="max-w-xs">
                        <label class="block text-xs font-bold uppercase tracking-wide mb-1 {{ $errors->has('expiration_date') ? 'text-red-600' : 'text-gray-700' }}">Tanggal Kadaluarsa (Expired)</label>
                        <input type="date" name="expiration_date" value="{{ old('expiration_date') }}" required 
                            class="w-full border {{ $errors->has('expiration_date') ? 'border-red-400 bg-red-50 text-red-900 focus:ring-red-500' : 'border-green-300 focus:ring-green-400' }} rounded-lg px-3 py-2 text-sm focus:outline-none shadow-sm">
                        @error('expiration_date')
                            <p class="mt-1 text-xs text-red-500 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                            class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-bold px-6 py-3 rounded-lg text-sm shadow-md transition-all active:scale-95">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            Selesaikan Produksi & Tambah Stok
                        </button>
                    </div>
                </form>
            </div>
            @endif

            {{-- BANNER REJECTED --}}
            @if($sudahRejected || $qcTidakLayak)
            <div class="bg-red-50 border border-red-200 rounded-xl p-5 flex items-center gap-3">
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-red-800 text-sm">Produksi ini ditolak karena QC tidak layak.</p>
                    <p class="text-xs text-red-600 mt-0.5">
                        Produksi tidak dapat dilanjutkan. Buat produksi baru jika diperlukan.
                        @if(!$sudahRejected && $qcTidakLayak)
                            <br><span class="font-medium">⚠ Status di database belum terupdate — apply fix controller untuk memperbaiki ini.</span>
                        @endif
                    </p>
                </div>
            </div>
            @endif

            {{-- BANNER SELESAI --}}
            @if($sudahSelesai)
            <div class="bg-green-50 border border-green-200 rounded-xl p-5 flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-green-800 text-sm">Produksi selesai!</p>
                    <p class="text-xs text-green-600 mt-0.5">
                        Stok <strong>{{ $production->product->product_name ?? 'produk' }}</strong> telah bertambah
                        <strong>{{ number_format($production->production_quantity) }} kg</strong>.
                    </p>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-admin-app-layout>