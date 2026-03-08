<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Material;
use App\Models\Product;
use App\Models\MaterialStock;
use App\Models\Supplier;
use App\Models\Owner;
use App\Models\ActivityLog;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    /**
     * 📄 List semua PO
     */
    public function index()
    {
        $purchaseOrders = PurchaseOrder::with([
            'supplier',
            'dipesanOleh',
            'dicatatOleh'
        ])->latest()->get();

        return view('admin.purchase-orders.index', compact('purchaseOrders'));
    }

    /**
     * ➕ Form buat PO
     */
    public function create()
    {
        $suppliers = Supplier::all();
        $materials = Material::all();
        $products = Product::where('type', 'obat')->get();

        return view('admin.purchase-orders.create', compact('suppliers', 'materials', 'products'));
    }

    /**
     * 💾 Simpan PO + item
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'type' => 'required|in:material,product',
            'tanggal_pesan' => 'required|date',

            'items' => 'required|array|min:1',

            'items.*.material_id' => 'nullable|required_if:type,material|prohibited_if:type,product|exists:materials,id',
            'items.*.product_id' => 'nullable|required_if:type,product|prohibited_if:type,material|exists:products,id',

            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga_satuan' => 'nullable|numeric|min:0',
            'items.*.subtotal' => 'nullable|numeric|min:0',
        ]);
        $po = DB::transaction(function () use ($request) {

            if (Auth::guard('admin')->check()) {
                $pencatat = Auth::guard('admin')->user();
                $guardPencatat = 'admin';
            } elseif (Auth::guard('owner')->check()) {
                $pencatat = Auth::guard('owner')->user();
                $guardPencatat = 'owner';
            } else {
                abort(401, 'Unauthorized');
            }

            if ($guardPencatat === 'admin' && $request->filled('dipesan_oleh_type')) {
                if ($request->dipesan_oleh_type === 'Owner') {
                    $pemesan = Owner::findOrFail($request->dipesan_oleh_id);
                } else {
                    $pemesan = $pencatat;
                }
            } else {
                $pemesan = $pencatat;
            }

            $kode_po = 'PO-' . date('Ymd') . '-' . rand(1000, 9999);

            $po = PurchaseOrder::create([
                'kode_po' => $kode_po,
                'supplier_id' => $request->supplier_id,
                'type' => $request->type,
                'source' => $request->source,
                'tanggal_pesan' => $request->tanggal_pesan,
                'status' => 'draft',
                'dipesan_oleh_id' => $pemesan->id,
                'dipesan_oleh_type' => get_class($pemesan),
                'dicatat_oleh_id' => $pencatat->id,
                'dicatat_oleh_type' => get_class($pencatat),
                'catatan' => $request->catatan,
            ]);

            foreach ($request->items as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'material_id' => $po->type === 'material' ? $item['material_id'] : null,
                    'product_id' => $po->type === 'product' ? $item['product_id'] : null,
                    'jumlah' => $item['jumlah'],
                    'harga_satuan' => $item['harga_satuan'],
                    'subtotal' => $item['jumlah'] * $item['harga_satuan'],
                ]);
            }

            return $po; // 🔥 penting
        });

        $route = Auth::guard('owner')->check()
            ? 'owner.purchase-orders.index'
            : 'admin.purchase-orders.index';

        $actor = $this->getCurrentActor();

        if ($actor) {
            ActivityLog::create([
                'actor_id' => $actor->id,
                'actor_type' => get_class($actor),
                'type' => 'po_created',
                'module' => 'purchase_order',
                'description' => 'Membuat Purchase Order #' . $po->kode_po
            ]);
        }

        return redirect()->route($route)
            ->with('success', 'Purchase Order berhasil dibuat');
    }


    /**
     * 🔍 Detail PO
     */
    public function show($id)
    {
        $po = PurchaseOrder::with([
            'supplier',
            'items.material',
            'dipesanOleh',
            'dicatatOleh'
        ])->findOrFail($id);

        return view('admin.purchase-orders.show', compact('po'));
    }

    /**
     * ✅ Approve PO (Owner Only)
     */
    public function approve(PurchaseOrder $purchaseOrder)
    {
        if (!Auth::guard('owner')->check()) {
            abort(403);
        }


        if ($purchaseOrder->status !== 'draft') {
            throw new \Exception('Purchase Order hanya bisa disetujui jika masih draft');
        }

        $purchaseOrder->update([
            'status' => 'dipesan',
            'tanggal_disetujui' => now(),
        ]);

        // 🔥 Tambahkan ini
        $actor = Auth::guard('owner')->user();

        ActivityLog::create([
            'actor_id' => $actor->id,
            'actor_type' => get_class($actor),
            'type' => 'po_approved',
            'module' => 'purchase_order',
            'description' => 'Owner Menyetujui Purchase Order #' . $purchaseOrder->kode_po
        ]);

        return back()->with('success', 'Purchase Order berhasil disetujui');
    }

    /**
     * 📦 Barang datang → stok masuk (Admin Only)
     */
    public function receive(Request $request, PurchaseOrder $purchaseOrder)
    {
        if (!Auth::guard('admin')->check()) {
            abort(403, 'Hanya admin yang bisa menerima barang');
        }

        if ($purchaseOrder->status === 'diterima') {
            return back()->with('error', 'Purchase Order sudah selesai.');
        }

        if ($purchaseOrder->status !== 'dipesan') {
            return back()->with('error', 'Purchase Order belum disetujui');
        }

        if (empty($request->items)) {
            return back()->with('error', 'Items cannot be empty');
        }

        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:purchase_order_items,id',
            'items.*.jumlah_diterima' => 'required|integer|min:1',
            'items.*.expired_date' => 'nullable|date',
        ]);

        foreach ($request->items as $data) {

            $item = PurchaseOrderItem::where('id', $data['id'])
                ->where('purchase_order_id', $purchaseOrder->id)
                ->first();

            if (!$item) {
                return back()->with('error', 'Item tidak ditemukan.');
            }

            if (!empty($item->jumlah_diterima)) {
                return back()->with('error', 'Item sudah pernah diterima.');
            }
        }

        DB::transaction(function () use ($request, $purchaseOrder) {

            foreach ($request->items as $data) {

                $item = PurchaseOrderItem::where('id', $data['id'])
                    ->where('purchase_order_id', $purchaseOrder->id)
                    ->firstOrFail();

                $jumlahPesan = $item->jumlah;
                $jumlahDiterima = (int) $data['jumlah_diterima'];

                $selisih = $jumlahDiterima - $jumlahPesan;

                // Update item (single receive only)
                $item->update([
                    'jumlah_diterima' => $jumlahDiterima,
                    'selisih' => $selisih,
                ]);

                /*
                |--------------------------------------------------------------------------
                | PO TYPE: MATERIAL
                |--------------------------------------------------------------------------
                */
                if ($purchaseOrder->type === 'material') {

                    $material = $item->material;

                    if (!$material) {
                        throw new \Exception('Material tidak ditemukan.');
                    }

                    MaterialStock::create([
                        'material_id' => $material->id,
                        'qty' => $jumlahDiterima,
                        'received_date' => now(),
                        'expired_date' => $data['expired_date'] ?? null,
                        'created_by' => auth('admin')->id(),
                    ]);

                    $material->stok = $material->materialStocks()->sum('qty');
                    $material->save();

                    StockMovement::create([
                        'stockable_id' => $material->id,
                        'stockable_type' => Material::class,
                        'type' => 'in',
                        'quantity' => $jumlahDiterima,
                        'source' => 'PO',
                        'reference_id' => $purchaseOrder->id,
                        'note' => 'PO ' . $purchaseOrder->kode_po .
                            ($purchaseOrder->catatan ? ' | ' . $purchaseOrder->catatan : ''),
                        'movement_date' => now(),
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | PO TYPE: PRODUCT (HANYA OBAT & SOURCE PEMBELIAN)
                |--------------------------------------------------------------------------
                */ elseif ($purchaseOrder->type === 'product') {

                    $product = $item->product;

                    if (!$product) {
                        throw new \Exception('Product tidak ditemukan.');
                    }

                    if ($product->type !== 'obat') {
                        throw new \Exception('Hanya produk obat yang boleh via PO.');
                    }

                    if ($product->source !== 'pembelian') {
                        throw new \Exception('Produk ini bukan dari pembelian.');
                    }

                    $product->increment('stok', $jumlahDiterima);

                    StockMovement::create([
                        'stockable_id' => $product->id,
                        'stockable_type' => Product::class,
                        'type' => 'in',
                        'quantity' => $jumlahDiterima,
                        'source' => 'PO',
                        'reference_id' => $purchaseOrder->id,
                        'movement_date' => now(),
                    ]);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Update Status PO
            |--------------------------------------------------------------------------
            */

            $allReceived = $purchaseOrder->items()
                ->where(function ($q) {
                    $q->whereNull('jumlah_diterima')
                        ->orWhere('jumlah_diterima', '<=', 0);
                })
                ->count() === 0;

            if ($allReceived) {
                $purchaseOrder->update([
                    'status' => 'diterima',
                    'tanggal_diterima' => now(),
                ]);
            }
        });

        $purchaseOrder->refresh();

        if ($purchaseOrder->status === 'diterima') {

            $actor = $this->getCurrentActor();

            if ($actor) {
                ActivityLog::create([
                    'actor_id' => $actor->id,
                    'actor_type' => get_class($actor),
                    'type' => 'po_received',
                    'module' => 'purchase_order',
                    'description' => 'Menerima Purchase Order #' . $purchaseOrder->kode_po
                ]);
            }
        }

        return back()->with('success', 'Barang berhasil diterima dan stok telah diperbarui');
    }
}