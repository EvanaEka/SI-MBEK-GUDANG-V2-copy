<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockMovement;
use App\Models\Production;
use App\Models\Disposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * 📦 1️⃣ Laporan Stok Masuk & Keluar
     */
    public function stock(Request $request)
    {
        $query = StockMovement::with('stockable');

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('movement_date', [
                $request->start_date,
                $request->end_date
            ]);
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->source) {
            $query->where('source', $request->source);
        }

        $movements = $query->latest()->paginate(20);

        return response()->json([
            'success' => true,
            'message' => 'Laporan stok berhasil diambil',
            'filters' => $request->only(['start_date', 'end_date', 'type', 'source']),
            'data' => $movements
        ]);
    }

    /**
     * 🏭 2️⃣ Laporan Produksi
     */
    public function production(Request $request)
    {
        $query = Production::with(['product', 'formula']);

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('production_date', [
                $request->start_date,
                $request->end_date
            ]);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->qc_status) {
            $query->where('qc_status', $request->qc_status);
        }

        $productions = $query->latest()->paginate(20);

        return response()->json([
            'success' => true,
            'message' => 'Laporan produksi berhasil diambil',
            'filters' => $request->only(['start_date', 'end_date', 'status', 'qc_status']),
            'data' => $productions
        ]);
    }

    /**
     * 🗑 3️⃣ Laporan Disposal
     */
    public function disposal(Request $request)
    {
        $query = Disposal::with(['disposable']);

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [
                $request->start_date,
                $request->end_date
            ]);
        }

        if ($request->reason) {
            $query->where('reason', $request->reason);
        }

        $disposals = $query->latest()->paginate(20);

        return response()->json([
            'success' => true,
            'message' => 'Laporan disposal berhasil diambil',
            'filters' => $request->only(['start_date', 'end_date', 'reason']),
            'data' => $disposals
        ]);
    }

    /**
     * 📊 4️⃣ Rekap Bulanan
     */
    public function monthly(Request $request)
    {
        $year = $request->year ?? now()->year;

        $stockSummary = StockMovement::select(
            DB::raw('MONTH(movement_date) as bulan'),
            DB::raw('SUM(CASE WHEN type = "in" THEN quantity ELSE 0 END) as total_masuk'),
            DB::raw('SUM(CASE WHEN type = "out" THEN quantity ELSE 0 END) as total_keluar')
        )
            ->whereYear('movement_date', $year)
            ->groupBy(DB::raw('MONTH(movement_date)'))
            ->orderBy('bulan')
            ->get();

        $productionSummary = Production::select(
            DB::raw('MONTH(production_date) as bulan'),
            DB::raw('SUM(qty_produksi) as total_produksi')
        )
            ->whereYear('production_date', $year)
            ->where('status', 'selesai')
            ->groupBy(DB::raw('MONTH(production_date)'))
            ->orderBy('bulan')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Rekap bulanan berhasil diambil',
            'year' => $year,
            'data' => [
                'stock_summary' => $stockSummary,
                'production_summary' => $productionSummary
            ]
        ]);
    }
}