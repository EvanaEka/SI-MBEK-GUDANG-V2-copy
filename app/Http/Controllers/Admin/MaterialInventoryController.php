<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\MaterialStock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaterialInventoryController extends Controller
{
    /**
     * 📊 List semua bahan + stok
     */
    public function index()
    {
        $materials = Material::orderBy('nama_bahan')->get();

        return response()->json($materials);
    }

    /**
     * 🔍 Detail 1 bahan (batch + movement)
     */
    public function show(Material $material)
    {
        $batches = $material->materialStocks()
            ->orderBy('received_date', 'asc')
            ->get();

        $movements = $material->stockMovements()
            ->latest()
            ->get();

        return response()->json([
            'material' => $material,
            'batches' => $batches,
            'movements' => $movements
        ]);
    }

    /**
     * ➕ / ➖ Adjustment Manual
     */
    public function adjust(Request $request, Material $material)
    {
        $request->validate([
            'type' => 'required|in:in,out',
            'quantity' => 'required|numeric|min:1',
            'expired_date' => 'nullable|date',
            'note' => 'nullable|string'
        ]);

        DB::transaction(function () use ($request, $material) {

            if ($request->type === 'in') {

                // Tambah batch baru
                MaterialStock::create([
                    'material_id' => $material->id,
                    'qty' => $request->quantity,
                    'received_date' => now(),
                    'expired_date' => $request->expired_date,
                    'created_by' => auth('admin')->id(),
                ]);

            } else {

                $remaining = $request->quantity;

                /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\MaterialStock> $batches */
                $batches = MaterialStock::where('material_id', $material->id)
                    ->where('qty', '>', 0)
                    ->orderBy('received_date', 'asc')
                    ->lockForUpdate()
                    ->get();

                foreach ($batches as $batch) {

                    if ($remaining <= 0)
                        break;

                    if ($batch->qty >= $remaining) {
                        $batch->decrement('qty', $remaining);
                        $remaining = 0;
                    } else {
                        $remaining -= $batch->qty;
                        $batch->update(['qty' => 0]);
                    }
                }

                if ($remaining > 0) {
                    throw new \Exception('Stok tidak mencukupi');
                }
            }

            // 🔥 Sync summary stok
            $material->stok = $material->materialStocks()->sum('qty');
            $material->save();

            // Catat movement
            StockMovement::create([
                'stockable_id' => $material->id,
                'stockable_type' => Material::class,
                'type' => $request->type,
                'quantity' => $request->quantity,
                'source' => 'adjustment',
                'movement_date' => now(),
                'created_by' => auth('admin')->id(),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Stok berhasil disesuaikan'
        ]);
    }

    /**
     * 🔄 Sync stok manual (untuk audit)
     */
    public function sync(Material $material)
    {
        $material->stok = $material->materialStocks()->sum('qty');
        $material->save();

        return response()->json([
            'success' => true,
            'message' => 'Stok berhasil disinkronisasi'
        ]);
    }
}