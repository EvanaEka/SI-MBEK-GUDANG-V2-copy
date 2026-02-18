<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Formula;
use App\Models\MaterialStock;
use App\Models\Production;
use App\Models\Product;
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
                $material->decrement('stok', $kebutuhan);
            }


            // 🏭 SIMPAN DATA PRODUKSI
            Production::create([
                'formula_id' => $formula->id,
                'product_id' => $product->id,
                'qty_produksi' => $request->qty_produksi,
                'production_date' => now(),
                'status' => 'diproses',
                'created_by' => auth('admin')->id(),
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Produksi berhasil dimulai');

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

    public function selesai(Production $production)
    {
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
            $production->product->increment(
                'stok',
                $production->qty_produksi
            );

            $production->update([
                'status' => 'selesai',
            ]);

            DB::commit();

            return back()->with('success', 'Produksi selesai & stok produk bertambah');

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

}
