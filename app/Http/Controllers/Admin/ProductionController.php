<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Formula;
use App\Models\Material;
use App\Models\MaterialStock;
use App\Models\StockMovement;
use App\Models\Production;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductionController extends Controller
{
    /**
     * 1️⃣ Buat produksi (status: diproses)
     * - validasi formula & produk
     * - cek stok bahan baku
     * - kurangi stok bahan baku
     */
    public function store(Request $request)
    {
        $request->validate([
            'formula_id' => 'required|exists:formulas,id',
            'product_id' => 'required|exists:products,id',
            'qty_produksi' => 'required|numeric|min:1',
        ]);

        $formula = Formula::with('materials')->findOrFail($request->formula_id);

        // 🔒 Pastikan produk milik formula tersebut
        $product = Product::where('id', $request->product_id)
            ->where('formula_id', $formula->id)
            ->first();

        if (!$product) {
            return back()->withErrors([
                'product' => 'Produk tidak sesuai dengan formula yang dipilih',
            ]);
        }

        DB::beginTransaction();

        try {
            // 🔍 CEK STOK BAHAN BAKU
            foreach ($formula->materials as $material) {
                $kebutuhan = $request->qty_produksi
                    * ($material->pivot->persentase / 100);

                if ($material->stok < $kebutuhan) {
                    throw new \Exception(
                        "Stok bahan {$material->nama_bahan} tidak mencukupi"
                    );
                }
            }

            // 🏭 SIMPAN DATA PRODUKSI
            $production = Production::create([
                'formula_id' => $formula->id,
                'product_id' => $product->id,
                'qty_produksi' => $request->qty_produksi,
                'production_date' => now(),
                'status' => 'diproses',
                'created_by' => auth('admin')->id(),
            ]);

            // 📉 KURANGI STOK BAHAN BAKU (FIFO)
            foreach ($formula->materials as $material) {

                $kebutuhan = $request->qty_produksi
                    * ($material->pivot->persentase / 100);

                $remainingQty = $kebutuhan;

                // Ambil batch paling lama dulu (FIFO)
                /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\MaterialStock> $batches */
                $batches = MaterialStock::where('material_id', $material->id)
                    ->where('qty', '>', 0)
                    ->where(function ($q) {
                        $q->whereNull('expired_date')
                            ->orWhere('expired_date', '>=', now());
                    })
                    ->orderBy('received_date', 'asc') // FIFO
                    ->lockForUpdate()
                    ->get();


                foreach ($batches as $batch) {

                    if ($remainingQty <= 0)
                        break;

                    if ($batch->qty >= $remainingQty) {
                        // Batch cukup
                        $batch->decrement('qty', $remainingQty);
                        $remainingQty = 0;
                    } else {
                        // Batch tidak cukup → habiskan batch
                        $remainingQty -= $batch->qty;
                        $batch->update(['qty' => 0]);
                    }
                }

                if ($remainingQty > 0) {
                    throw new \Exception(
                        "Batch bahan {$material->nama_bahan} tidak mencukupi"
                    );
                }

                // Tetap kurangi stok summary
                $material->stok = $material->materialStocks()->sum('qty');
                $material->save();

                StockMovement::create([
                    'stockable_id' => $material->id,
                    'stockable_type' => Material::class,
                    'type' => 'out',
                    'quantity' => $kebutuhan,
                    'source' => 'production',
                    'reference_id' => $production->id,
                    'movement_date' => now(),
                ]);
            }

            $actor = $this->getCurrentActor();

            if ($actor) {
                ActivityLog::create([
                    'actor_id' => $actor->id,
                    'actor_type' => get_class($actor),
                    'type' => 'production_created',
                    'module' => 'production',
                    'description' => 'Membuat Produksi #' . $production->id
                ]);
            }


            DB::commit();

            return redirect()->route('admin.productions.index')
        ->with('success', 'Proses produksi berhasil dimulai. Silakan lanjutkan ke tahap QC.');

        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withErrors([
                'produksi' => $e->getMessage(),
            ]);
        }
    }

    public function qc(Request $request, Production $production)
    {
        if ($production->status !== 'diproses') {
            return back()->withErrors([
                'status' => 'Produksi tidak dalam status diproses',
            ]);
        }

        $request->validate([
            'indicators' => 'required|array',
            'qc_threshold' => 'required|numeric|min:70|max:90',
        ]);

        $totalNonCritical = 0;
        $lulusNonCritical = 0;
        $status = 'layak';

        foreach ($request->indicators as $indicatorId => $result) {
            $indicator = \App\Models\QcIndicator::findOrFail($indicatorId);

            // Jika indikator critical gagal → langsung tidak layak
            if ($indicator->is_critical && $result === 'gagal') {
                $status = 'tidak_layak';
            }

            // Hitung non critical untuk persentase
            if (!$indicator->is_critical) {
                $totalNonCritical++;

                if ($result === 'lulus') {
                    $lulusNonCritical++;
                }
            }
        }

        $percentage = $totalNonCritical > 0
            ? ($lulusNonCritical / $totalNonCritical) * 100
            : 100;

        // Jika persentase di bawah threshold → tidak layak
        if ($percentage < $request->qc_threshold) {
            $status = 'tidak_layak';
        }

        DB::transaction(function () use ($production, $status, $percentage, $request) {

            // Update production (hasil QC)
            $production->update([
                'qc_status' => $status,
                'qc_percentage' => $percentage,
                'qc_threshold' => $request->qc_threshold,
                'status'        => $status === 'layak' ? 'diproses' : 'rejected',
            ]);

            // Simpan log QC
            \App\Models\ProductionQc::create([
                'production_id' => $production->id,
                'status' => $status,
                'score_non_kritis' => $percentage,
                'threshold' => $request->qc_threshold,
                'catatan' => $request->catatan ?? null,
                'created_by' => auth('admin')->id(),
            ]);

            // 🔥 AUTO DISPOSAL JIKA QC GAGAL
            if ($status === 'tidak_layak') {

                $production->disposals()->create([
                    'quantity' => $production->qty_produksi,
                    'reason' => 'qc_failed',
                    'notes' => 'Otomatis dibuang karena tidak lolos Quality Control',
                    'created_by' => auth('admin')->id(),
                ]);

                // Ubah status produksi jadi rejected
                $production->update([
                    'status' => 'rejected',
                ]);
            }
        });


        return redirect()->back()
            ->with('success', 'QC berhasil disimpan');
    }

   public function selesai(Request $request, Production $production)
{
    // 1. Validasi Input Tanggal
    $request->validate([
        'expired_date' => 'required|date|after:today',
    ]);

        if ($production->status === 'selesai') {
            return back()->withErrors([
                'status' => 'Produksi sudah selesai',
            ]);
        }

        if ($production->status === 'rejected') {
            return back()->withErrors([
                'status' => 'Produksi sudah ditolak dan tidak bisa diselesaikan',
            ]);
        }

        if (!$production->qc_status) {
            return back()->withErrors([
                'qc' => 'QC belum dilakukan',
            ]);
        }

        if ($production->qc_status !== 'layak') {
            return back()->withErrors([
                'qc' => 'Produksi tidak layak untuk diselesaikan',
            ]);
        }

        DB::beginTransaction();

        try {

        // 2. Update Tanggal Expired di Tabel Productions
        $production->update([
            'status' => 'selesai',
            'expired_date' => $request->expired_date,
        ]);

            // 1️⃣ Tambah stok summary
            $production->product->increment(
                'stok',
                $production->qty_produksi
            );

            // 2️⃣ INSERT KE PRODUCT_STOCKS (BATCH)
            ProductStock::create([
                'product_id' => $production->product_id,
                'qty' => $production->qty_produksi,
                'source' => 'production',
                'reference_id' => $production->id,
                'received_date' => $production->production_date ?? now(),
                'expired_date' => $request->expired_date,
            ]);

            // 3️⃣ Stock movement log
            StockMovement::create([
                'stockable_id' => $production->product->id,
                'stockable_type' => Product::class,
                'type' => 'in',
                'quantity' => $production->qty_produksi,
                'source' => 'production',
                'reference_id' => $production->id,
                'movement_date' => now(),
            ]);

            // 4️⃣ Update status produksi
            $production->update([
                'status' => 'selesai',
            ]);

            DB::commit();

            $actor = $this->getCurrentActor();

            if ($actor) {
                ActivityLog::create([
                    'actor_id' => $actor->id,
                    'actor_type' => get_class($actor),
                    'type' => 'production_finished',
                    'module' => 'production',
                    'description' => 'Menyelesaikan Produksi #' . $production->id
                ]);
            }


            DB::commit();

            return back()->with('success', 'Produksi selesai & stok produk bertambah');

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
/** 
 * Route: GET /admin/productions  →  admin.productions.index
 */
public function index()
{
    $productions = Production::with(['product', 'formula'])
        ->latest()
        ->get();

    return view('admin.production.index', compact('productions'));
}

/**
 * Form buat produksi baru
 * Route: GET /admin/productions/create  →  admin.productions.create
 */
public function create()
{
    $formulas = Formula::where('is_active', true)->get();

    // Map formula_id → list produk (untuk JS dynamic dropdown)
    $formulaProducts = [];
    $formulaMaterials = [];

    foreach ($formulas as $formula) {
        // Produk yang punya formula_id ini
        $formulaProducts[$formula->id] = \App\Models\Product::where('formula_id', $formula->id)
            ->select('id', 'kode', 'nama')
            ->get()
            ->toArray();

        // Komposisi bahan untuk preview
        $formulaMaterials[$formula->id] = $formula->materials
            ->map(fn($m) => [
                'nama_bahan'  => $m->nama_bahan,
                'satuan'      => $m->satuan,
                'persentase'  => $m->pivot->persentase,
            ])
            ->toArray();
    }

    return view('admin.production.create', compact(
        'formulas',
        'formulaProducts',
        'formulaMaterials'
    ));
}

public function show(Production $production)
{
    // PENTING: eager load formula->materials dengan pivot (persentase)
    $production->load([
        'product',
        'formula.materials', // ini yang bikin komposisi bahan muncul
    ]);

    $qcIndicators = \App\Models\QcIndicator::active()
        ->orderBy('is_critical', 'desc')
        ->orderBy('name', 'asc')
        ->get();

    return view('admin.production.show', compact('production', 'qcIndicators'));
}

}
