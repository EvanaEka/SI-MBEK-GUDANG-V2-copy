<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\StockMovement;
use App\Models\Production;
use App\Models\Disposal;
use App\Models\Product;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * 📦 1. Laporan Stok Masuk & Keluar
     */
    public function stock(Request $request)
    {
        $startDate = $request->start_date ?? now()->startOfMonth()->toDateString();
        $endDate   = $request->end_date   ?? now()->toDateString();

        $query = StockMovement::with('stockable')
            ->whereBetween('movement_date', [$startDate, $endDate]);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        $movements = $query->latest('movement_date')->paginate(20)->withQueryString();

        // Summary
        $summary = StockMovement::whereBetween('movement_date', [$startDate, $endDate])
            ->when($request->filled('source'), fn($q) => $q->where('source', $request->source))
            ->selectRaw('
                SUM(CASE WHEN type="in"  THEN quantity ELSE 0 END) as total_masuk,
                SUM(CASE WHEN type="out" THEN quantity ELSE 0 END) as total_keluar,
                COUNT(*) as total_transaksi
            ')
            ->first();

        // Chart data — per hari
        $chartData = StockMovement::whereBetween('movement_date', [$startDate, $endDate])
            ->when($request->filled('source'), fn($q) => $q->where('source', $request->source))
            ->selectRaw('
                DATE(movement_date) as tgl,
                SUM(CASE WHEN type="in"  THEN quantity ELSE 0 END) as masuk,
                SUM(CASE WHEN type="out" THEN quantity ELSE 0 END) as keluar
            ')
            ->groupBy('tgl')
            ->orderBy('tgl')
            ->get();

        // Source options untuk filter
        $sources = StockMovement::distinct()->pluck('source')->sort()->values();

        return view('owner.report.stock', compact(
            'movements', 'summary', 'chartData', 'sources',
            'startDate', 'endDate'
        ));
    }

    /**
     * 🏭 2. Laporan Produksi
     */
    public function production(Request $request)
    {
        $startDate = $request->start_date ?? now()->startOfMonth()->toDateString();
        $endDate   = $request->end_date   ?? now()->toDateString();

        $query = Production::with(['product', 'formula', 'admin'])
            ->whereBetween('production_date', [$startDate, $endDate]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('qc_status')) {
            $query->where('qc_status', $request->qc_status);
        }
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        $productions = $query->latest('production_date')->paginate(20)->withQueryString();

        // Summary
        $summary = Production::whereBetween('production_date', [$startDate, $endDate])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->selectRaw('
                COUNT(*) as total_batch,
                SUM(qty_produksi) as total_qty,
                SUM(CASE WHEN status="selesai" THEN 1 ELSE 0 END) as selesai,
                SUM(CASE WHEN status="diproses" THEN 1 ELSE 0 END) as diproses,
                SUM(CASE WHEN qc_status="lulus" THEN 1 ELSE 0 END) as lulus_qc,
                SUM(CASE WHEN qc_status="gagal" THEN 1 ELSE 0 END) as gagal_qc
            ')
            ->first();

        // Chart — produksi per hari
        $chartData = Production::whereBetween('production_date', [$startDate, $endDate])
            ->selectRaw('
                DATE(production_date) as tgl,
                SUM(qty_produksi) as qty,
                SUM(CASE WHEN qc_status="lulus" THEN 1 ELSE 0 END) as lulus,
                SUM(CASE WHEN qc_status="gagal" THEN 1 ELSE 0 END) as gagal
            ')
            ->groupBy('tgl')
            ->orderBy('tgl')
            ->get();

        $products = Product::orderBy('nama')->get(['id', 'nama', 'type']);

        return view('owner.report.production', compact(
            'productions', 'summary', 'chartData', 'products',
            'startDate', 'endDate'
        ));
    }

    /**
     * 🗑 3. Laporan Disposal
     */
    public function disposal(Request $request)
    {
        $startDate = $request->start_date ?? now()->startOfMonth()->toDateString();
        $endDate   = $request->end_date   ?? now()->toDateString();

        $query = Disposal::with(['disposable', 'admin'])
            ->whereBetween('created_at', [
                $startDate . ' 00:00:00',
                $endDate   . ' 23:59:59',
            ]);

        if ($request->filled('reason')) {
            $query->where('reason', $request->reason);
        }

        $disposals = $query->latest()->paginate(20)->withQueryString();

        // Summary
        $summary = Disposal::whereBetween('created_at', [
                $startDate . ' 00:00:00',
                $endDate   . ' 23:59:59',
            ])
            ->when($request->filled('reason'), fn($q) => $q->where('reason', $request->reason))
            ->selectRaw('
                COUNT(*) as total_disposal,
                SUM(quantity) as total_qty
            ')
            ->first();

        // Chart — disposal per hari
        $chartData = Disposal::whereBetween('created_at', [
                $startDate . ' 00:00:00',
                $endDate   . ' 23:59:59',
            ])
            ->selectRaw('DATE(created_at) as tgl, COUNT(*) as jumlah, SUM(quantity) as qty')
            ->groupBy('tgl')
            ->orderBy('tgl')
            ->get();

        // Reason breakdown
        $reasonBreakdown = Disposal::whereBetween('created_at', [
                $startDate . ' 00:00:00',
                $endDate   . ' 23:59:59',
            ])
            ->selectRaw('reason, COUNT(*) as jumlah, SUM(quantity) as qty')
            ->groupBy('reason')
            ->get();

        $reasons = Disposal::distinct()->pluck('reason')->filter()->sort()->values();

        return view('owner.report.disposal', compact(
            'disposals', 'summary', 'chartData', 'reasonBreakdown', 'reasons',
            'startDate', 'endDate'
        ));
    }

    /**
     * 📊 4. Rekap Bulanan
     */
    public function monthly(Request $request)
    {
        $year = $request->year ?? now()->year;

        // Stok per bulan
        $stockSummary = StockMovement::selectRaw('
                MONTH(movement_date) as bulan,
                SUM(CASE WHEN type="in"  THEN quantity ELSE 0 END) as total_masuk,
                SUM(CASE WHEN type="out" THEN quantity ELSE 0 END) as total_keluar
            ')
            ->whereYear('movement_date', $year)
            ->groupBy(DB::raw('MONTH(movement_date)'))
            ->orderBy('bulan')
            ->get()
            ->keyBy('bulan');

        // Produksi per bulan
        $productionSummary = Production::selectRaw('
                MONTH(production_date) as bulan,
                COUNT(*) as total_batch,
                SUM(qty_produksi) as total_produksi,
                SUM(CASE WHEN status="selesai" THEN 1 ELSE 0 END) as selesai
            ')
            ->whereYear('production_date', $year)
            ->groupBy(DB::raw('MONTH(production_date)'))
            ->orderBy('bulan')
            ->get()
            ->keyBy('bulan');

        // Disposal per bulan
        $disposalSummary = Disposal::selectRaw('
                MONTH(created_at) as bulan,
                COUNT(*) as total_disposal,
                SUM(quantity) as total_qty
            ')
            ->whereYear('created_at', $year)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('bulan')
            ->get()
            ->keyBy('bulan');

        // Gabung ke 12 bulan
        $bulanNames = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $months = collect(range(1, 12))->map(fn($m) => [
            'bulan'          => $m,
            'nama'           => $bulanNames[$m - 1],
            'stok_masuk'     => $stockSummary[$m]->total_masuk     ?? 0,
            'stok_keluar'    => $stockSummary[$m]->total_keluar    ?? 0,
            'total_produksi' => $productionSummary[$m]->total_produksi ?? 0,
            'total_batch'    => $productionSummary[$m]->total_batch    ?? 0,
            'total_disposal' => $disposalSummary[$m]->total_disposal   ?? 0,
            'qty_disposal'   => $disposalSummary[$m]->total_qty        ?? 0,
        ]);

        // Annual totals
        $totals = [
            'stok_masuk'     => $months->sum('stok_masuk'),
            'stok_keluar'    => $months->sum('stok_keluar'),
            'total_produksi' => $months->sum('total_produksi'),
            'total_batch'    => $months->sum('total_batch'),
            'total_disposal' => $months->sum('total_disposal'),
        ];

        $availableYears = collect(range(now()->year - 3, now()->year))->reverse()->values();

        return view('owner.report.monthly', compact(
            'months', 'totals', 'year', 'availableYears'
        ));
    }
}