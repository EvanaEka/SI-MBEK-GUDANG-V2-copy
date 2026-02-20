<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Material;
use App\Models\MaterialStock;
use App\Models\Supplier;
use App\Models\Owner;
use App\Models\ActivityLog;
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

        return view('admin.purchase-orders.create', compact('suppliers', 'materials'));
    }

    /**
     * 💾 Simpan PO + item
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'tanggal_pesan' => 'required|date',
            'items' => 'required|array',
            'items.*.material_id' => 'required|exists:materials,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga_satuan' => 'required|numeric|min:0',
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
                'tanggal_pesan' => $request->tanggal_pesan,
                'status' => 'draft',
                'dipesan_oleh_id' => $pemesan->id,
                'dipesan_oleh_type' => get_class($pemesan),
                'dicatat_oleh_id' => $pencatat->id,
                'dicatat_oleh_type' => get_class($pencatat),
                'catatan_owner' => $request->catatan_owner,
            ]);

            foreach ($request->items as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'material_id' => $item['material_id'],
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
        dump($actor);

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
            'items.*.jumlah_diterima' => 'required|integer|min:0',
            'items.*.expired_date' => 'nullable|date',
        ]);

        foreach ($request->items as $data) {

            $item = PurchaseOrderItem::where('id', $data['id'])
                ->where('purchase_order_id', $purchaseOrder->id)
                ->first();

            if (!$item) {
                return back()->with('error', 'Item tidak ditemukan.');
            }

            if ($item->jumlah_diterima !== null && $item->jumlah_diterima != 0) {
                return back()->with('error', 'Item sudah pernah diterima.');
            }
        }

        DB::transaction(function () use ($request, $purchaseOrder) {

            foreach ($request->items as $data) {

                $item = PurchaseOrderItem::where('id', $data['id'])
                    ->where('purchase_order_id', $purchaseOrder->id)
                    ->firstOrFail();

                $jumlahPesan = $item->jumlah;
                $jumlahDiterimaBaru = (int) $data['jumlah_diterima'];

                // Hitung selisih
                $selisih = $jumlahDiterimaBaru - $jumlahPesan;

                // Update item
                $item->update([
                    'jumlah_diterima' => $jumlahDiterimaBaru,
                    'selisih' => $selisih,
                ]);

                /*
                |--------------------------------------------------------------------------
                | Jika PO Material → Buat Batch Stock
                |--------------------------------------------------------------------------
                */
                if ($purchaseOrder->type === 'material') {

                    $material = $item->material;

                    if (!$material) {
                        throw new \Exception('Material tidak ditemukan.');
                    }

                    MaterialStock::create([
                        'material_id' => $material->id,
                        'qty' => $jumlahDiterimaBaru,
                        'received_date' => now(),
                        'expired_date' => $data['expired_date'] ?? null,
                        'created_by' => auth('admin')->id(),
                    ]);

                    $material->increment('stok', $jumlahDiterimaBaru);
                }

                /*
                |--------------------------------------------------------------------------
                | Jika PO Product → Tambah stok product
                |--------------------------------------------------------------------------
                */ elseif ($purchaseOrder->type === 'product') {

                    $product = $item->product;

                    if (!$product) {
                        throw new \Exception('Produk tidak ditemukan.');
                    }

                    $product->increment('stok', $jumlahDiterimaBaru);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Update Status PO
            |--------------------------------------------------------------------------
            */

            // ✅ Perbaikan: cek jumlah_diterima > 0
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