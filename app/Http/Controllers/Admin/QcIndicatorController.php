<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QcIndicator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QcIndicatorController extends Controller
{
    /**
     * Tampilkan daftar semua indikator QC.
     */
    public function index()
    {
        $indicators = QcIndicator::orderByDesc('is_critical')
            ->orderBy('name')
            ->get();

        return view('admin.qc-indicators.index', compact('indicators'));
    }

    /**
     * Form tambah indikator baru.
     */
    public function create()
    {
        return view('admin.qc-indicators.create');
    }

    /**
     * Simpan indikator QC baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:qc_indicators,name',
            'is_critical' => 'required|boolean',
        ], [
            'name.required'        => 'Nama indikator wajib diisi.',
            'name.unique'          => 'Nama indikator ini sudah terdaftar.',
            'is_critical.required' => 'Tipe indikator wajib dipilih.',
        ]);

        // Selalu aktif saat pertama kali dibuat
        $validated['is_active'] = true;

        QcIndicator::create($validated);

        return redirect()
            ->route('admin.qc-indicators.index')
            ->with('success', 'Indikator QC "' . $validated['name'] . '" berhasil ditambahkan.');
    }

    /**
     * Halaman detail + edit indikator.
     */
    public function show(QcIndicator $qcIndicator)
    {
        return view('admin.qc-indicators.show', compact('qcIndicator'));
    }

    /**
     * Perbarui data indikator QC.
     */
    public function update(Request $request, QcIndicator $qcIndicator)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('qc_indicators', 'name')->ignore($qcIndicator->id),
            ],
            'is_critical' => 'required|boolean',
            'is_active'   => 'required|boolean',
        ], [
            'name.required'        => 'Nama indikator wajib diisi.',
            'name.unique'          => 'Nama indikator ini sudah terdaftar.',
            'is_critical.required' => 'Tipe indikator wajib dipilih.',
            'is_active.required'   => 'Status aktif wajib dipilih.',
        ]);

        $qcIndicator->update($validated);

        return redirect()
            ->route('admin.qc-indicators.show', $qcIndicator)
            ->with('success', 'Indikator pengecekan kualitas berhasil diperbarui.');
    }

    /**
     * Hapus indikator QC.
     */
    public function destroy(QcIndicator $qcIndicator)
    {
        $name = $qcIndicator->name;
        $qcIndicator->delete();

        return redirect()
            ->route('admin.qc-indicators.index')
            ->with('success', 'Indikator QC "' . $name . '" berhasil dihapus.');
    }
}