<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaterialStock;
use App\Models\Production;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DisposalController extends Controller
{
    /**
     * 🔥 Manual Disposal - Material (Batch)
     */
    public function disposeMaterial(Request $request, MaterialStock $stock)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        if ($stock->qty <= 0) {
            return back()->withErrors([
                'stok' => 'Batch bahan sudah habis atau sudah didisposal.',
            ]);
        }

        DB::beginTransaction();

        try {
            $qty = $stock->qty;

            // Simpan ke disposals (polymorphic)
            $stock->disposals()->create([
                'quantity' => $qty,
                'reason' => $request->reason,
                'created_by' => auth('admin')->id(),
            ]);

            // Kurangi stok summary di materials
            $stock->material->decrement('stok', $qty);

            // Habisin batch
            $stock->update([
                'qty' => 0
            ]);

            DB::commit();

            return back()->with('success', 'Bahan berhasil didisposal.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors([
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * 🔥 Manual Disposal - Production (Produk Jadi)
     */
    public function disposeProduction(Request $request, Production $production)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);
        
        if ($production->status !== 'selesai') {
            return back()->withErrors([
                'status' => 'Produksi belum selesai atau tidak valid untuk disposal.',
            ]);
        }

        if ($production->qty_produksi <= 0) {
            return back()->withErrors([
                'stok' => 'Produksi sudah tidak memiliki stok.',
            ]);
        }

        DB::beginTransaction();

        try {
            $qty = $production->qty_produksi;

            // Simpan disposal
            $production->disposals()->create([
                'quantity' => $qty,
                'reason' => $request->reason,
                'created_by' => auth('admin')->id(),
            ]);

            // Kurangi stok produk
            $production->product->decrement('stok', $qty);

            // Update status produksi (opsional tapi direkomendasikan)
            $production->update([
                'status' => 'rejected',
            ]);

            DB::commit();

            return back()->with('success', 'Produk berhasil didisposal.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors([
                'error' => $e->getMessage(),
            ]);
        }
    }
}
