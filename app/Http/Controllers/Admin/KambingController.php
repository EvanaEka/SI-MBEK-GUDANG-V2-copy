<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kambing;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
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
            'image' => 'required|file|mimes:jpg,jpeg,png',
            'imageCaption' => 'required',
            'type_goat' => 'required',
            'jenis_kelamin' => 'required|in:Jantan,Betina',
            'weight' => 'required|numeric',
            'tanggal_lahir' => 'required|date|before_or_equal:today',
            'faksin_status' => 'required',
            'healt_status' => 'required',

        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = "raman" .  time() . '_' . $file->getClientOriginalName();
            $image = Image::read($file);

            $image->resize(
                $image->width() * 0.5,
                $image->height() * 0.5
            );
            $fileName = "raman" . time() . '_' . $file->getClientOriginalName();
            $image = Image::read($file);

            $image->resize(
            $image->width() * 0.5,
            $image->height() * 0.5
            );

            // Tentukan folder
            $uploadPath = public_path('uploads');

            // Kalau folder belum ada, buat otomatis
            if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0777, true);
            }

            // Path lengkap file
            $filePath = 'uploads/' . $fileName;

            // Save gambar
            $image->save($uploadPath . '/' . $fileName);

            }

        Kambing::create([
            'user_id' => $request->user_id,
            'name' => $request->name,
            'age' => $request->age ?? 0,
            'image' => $filePath,
            'imageCaption' => $request->imageCaption,
            'type_goat' => $request->type_goat,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'weight' => $request->weight,
            'faksin_status' => $request->faksin_status,
            'healt_status' => $request->healt_status,
            'age_now' => Carbon::parse($request->tanggal_lahir)->age,
            'weight_now' =>  $request->weight_now ?? $request->weight,
            'for_sale' => $request->for_sale ?? 'no',
        ]);

        return redirect()->back()->with('success', 'Data kambing berhasil ditambah');
    }

    public function update(Request $request, Kambing $kambing)
    {
        $oldStatus = $kambing->for_sale;
        $oldHarga = $kambing->harga;

        $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'nullable|integer',
            'tanggal_lahir' => 'required|date|before_or_equal:today',
            'user_id' => 'required|exists:users,id',
            'type_goat' => 'required|string|max:255',
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
            if ($kambing->image && file_exists(public_path($kambing->image))) {
                unlink(public_path($kambing->image));
            }

            $file = $request->file('image');
            $fileName = "ramanU" . time() . '_' . $file->getClientOriginalName();
            $image = Image::read($file);

            $image->resize(
                $image->width() * 0.5,
                $image->height() * 0.5
            );

            // Tentukan folder
            $uploadPath = public_path('uploads');

            // Buat folder kalau belum ada
            if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0777, true);
            }

            $filePath = 'uploads/' . $fileName;

            // Save
            $image->save($uploadPath . '/' . $fileName);

$data['image'] = $filePath;

        }

        $kambing->update($data);

        
        $newStatus = $kambing->for_sale;
        $newHarga = $kambing->harga;
        // Cek apakah ada perubahan status dijual atau harga
        $statusBerubah = $oldStatus !== $newStatus;
        $hargaBerubah = $oldHarga != $newHarga; // Menggunakan != untuk handle null/0

        // Kirim notifikasi HANYA jika status dijual atau harga berubah
        if ($statusBerubah || $hargaBerubah) {
            $kambing->user->notify(new StatusDijualChanged($kambing, $oldStatus, $oldHarga, 'kambing'));
        }

       $today = Carbon::today()->toDateString(); // e.g. '2025-06-12'
        KambingHistory::updateOrCreate(
            [
                'kambing_id' => $kambing->id,
                'tanggal'    => $today,
            ],
            [
                'bulan' => Carbon::now()->format('Y-m'),
                'berat' => $request->weight_now,
                'harga' => $request->harga ?? 0,
            ]
        );


        return redirect()->back()->with('success', 'Data kambing berhasil diperbarui');
    }

    public function destroy(Kambing $kambing)
    {
        if ($kambing->image && file_exists(public_path($kambing->image))) {
            unlink(public_path($kambing->image));
        }

        $kambing->delete();

        return redirect()->back()->with('success', 'Data kambing berhasil dihapus');
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

    $labels    = $historis->pluck('bulan')->toArray();
    $beratData = $historis->pluck('berat')->toArray();
    $hargaData = $historis->pluck('harga')->toArray();

    return view('admin.monitoring', compact(
        'kambing','historis','labels','beratData','hargaData','selectedMonth'
    ));
    }

    public function show($kambing)
    {
        $users = User::all();
        $kambings = Kambing::find($kambing);

        if (!$kambings) {
            return redirect()->back()->with('error', 'Kambing tidak ditemukan.');
        }

        $umurAwal = $this->hitungUmur(
            $kambings->tanggal_lahir, 
            $kambings->created_at
        );
        
        $umurSekarang = $this->hitungUmur(
            $kambings->tanggal_lahir
        );
        
        $selectedMonth = request('bulan', date('Y-m'));
        $historis = KambingHistory::where('kambing_id', $kambing)
            ->where('bulan', $selectedMonth) // Perbaikan: gunakan exact match
            ->get();

        return view('admin.showkambing', compact('users', 'kambings', 'umurAwal', 'umurSekarang', 'historis', 'selectedMonth'));
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
                'tanggal'    => $today,
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