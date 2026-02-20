<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Models\PurchaseOrder;
use App\Models\Order;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class WarehouseDashboardController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | 1. SUMMARY DATA
        |--------------------------------------------------------------------------
        */
        $summary = [
            'total_material' => Material::count(),
            'material_below_rop' => Material::belowRop()->count(),
            'total_product' => Product::count(),
            'product_below_rop' => Product::whereColumn('stok', '<=', 'rop')->count(),
            'total_supplier' => Supplier::count(),
            'total_buyer' => Order::distinct('user_id')->count('user_id'),

        ];

        /*
        |--------------------------------------------------------------------------
        | 2. WARNING ROP LIST
        |--------------------------------------------------------------------------
        */
        $materialsLow = Material::belowRop()
            ->select('id', 'nama_bahan', 'kategori', 'stok', 'pemakaian_rata_rata', 'lead_time', 'safety_stock')
            ->get();


        $productsLow = Product::whereColumn('stok', '<=', 'rop')
            ->select('id', 'kode', 'nama', 'stok', 'rop')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 3. SUPPLIER DISTRIBUTION (Jumlah PO per Supplier)
        |--------------------------------------------------------------------------
        */
        $supplierDistribution = PurchaseOrder::selectRaw('supplier_id, COUNT(*) as total')
            ->groupBy('supplier_id')
            ->with('supplier:id,nama_supplier')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 4. BUYER DISTRIBUTION (Jumlah Transaksi per Buyer)
        |--------------------------------------------------------------------------
        */
        $buyerDistribution = Order::whereIn('status', ['success', 'settlement'])
            ->selectRaw('user_id, COUNT(*) as total')
            ->groupBy('user_id')
            ->with('user:id,name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 5. RECENT ACTIVITY (Operasional Only)
        |--------------------------------------------------------------------------
        */
        $recentActivities = ActivityLog::whereIn('type', [
            'po_created',
            'po_approved',
            'po_received',
            'qc_checked',
            'production_created',
            'allocation_created',
            'disposal_created',
            'order_create',
            'order_update'
        ])
            ->latest()
            ->take(7)
            ->get();

        if (app()->environment('testing')) {
            return response()->json([
                'summary' => $summary,
                'materialsLow' => $materialsLow,
                'productsLow' => $productsLow,
                'supplierDistribution' => $supplierDistribution,
                'buyerDistribution' => $buyerDistribution,
                'recentActivities' => $recentActivities,
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Return View
        |--------------------------------------------------------------------------
        */
        return view('warehouse.dashboard', compact(
            'summary',
            'materialsLow',
            'productsLow',
            'supplierDistribution',
            'buyerDistribution',
            'recentActivities'
        ));
    }
}
