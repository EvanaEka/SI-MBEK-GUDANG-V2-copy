<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Models\PurchaseOrder;
use App\Models\Order;
use App\Models\ActivityLog;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehouseDashboardController extends Controller
{
    public function index()
    {
        // ── 1. Summary ──────────────────────────────────────────
        $summary = [
            'total_material'     => Material::count(),
            'material_below_rop' => Material::belowRop()->count(),
            'total_product'      => Product::count(),
            'product_below_rop'  => Product::whereColumn('stok', '<=', 'rop')->count(),
            'total_supplier'     => Supplier::count(),
            'total_buyer'        => Order::distinct('user_id')->count('user_id'),
        ];

        // ── 2. ROP Warnings ─────────────────────────────────────
        $materialsLow = Material::belowRop()
            ->select('id', 'nama_bahan', 'kategori', 'stok', 'pemakaian_rata_rata', 'lead_time', 'safety_stock')
            ->get();

        $productsLow = Product::whereColumn('stok', '<=', 'rop')
            ->select('id', 'kode', 'nama', 'stok', 'rop', 'type')
            ->get();

        // ── 3. Supplier Distribution ─────────────────────────────
        $supplierDistribution = PurchaseOrder::selectRaw('supplier_id, COUNT(*) as total')
            ->groupBy('supplier_id')
            ->with('supplier:id,nama_supplier')
            ->get();

        // ── 4. Buyer Distribution ────────────────────────────────
        $buyerDistribution = Order::whereIn('status', ['success', 'settlement'])
            ->selectRaw('user_id, COUNT(*) as total')
            ->groupBy('user_id')
            ->with('user:id,name')
            ->orderByDesc('total')
            ->take(8)
            ->get();

        // ── 5. Recent Activities ─────────────────────────────────
        $recentActivities = ActivityLog::whereIn('type', [
            'po_created',
            'po_approved',
            'po_received',
            'qc_checked',
            'production_created',
            'allocation_created',
            'disposal_created',
            'order_create',
            'order_update',
        ])->latest()->take(7)->get();

        // ── 6. Movement Chart — 7 hari terakhir ─────────────────
        $movementChart = StockMovement::selectRaw('
                DATE(movement_date) as tgl,
                SUM(CASE WHEN type="in"  THEN quantity ELSE 0 END) as masuk,
                SUM(CASE WHEN type="out" THEN quantity ELSE 0 END) as keluar
            ')
            ->where('movement_date', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('tgl')
            ->orderBy('tgl')
            ->get();

        // ── 7. PO Status Summary ─────────────────────────────────
        $poSummary = PurchaseOrder::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        if (app()->environment('testing')) {
            return response()->json(compact(
                'summary', 'materialsLow', 'productsLow',
                'supplierDistribution', 'buyerDistribution', 'recentActivities'
            ));
        }

        return view('owner.warehouse.dashboard', compact(
            'summary',
            'materialsLow',
            'productsLow',
            'supplierDistribution',
            'buyerDistribution',
            'recentActivities',
            'movementChart',
            'poSummary',
        ));
    }

    public function activityLog(Request $request)
    {
        $query = ActivityLog::with('actor')->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('dari')) {
            $query->whereDate('created_at', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('created_at', '<=', $request->sampai);
        }

        $logs    = $query->paginate(25)->withQueryString();
        $types   = ActivityLog::distinct()->pluck('type')->sort()->values();
        $modules = ActivityLog::distinct()->pluck('module')->filter()->sort()->values();

        return view('owner.warehouse.activity-log', compact('logs', 'types', 'modules'));
    }
}