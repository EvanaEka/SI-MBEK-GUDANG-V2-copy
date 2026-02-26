<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Models\Kambing;
use App\Models\Domba;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\ActivityLog;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

// Tambahkan import Midtrans
use Midtrans\Config;
use Midtrans\Snap;

class OrderController extends Controller
{
    /**
     * Constructor: atur konfigurasi Midtrans
     */
    public function __construct()
    {
        // Inisialisasi konfigurasi Midtrans berdasarkan config/midtrans.php
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    /**
     * Display a listing of the resource (halaman list order).
     */
    public function index()
    {
        $kambings = Kambing::where('for_sale', 'yes')->get();
        $dombas = Domba::where('for_sale', 'yes')->get();
        $products = Product::whereHas('stocks', function ($q) {
            $q->where('qty', '>', 0);
        })->get();

        return view('order', compact('kambings', 'dombas', 'products'));
    }

    /**
     * Display the specified resource (halaman detail order berdasarkan category dan id).
     */
    public function show($category, $id)
    {
        switch ($category) {
            case 'kambing':
                $item = Kambing::findOrFail($id);
                break;

            case 'domba':
                $item = Domba::findOrFail($id);
                break;

            case 'product':
                $item = Product::findOrFail($id);
                break;

            default:
                abort(404);
        }

        return view('order', [
            'item' => $item,
            'produk' => $item,
            'category' => $category,
        ]);
    }

    /**
     * Generate Snap Token (Midtrans) berdasarkan data request.
     * Route: POST /midtrans/token
     */
    public function getSnapToken(Request $request)
    {
        // 1. Validasi request
        $validated = $request->validate([
            'produk_id' => 'required|integer',
            'category' => 'required|string',
            'email' => 'required|email',
            'name' => 'required|string',
            'address' => 'required|string',
            'phone' => 'required|string',
        ]);

        // 2. Ambil item berdasarkan category
        switch ($request->category) {
            case 'kambing':
                $item = Kambing::find($request->produk_id);
                break;

            case 'domba':
                $item = Domba::find($request->produk_id);
                break;

            case 'product':
                $item = Product::find($request->produk_id);
                break;

            default:
                $item = null;
        }

        if (!$item) {
            return response()->json(['error' => 'Item tidak ditemukan'], 404);
        }

        // 3. Generate order_id unik
        $orderId = 'ORD-' . time() . '-' . Auth::id();

        // 4. Susun item_details dan transaction_details sesuai Midtrans
        $itemDetails = [
            [
                'id' => $item->id,
                'price' => (int) $item->harga,
                'quantity' => 1,
                'name' => ucfirst($request->category ?? 'Produk') . ' - ' . ($item->name ?? 'Unnamed'),
            ]
        ];

        $transactionDetails = [
            'order_id' => $orderId,
            'gross_amount' => (int) $item->harga,
        ];

        $customerDetails = [
            'first_name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'billing_address' => [
                'first_name' => $request->name,
                'address' => $request->address,
                'city' => $request->city ?? 'Unknown',
                'postal_code' => '',
                'phone' => $request->phone,
                'country_code' => 'IDN',
            ],
            'shipping_address' => [
                'first_name' => $request->name,
                'address' => $request->address,
                'city' => $request->city ?? 'Unknown',
                'postal_code' => '',
                'phone' => $request->phone,
                'country_code' => 'IDN',
            ],
        ];

        $midtransParams = [
            'transaction_details' => $transactionDetails,
            'customer_details' => $customerDetails,
            'item_details' => $itemDetails,
        ];

        try {
            // 5. Dapatkan Snap Token
            $snapToken = Snap::getSnapToken($midtransParams);

            // 6. Simpan ke tabel orders
            $order = Order::create([
                'user_id' => Auth::id(),
                'orderable_id' => $item->id,
                'orderable_type' => get_class($item),
                'order_id' => $orderId,
                'snap_token' => $snapToken,
                'gross_amount' => $item->harga,
                'status' => 'pending',
                'payment_method' => 'midtrans',
                'name' => $request->name,
                'address' => $request->address,
                'phone' => $request->phone,
                'qty' => 1,
            ]);

            ActivityLog::create([
                'actor_id' => Auth::id(),
                'actor_type' => \App\Models\User::class,
                'type' => 'order_create',
                'module' => 'order',
                'description' => 'Membuat order Midtrans. Order ID: ' . $order->order_id .
                    ', Produk ID: ' . $item->id .
                    ', Total: ' . $item->harga,
            ]);



            return response()->json([
                'snap_token' => $snapToken,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function invoice($order_id)
    {
        $order = Order::where('order_id', $order_id)->with(['user', 'orderable'])->firstOrFail();

        // Pastikan hanya user terkait yang bisa akses invoice-nya
        if (auth()->id() !== $order->user_id && !auth()->user()->is_admin) {
            abort(403);
        }

        return view('order.invoice', compact('order'));
    }

    public function midtransWebhook(Request $request)
    {
        $notif = $request->all();

        $orderId = $notif['order_id'] ?? null;
        $transactionStatus = $notif['transaction_status'] ?? null;

        if (!$orderId) {
            return response()->json(['message' => 'Order ID not found'], 400);
        }

        $order = Order::with('orderable')
            ->where('order_id', $orderId)
            ->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $oldStatus = $order->status;

        // Mapping status Midtrans → sistem
        switch ($transactionStatus) {
            case 'capture':
            case 'settlement':
                $newStatus = 'success';
                break;
            case 'pending':
                $newStatus = 'pending';
                break;
            case 'deny':
            case 'expire':
            case 'cancel':
                $newStatus = 'failed';
                break;
            default:
                $newStatus = $transactionStatus;
        }

        if ($oldStatus === $newStatus) {
            return response()->json([
                'message' => 'No status change'
            ], 200);
        }

        ActivityLog::create([
            'actor_id' => $order->user_id,
            'actor_type' => \App\Models\User::class,
            'type' => 'order_update',
            'module' => 'order',
            'description' => 'Webhook update. Order ID: '
                . $order->order_id . ', Status: ' . $order->status,
        ]);

        $item = $order->orderable;

        if (!$item) {
            return response()->json(['message' => 'Item not found'], 404);
        }

        // ==================================
        // STOCK PROTECTION (RETRY SAFE)
        // ==================================

        DB::transaction(function () use ($order, $item, $newStatus, $oldStatus) {

            $order->status = $newStatus;
            $order->save();

            if ($newStatus === 'success' && $oldStatus !== 'success') {

                if ($item instanceof Product) {

                    $remaining = $order->qty;

                    $batches = $item->stocks()
                        ->where('qty', '>', 0)
                        ->orderBy('received_date')
                        ->lockForUpdate()
                        ->get();

                    foreach ($batches as $batch) {

                        if ($remaining <= 0)
                            break;

                        $deduct = min($batch->qty, $remaining);

                        $batch->decrement('qty', $deduct);

                        StockMovement::create([
                            'stockable_id' => $item->id,
                            'stockable_type' => Product::class,
                            'type' => 'out',
                            'quantity' => $deduct,
                            'source' => 'purchase',
                            'reference_id' => $order->id,
                            'movement_date' => now(),
                        ]);

                        $remaining -= $deduct;
                    }

                    if ($remaining > 0) {

                        // 🔥 FALLBACK kalau tidak ada batch (supaya test lama tetap jalan)
                        if ($item->stocks()->count() === 0) {

                            if ($item->stok < $order->qty) {
                                throw new \Exception('Stok tidak cukup');
                            }

                            $item->decrement('stok', $order->qty);

                            StockMovement::create([
                                'stockable_id' => $item->id,
                                'stockable_type' => Product::class,
                                'type' => 'out',
                                'quantity' => $order->qty,
                                'source' => 'purchase',
                                'reference_id' => $order->id,
                                'movement_date' => now(),
                            ]);

                            return;
                        }

                        throw new \Exception('Stok tidak cukup');
                    }

                    // optional: update cached total stok
                    $item->update([
                        'stok' => $item->stocks()->sum('qty')
                    ]);
                }

                if ($item instanceof Kambing || $item instanceof Domba) {
                    $item->update([
                        'for_sale' => 'no',
                        'is_locked' => false
                    ]);
                }
            }

            if ($newStatus === 'failed' && $oldStatus === 'success') {

                if ($item instanceof Product) {

                    ProductStock::create([
                        'product_id' => $item->id,
                        'qty' => $order->qty,
                        'source' => 'manual_adjustment',
                        'reference_id' => $order->id,
                        'received_date' => now(),
                        'price_per_unit' => null,
                    ]);

                    StockMovement::create([
                        'stockable_id' => $item->id,
                        'stockable_type' => get_class($item),
                        'type' => 'in',
                        'quantity' => $order->qty,
                        'source' => 'purchase',
                        'reference_id' => $order->id,
                        'movement_date' => now(),
                    ]);
                }

                if ($item instanceof Kambing || $item instanceof Domba) {
                    $item->update([
                        'for_sale' => 'yes',
                        'is_locked' => false
                    ]);
                }

                // optional: update cached total stok
                $item->update([
                    'stok' => $item->stocks()->sum('qty')
                ]);
            }
        });

        return response()->json([
            'message' => 'Order status updated',
            'order' => $order
        ], 200);
    }

    public function transaksi()
    {
        $orders = Order::where('user_id', auth()->id())
            ->with(['orderable'])
            ->latest()
            ->get();
        return view('order.transaksi', compact('orders'));
    }

    public function manualTransfer(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|integer',
            'category' => 'required|in:kambing,domba,product',
            'email' => 'required|email',
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'sender_name' => 'required|string|max:255',
            'bank_origin' => 'required|string|max:255',
            'transfer_date' => 'required|date',
            'transfer_amount' => 'required|numeric|min:1',
            'transfer_proof' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Cari produk
        switch ($request->category) {
            case 'kambing':
                $produk = Kambing::findOrFail($request->produk_id);
                break;
            case 'domba':
                $produk = Domba::findOrFail($request->produk_id);
                break;
            case 'product':
                $produk = Product::findOrFail($request->produk_id);
                break;
        }

        // Generate order ID
        $orderId = 'ORD-' . time() . '-' . Auth::id();

        // Simpan bukti transfer
        $proofPath = $request->file('transfer_proof')->store('bukti_transfer', 'public');

        try {
            // Buat order
            $order = Order::create([
                'user_id' => Auth::id(),
                'orderable_id' => $produk->id,
                'orderable_type' => get_class($produk),
                'order_id' => $orderId,
                'snap_token' => null,
                'gross_amount' => $request->transfer_amount,
                'status' => 'pending',
                'payment_method' => 'manual',
                'name' => $request->name,
                'address' => $request->address,
                'phone' => $request->phone,
                'qty' => 1,
                'bukti_transfer' => $proofPath,
                'sender_name' => $request->sender_name,
                'bank_origin' => $request->bank_origin,
                'transfer_date' => $request->transfer_date,
            ]);

            ActivityLog::create([
                'actor_id' => Auth::id(),
                'actor_type' => \App\Models\User::class,
                'type' => 'order_create',
                'module' => 'order',
                'description' => 'Membuat order manual. Order ID: ' . $order->order_id .
                    ', Nominal: ' . $request->transfer_amount,
            ]);



            return response()->json([
                'order_id' => $order->order_id,
                'message' => 'Transfer manual berhasil disimpan'
            ]);

        } catch (\Exception $e) {
            // Hapus file jika ada error
            if (Storage::disk('public')->exists($proofPath)) {
                Storage::disk('public')->delete($proofPath);
            }

            return response()->json([
                'error' => 'Gagal menyimpan data transfer: ' . $e->getMessage()
            ], 500);
        }
    }

    public function manualInvoice($order_id)
    {
        $order = Order::where('order_id', $order_id)
            ->where('payment_method', 'manual')
            ->with(['user', 'orderable'])
            ->firstOrFail();

        // Pastikan hanya user terkait yang bisa akses
        if (auth()->id() !== $order->user_id && !auth()->user()->is_admin) {
            abort(403);
        }

        return view('order.manual-invoice', compact('order'));
    }

    public function updateOrderStatus(Request $request, $orderId)
    {
        try {
            $order = Order::with('orderable')->findOrFail($orderId);
            $item = $order->orderable;

            $oldStatus = $order->status;

            $status = $request->input('status');
            $notes = $request->input('notes');

            if (!in_array($status, ['settlement', 'cancel'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Status tidak valid'
                ], 400);
            }

            // Update order dulu
            $order->status = $status;
            $order->admin_notes = $notes;
            $order->save();

            ActivityLog::create([
                'actor_id' => Auth::id(),
                'actor_type' => \App\Models\User::class,
                'type' => 'order_update',
                'module' => 'order',
                'description' => 'Admin update status. Order ID: '
                    . $order->order_id . ', Status: ' . $order->status,
            ]);

            // ===============================
            // HANDLE STOCK LOGIC
            // ===============================

            DB::transaction(function () use ($order, $item, $status, $oldStatus) {

                dd([
                    'oldStatus' => $oldStatus,
                    'newStatus' => $status,
                ]);
                
                if ($status === 'settlement' && $oldStatus !== 'settlement') {

                    if ($item instanceof Product) {

                        // 🔥 Kalau tidak pakai FIFO (tidak ada batch sama sekali)
                        if ($item->stocks()->sum('qty') == 0) {

                            if ($item->stok < $order->qty) {
                                throw new \Exception('Stok tidak cukup');
                            }

                            $item->decrement('stok', $order->qty);

                            StockMovement::create([
                                'stockable_id' => $item->id,
                                'stockable_type' => Product::class,
                                'type' => 'out',
                                'quantity' => $order->qty,
                                'source' => 'purchase',
                                'reference_id' => $order->id,
                                'movement_date' => now(),
                            ]);

                        } else {

                            // 🔥 FIFO version
                            $remaining = $order->qty;

                            $batches = $item->stocks()
                                ->where('qty', '>', 0)
                                ->orderBy('received_date')
                                ->lockForUpdate()
                                ->get();

                            foreach ($batches as $batch) {

                                if ($remaining <= 0)
                                    break;

                                $deduct = min($batch->qty, $remaining);

                                $batch->decrement('qty', $deduct);

                                StockMovement::create([
                                    'stockable_id' => $item->id,
                                    'stockable_type' => Product::class,
                                    'type' => 'out',
                                    'quantity' => $deduct,
                                    'source' => 'purchase',
                                    'reference_id' => $order->id,
                                    'movement_date' => now(),
                                ]);

                                $remaining -= $deduct;
                            }

                            if ($remaining > 0) {
                                throw new \Exception('Stok tidak cukup');
                            }

                            $item->update([
                                'stok' => $item->stocks()->sum('qty')
                            ]);
                        }
                    }

                    if ($item instanceof Kambing || $item instanceof Domba) {
                        $item->update([
                            'for_sale' => 'no',
                            'is_locked' => false
                        ]);
                    }
                }

                if ($status === 'cancel' && $oldStatus === 'settlement') {

                    if ($item instanceof Product) {

                        ProductStock::create([
                            'product_id' => $item->id,
                            'qty' => $order->qty,
                            'source' => 'manual_adjustment',
                            'reference_id' => $order->id,
                            'received_date' => now(),
                            'price_per_unit' => null,
                        ]);

                        $item->update([
                            'stok' => $item->stocks()->sum('qty')
                        ]);

                        StockMovement::create([
                            'stockable_id' => $item->id,
                            'stockable_type' => get_class($item),
                            'type' => 'in',
                            'quantity' => $order->qty,
                            'source' => 'purchase',
                            'reference_id' => $order->id,
                            'movement_date' => now(),
                        ]);
                    }

                    if ($item instanceof Kambing || $item instanceof Domba) {
                        $item->update([
                            'for_sale' => 'yes',
                            'is_locked' => false
                        ]);
                    }
                }
            });

            return response()->json(['success' => true]);

        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function updateOrderNotes(Request $request, $orderId)
    {
        try {
            $order = Order::findOrFail($orderId);

            $request->validate([
                'notes' => 'required|string'
            ]);

            $order->admin_notes = $request->notes;
            $order->save();

            ActivityLog::create([
                'actor_id' => Auth::id(),
                'actor_type' => \App\Models\User::class,
                'type' => 'order_update',
                'module' => 'order',
                'description' => 'Webhook update status. Order ID: ' . $order->order_id .
                    ', Status: ' . $order->status,
            ]);



            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}