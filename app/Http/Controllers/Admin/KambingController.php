<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kambing;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\KambingHistory;
use Illuminate\Support\Facades\Mail;
use App\Notifications\StatusDijualChanged;

class KambingController extends Controller
{
    public function index()
    {
        $users = User::all();
        $kambings = Kambing::paginate(10);
        return view('admin.listkambing', compact('users', 'kambings'));
    }
    public function pemilik()
    {
        $users = User::paginate(7);
        $kambings = Kambing::all();
        return view('admin.pemilikkambing', compact('users', 'kambings'));
    }

    private function hitungUmur($tanggal_lahir, $referensi = null)
    {
        $referensi = $referensi ?: now();
        $lahir = Carbon::parse($tanggal_lahir);
        $diff = $lahir->diff($referensi);

        return [
            'tahun' => $diff->y,
            'bulan' => $diff->m,
            'hari' => $diff->d
        ];
    }

    public function create()
    {
        $users = User::all();
        $kambings = Kambing::all();
        $type_goats = ['Etawa', 'Boer', 'Skeang', 'Saaren'];

        return view('admin.tambahkambing', compact('users', 'kambings', 'type_goats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'name' => 'required',
            'age' => 'nullable|integer',
            'images' => 'nullable|array|max:10',
            'images.*' => 'file|mimes:jpg,jpeg,png,webp|max:2048',
            'type_goat' => 'required',
            'jenis_kelamin' => 'required|in:Jantan,Betina',
            'weight' => 'required|numeric',
            'tanggal_lahir' => 'required|date|before_or_equal:today',
            'faksin_status' => 'required',
            'healt_status' => 'required',

        ]);

        $kambing = Kambing::create([
            'user_id' => $request->user_id,
            'name' => $request->name,
            'age' => $request->age ?? 0,
            'image' => '',
            'imageCaption' => '',
            'type_goat' => $request->type_goat,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'weight' => $request->weight,
            'faksin_status' => $request->faksin_status,
            'healt_status' => $request->healt_status,
            'age_now' => Carbon::parse($request->tanggal_lahir)->age,
            'weight_now' => $request->weight_now ?? $request->weight,
            'for_sale' => $request->for_sale ?? 'no',
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                $fileName = "kambing_" . time() . '_' . $index . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('kambing', $fileName, 'public');

                $kambing->media()->create([
                    'file_name'  => $file->getClientOriginalName(),
                    'file_path'  => $filePath,
                    'mime_type'  => $file->getMimeType(),
                    'file_size'  => $file->getSize(),
                    'type'       => 'image',
                    'is_primary' => $index === 0,
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()->route('admin.listkambing')->with('success', 'Data kambing berhasil ditambah');
    }

    public function update(Request $request, Kambing $kambing)
    {
        $oldStatus = $kambing->for_sale;
        $oldHarga = $kambing->harga;

        $request->validate([
            'name' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date|before_or_equal:today',
            'user_id' => 'required|exists:users,id',
            'type_goat' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:Jantan,Betina',
            'weight' => 'required|numeric',
            'faksin_status' => 'required|string|max:255',
            'healt_status' => 'required|string|max:255',
            'images' => 'nullable|array|max:10',
            'images.*' => 'file|mimes:jpg,jpeg,png,webp|max:2048',
            'weight_now' => 'nullable|numeric',
            'for_sale' => 'nullable|in:yes,no',
            'harga' => 'nullable|numeric',
        ]);

        $data = $request->except(['images', 'hapus_media']);

        if ($request->has('hapus_media')) {
            $mediaYangAkanDihapus = $kambing->media()->whereIn('id', $request->hapus_media)->get();
            foreach ($mediaYangAkanDihapus as $media) {
                Storage::disk('public')->delete($media->file_path);
                $media->delete();
            }
        }

        if ($request->hasFile('images')) {
            $startingIndex = $kambing->media()->count();
            foreach ($request->file('images') as $index => $file) {
                $fileName = "kambingU_" . time() . '_' . $index . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('kambing', $fileName, 'public');

                $kambing->media()->create([
                    'file_name'  => $file->getClientOriginalName(),
                    'file_path'  => $filePath,
                    'mime_type'  => $file->getMimeType(),
                    'file_size'  => $file->getSize(),
                    'type'       => 'image',
                    'is_primary' => ($startingIndex === 0 && $index === 0),
                    'sort_order' => $startingIndex + $index,
                ]);
            }
        }
        
        $kambing->update($data);

        $newStatus = $kambing->for_sale;
        $newHarga = $kambing->harga;
        
        $statusBerubah = $oldStatus !== $newStatus;
        $hargaBerubah = $oldHarga != $newHarga; 

        if ($statusBerubah || $hargaBerubah) {
            $kambing->user->notify(new StatusDijualChanged($kambing, $oldStatus, $oldHarga, 'kambing'));
        }

        $today = Carbon::today()->toDateString(); 
        KambingHistory::updateOrCreate(
            [
                'kambing_id' => $kambing->id,
                'tanggal' => $today,
            ],
            [
                'bulan' => Carbon::now()->format('Y-m'),
                'berat' => $request->weight_now ?? $kambing->weight_now,
                'harga' => $request->harga ?? 0,
            ]
        );

        return redirect()->back()->with('success', 'Data kambing berhasil diperbarui');
    }

    public function destroy(Kambing $kambing)
    {
        foreach ($kambing->media as $media) {
            Storage::disk('public')->delete($media->file_path);
        }
        $kambing->media()->delete();

        if ($kambing->image) {
            Storage::disk('public')->delete($kambing->image);
        }

        $kambing->delete();
        return redirect()->route('admin.listkambing')->with('success', 'Data kambing berhasil dihapus');
    }

    // For monitoring
    public function monitoring($id)
    {
        $kambing = Kambing::findOrFail($id);
        $selectedMonth = request('bulan', 'all');
        $query = KambingHistory::where('kambing_id', $id);
        if ($selectedMonth !== 'all') {
            $query->where('bulan', $selectedMonth);
        }
        $historis = $query->orderBy('bulan')->get();

        $labels = $historis->pluck('bulan')->toArray();
        $beratData = $historis->pluck('berat')->toArray();
        $hargaData = $historis->pluck('harga')->toArray();

        return view('admin.monitoring', compact(
            'kambing',
            'historis',
            'labels',
            'beratData',
            'hargaData',
            'selectedMonth'
        ));
    }

    public function show($kambing)
    {
        $users = User::all();
        $kambings = Kambing::findOrFail($kambing);

        $selectedMonth = request('bulan', date('Y-m'));
        $historis = KambingHistory::where('kambing_id', $kambing)
            ->where('bulan', $selectedMonth)
            ->get();

        return view('admin.showkambing', compact('users', 'kambings', 'historis', 'selectedMonth'));
    }

    public function storeHistory(Request $request, Kambing $kambing)
    {
        $request->validate([
            'berat' => 'required|numeric',
            'harga' => $kambing->for_sale === 'yes' ? 'required|numeric' : 'nullable',
        ]);

        $today = Carbon::today()->toDateString();

        KambingHistory::updateOrCreate(
            [
                'kambing_id' => $kambing->id,
                'tanggal' => $today,
            ],
            [
                'bulan' => $request->bulan,  // jika masih butuh filter per bulan custom
                'berat' => $request->berat,
                'harga' => $request->harga,
            ]
        );

        return back()->with('success', 'Data monitoring berhasil disimpan');
    }
}