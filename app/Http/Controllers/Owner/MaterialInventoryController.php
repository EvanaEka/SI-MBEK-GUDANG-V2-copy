<?php

namespace App\Http\Controllers\Owner;

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

       return view('owner.inventory.material.index', compact('materials'));
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

       return view('owner.inventory.material.show', compact('material', 'batches', 'movements'));
    }
    
    /**
     * 🔄 Sync stok manual (untuk audit)
     */
    public function sync(Material $material)
    {
        $material->stok = $material->materialStocks()->sum('qty');
        $material->save();

       return redirect()->back()->with('success', 'Stok berhasil disinkronisasi');
    }
}