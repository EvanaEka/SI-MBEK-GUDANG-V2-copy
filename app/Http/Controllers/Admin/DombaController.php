<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Domba;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Carbon\Carbon;
use App\Models\DombaHistory;
use App\Notifications\StatusDijualChanged;

class DombaController extends Controller
{
    public function index()
    {
        $users = User::all();
        $dombas = Domba::paginate(10);
        return view('admin.listdomba', compact('users', 'dombas'));
    }

    public function pemilik()
    {
        $users = User::paginate(7);
        $dombas = Domba::all();
        return view('admin.pemilikdomba', compact('users', 'dombas'));
    }

    private function hitungUmur($tanggal_lahir, $referensi = null)
    {
        $referensi = $referensi ?: now();
        $lahir = Carbon::parse($tanggal_lahir);
        $diff = $lahir->diff($referensi);
        return ['tahun' => $diff->y, 'bulan' => $diff->m, 'hari' => $diff->d];
    }

    public function create()
    {
        $users = User::all();
        $type_dombas = ['Garut', 'Ekor Gemuk', 'Ekor Tipis', 'Texel', 'Dorper'];
        return view('admin.tambahdomba', compact('users', 'type_dombas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'name' => 'required',
            'age' => 'nullable|integer',
            'image' => 'required|file|mimes:jpg,jpeg,png',
            'imageCaption' => 'required',
            'type_domba' => 'required',
            'jenis_kelamin' => 'required|in:Jantan,Betina',
            'weight' => 'required|numeric',
            'tanggal_lahir' => 'required|date|before_or_equal:today',
            'faksin_status' => 'required',
            'healt_status' => 'required',
        ]);

        $filePath = null;

        if ($request->hasFile('image')) {
            $file = $request->file('image');

            $fileName = 'domba_' . time() . '_' . $file->getClientOriginalName();

            $image = Image::read($file)
                ->resize($file->getWidth() * 0.5, $file->getHeight() * 0.5);

            $filePath = 'domba/' . $fileName;

            Storage::disk('public')->put($filePath, (string) $image->encode());
        }

        Domba::create([
            'user_id' => $request->user_id,
            'name' => $request->name,
            'age' => $request->age ?? 0,
            'type_domba' => $request->type_domba,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'weight' => $request->weight,
            'faksin_status' => $request->faksin_status,
            'healt_status' => $request->healt_status,
            'image' => $filePath,
            'imageCaption' => $request->imageCaption,
            'age_now' => 0,
            'weight_now' => $request->weight_now ?? $request->weight,
            'for_sale' => $request->for_sale ?? 'no',
        ]);

        return back()->with('success', 'Data domba berhasil ditambah');
    }

    public function update(Request $request, Domba $domba)
    {
        $oldStatus = $domba->for_sale;
        $oldHarga = $domba->harga;

        $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'nullable|integer',
            'tanggal_lahir' => 'required|date|before_or_equal:today',
            'user_id' => 'required|exists:users,id',
            'type_domba' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:Jantan,Betina',
            'weight' => 'required|numeric',
            'faksin_status' => 'required|string|max:255',
            'healt_status' => 'required|string|max:255',
            'image' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'age_now' => 'nullable|integer',
            'weight_now' => 'nullable|numeric',
            'for_sale' => 'nullable|in:yes,no',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {

            // hapus lama dari storage
            if ($domba->image) {
                Storage::disk('public')->delete($domba->image);
            }

            $file = $request->file('image');

            $fileName = 'dombaU_' . time() . '_' . $file->getClientOriginalName();

            $image = Image::read($file)->scale(50);

            $filePath = 'domba/' . $fileName;

            Storage::disk('public')->put($filePath, (string) $image->encode());

            $data['image'] = $filePath;
        }

        $domba->update($data);

        $newStatus = $domba->for_sale;
        $newHarga = $domba->harga;
        // Cek apakah ada perubahan status dijual atau harga
        $statusBerubah = $oldStatus !== $newStatus;
        $hargaBerubah = $oldHarga != $newHarga; // Menggunakan != untuk handle null/0

        // Kirim notifikasi HANYA jika status dijual atau harga berubah
        if ($statusBerubah || $hargaBerubah) {
            $domba->user->notify(new StatusDijualChanged($domba, $oldStatus, $oldHarga, 'domba'));
        }

        $today = Carbon::today()->toDateString(); // e.g. '2025-06-12'
        DombaHistory::updateOrCreate(
            [
                'domba_id' => $domba->id,
                'tanggal' => $today,
            ],
            [
                'bulan' => Carbon::now()->format('Y-m'),
                'berat' => $request->weight_now,
                'harga' => $request->harga ?? 0,
            ],
        );

        return back()->with('success', 'Data domba berhasil diperbarui');
    }

    public function destroy(Domba $domba)
    {
        if ($domba->image) {
            Storage::disk('public')->delete($domba->image);
        }
        $domba->delete();
        return back()->with('success', 'Data domba berhasil dihapus');
    }

    public function show(Domba $domba)
    {
        $users = User::all();
        return view('admin.showdomba', compact('users', 'domba'));
    }

    public function monitoring($id)
    {
        $domba = Domba::findOrFail($id);
        $selectedMonth = request('bulan', 'all');
        $query = DombaHistory::where('domba_id', $id);
        if ($selectedMonth !== 'all') {
            $query->where('bulan', $selectedMonth);
        }
        $historis = $query->orderBy('bulan')->get();

        $labels = $historis->pluck('bulan')->toArray();
        $beratData = $historis->pluck('berat')->toArray();
        $hargaData = $historis->pluck('harga')->toArray();

        // Reuse view monitoring dengan variable alias
        return view('admin.monitoringdomba', compact('domba', 'historis', 'labels', 'beratData', 'hargaData', 'selectedMonth'));
    }

    public function storeHistory(Request $request, Domba $domba)
    {
        $request->validate([
            'berat' => 'required|numeric',
            'harga' => $domba->for_sale === 'yes' ? 'required|numeric' : 'nullable',
        ]);

        $today = Carbon::today()->toDateString();
        DombaHistory::updateOrCreate(
            ['domba_id' => $domba->id, 'tanggal' => $today],
            [
                'bulan' => $request->input('bulan'),
                'berat' => $request->input('berat'),
                'harga' => $request->input('harga'),
            ],
        );

        return back()->with('success', 'History domba berhasil disimpan');
    }
}
