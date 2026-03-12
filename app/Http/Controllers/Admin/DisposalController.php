<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaterialStock;
use App\Models\ProductStock;
use App\Models\Production;
use App\Models\Disposal;
use App\Models\ActivityLog;
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
            'notes'  => 'nullable|string' // Pastikan notes divalidasi
        ]);

        // HAPUS: $disposal = Disposal::create($validated); <- Ini yang bikin error SQL

        if ($stock->qty <= 0) {
            return back()->withErrors([
                'stok' => 'Batch bahan sudah habis atau sudah didisposal.',
            ]);
        }

        DB::beginTransaction();

        try {
            $qty = $stock->qty;

            // 💡 LOGIKA CATATAN OTOMATIS
            $notes = $request->notes;
            if ($request->reason === 'expired') {
                $notes = 'Otomatis dibuang karena batch bahan ini sudah melewati masa kadaluarsa (Expired).';
            }

            // Simpan ke disposals (polymorphic) DENGAN BENAR
            $disposal = $stock->disposals()->create([
                'quantity' => $qty,
                'reason' => $request->reason,
                'notes'      => $notes,
                'created_by' => auth('admin')->id(),
            ]);

            // Kurangi stok summary di materials
            $stock->material->decrement('stok', $qty);

            // Habisin batch
            $stock->update([
                'qty' => 0
            ]);

            $actor = $this->getCurrentActor();
            if ($actor) {
                ActivityLog::create([
                    'actor_id' => $actor->id,
                    'actor_type' => get_class($actor),
                    'type' => 'disposal_created',
                    'module' => 'disposal',
                    // FIX: pakai material->nama_bahan, bukan product->kode karena ini bahan baku
                    'description' => 'Membuat disposal untuk bahan ' . $stock->material->nama_bahan
                ]);
            }

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
            'notes'  => 'nullable|string',
        ]);

        // Cari batch stok produk yang sesuai dengan ID produksi ini
        $batch = ProductStock::where('source', 'production')
            ->where('reference_id', $production->id)
            ->first();

        if (!$batch || $batch->qty <= 0) {
            return back()->withErrors([
                'stok' => 'Produksi sudah tidak memiliki sisa stok di batch ini.',
            ]);
        }

        DB::beginTransaction();

        try {
            // FIX: Gunakan sisa qty di BATCH, bukan total awal produksi
            $qty = $batch->qty; 

            // 💡 LOGIKA CATATAN OTOMATIS
            $notes = $request->notes;
            if ($request->reason === 'expired') {
                $notes = 'Otomatis dibuang karena produk ini sudah melewati masa kadaluarsa (Expired).';
            }

            // Simpan disposal
            $disposal = $production->disposals()->create([
                'quantity' => $qty,
                'reason' => $request->reason,
                'notes'      => $notes,
                'created_by' => auth('admin')->id(),
            ]);

            // Kurangi stok utama produk
            $production->product->decrement('stok', $qty);

            // Nol-kan qty di tabel batch
            $batch->update(['qty' => 0]);

            // Update status produksi jadi rejected
            $production->update([
                'status' => 'rejected',
            ]);

            $actor = $this->getCurrentActor();
            if ($actor) {
                ActivityLog::create([
                    'actor_id' => $actor->id,
                    'actor_type' => get_class($actor),
                    'type' => 'disposal_created',
                    'module' => 'disposal',
                    'description' => 'Membuat disposal untuk produk #' . $production->product->kode
                ]);
            }

            DB::commit();

            return back()->with('success', 'Sisa produk berhasil didisposal.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors([
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * 🔥 Manual Disposal - Product (Batch)
     */
    public function disposeProductBatch(Request $request, ProductStock $stock)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
            'notes'  => 'nullable|string'
        ]);

        if ($stock->qty <= 0) {
            return back()->withErrors([
                'stok' => 'Batch produk sudah habis atau sudah didisposal.',
            ]);
        }

        DB::beginTransaction();

        try {
            $qty = $stock->qty;

            $notes = $request->notes;
            if ($request->reason === 'expired') {
                $notes = 'Otomatis dibuang karena batch produk ini sudah melewati masa kadaluarsa (Expired).';
            }

            // Simpan ke disposals
            $disposal = $stock->disposals()->create([
                'quantity'   => $qty,
                'reason'     => $request->reason,
                'notes'      => $notes,
                'created_by' => auth('admin')->id(),
            ]);

            // Kurangi stok utama produk
            $stock->product->decrement('stok', $qty);

            // Habisin batch
            $stock->update([
                'qty' => 0
            ]);

            $actor = $this->getCurrentActor();
            if ($actor) {
                ActivityLog::create([
                    'actor_id'    => $actor->id,
                    'actor_type'  => get_class($actor),
                    'type'        => 'disposal_created',
                    'module'      => 'disposal',
                    'description' => 'Membuat disposal untuk produk ' . $stock->product->nama
                ]);
            }

            DB::commit();

            return back()->with('success', 'Batch produk berhasil didisposal.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors([
                'error' => $e->getMessage(),
            ]);
        }
    }
}