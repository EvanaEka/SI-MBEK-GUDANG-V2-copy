<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Models\Kambing;
use App\Models\Domba;
use App\Models\Order;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $kambings = Kambing::where('for_Sale', 'yes')->get();
        return view('order', [
            'kambings' => $kambings,
        ]);
    }

    /**
     * Display the specified resource (halaman detail order berdasarkan category dan id).
     */
    public function show($category, $id)
    {
        if ($category === 'kambing') {
            $produk = Kambing::findOrFail($id);
        } else {
            $produk = Domba::findOrFail($id);
        }

        return view('order', compact('produk', 'category'));
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

        // 2. Ambil objek produk: bisa Kambing atau Domba
        $produk = null;
        if ($request->category === 'kambing') {
            $produk = Kambing::find($request->produk_id);
        } else {
            $produk = Domba::find($request->produk_id);
        }

        if (!$produk) {
            return response()->json([
                'error' => 'Produk tidak ditemukan'
            ], 404);
        }

        // 3. Generate order_id unik
        $orderId = 'ORD-' . time() . '-' . Auth::id();

        // 4. Susun item_details dan transaction_details sesuai Midtrans
        $itemDetails = [
            [
                'id' => $produk->id,
                'price' => (int) $produk->harga,
                'quantity' => 1,
                'name' => ucfirst($request->category ?? 'Produk') . ' - ' . ($produk->name ?? 'Unnamed'),
            ]
        ];

        $transactionDetails = [
            'order_id' => $orderId,
            'gross_amount' => (int) $produk->harga,
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
                'produk_id' => $produk->id,
                'order_id' => $orderId,
                'snap_token' => $snapToken,
                'gross_amount' => $produk->harga,
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
                    ', Produk ID: ' . $produk->id .
                    ', Total: ' . $produk->harga,
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
        $order = Order::where('order_id', $order_id)->with(['user', 'kambing', 'domba'])->firstOrFail();

        // Pastikan hanya user terkait yang bisa akses invoice-nya
        if (auth()->id() !== $order->user_id && !auth()->user()->is_superadmin) {
            abort(403);
        }

        return view('order.invoice', compact('order'));
    }

    public function midtransWebhook(Request $request)
    {
        $notif = $request->all();

        // Ambil order_id dari notifikasi
        $orderId = $notif['order_id'] ?? null;
        $transactionStatus = $notif['transaction_status'] ?? null;

        if (!$orderId) {
            return response()->json(['message' => 'Order ID not found'], 400);
        }

        // Cari order di database
        $order = Order::where('order_id', $orderId)->first();
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Update status order sesuai status dari Midtrans
        switch ($transactionStatus) {
            case 'capture':
            case 'settlement':
                $order->status = 'success';
                break;
            case 'pending':
                $order->status = 'pending';
                break;
            case 'deny':
            case 'expire':
            case 'cancel':
                $order->status = 'failed';
                break;
            default:
                $order->status = $transactionStatus;
        }

        $order->save();

        ActivityLog::create([
            'actor_id' => $order->user_id, // atau null kalau mau tandai system
            'actor_type' => \App\Models\User::class,
            'type' => 'order_update',
            'module' => 'order',
            'description' => 'Webhook update status. Order ID: ' . $order->order_id .
                ', Status: ' . $order->status,
        ]);



        // Ambil produk terkait
        $produk = Kambing::find($order->produk_id) ?: Domba::find($order->produk_id);

        if ($produk) {
            if (in_array($transactionStatus, ['settlement', 'capture'])) {
                // Pembayaran sukses: produk hilang dari etalase
                $produk->update(['for_sale' => 'no', 'is_locked' => false]);
            } elseif (in_array($transactionStatus, ['expire', 'cancel', 'deny', 'failure'])) {
                // Pembayaran gagal: produk muncul lagi di etalase
                $produk->update(['is_locked' => false, 'for_sale' => 'yes']);
            }
        }

        return response()->json(['message' => 'Order status updated', 'order' => $order], 200);
    }

    public function transaksi()
    {
        $orders = Order::where('user_id', auth()->id())
            ->with(['kambing', 'domba'])
            ->latest()
            ->get();
        return view('order.transaksi', compact('orders'));
    }

    public function manualTransfer(Request $request)
    {
        $validated = $request->validate([
            'produk_id' => 'required|integer',
            'category' => 'required|in:kambing,domba',
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
        $produk = ($request->category === 'kambing')
            ? Kambing::findOrFail($request->produk_id)
            : Domba::findOrFail($request->produk_id);

        // Generate order ID
        $orderId = 'ORD-' . time() . '-' . Auth::id();

        // Simpan bukti transfer
        $proofPath = $request->file('transfer_proof')->store('bukti_transfer', 'public');

        try {
            // Buat order
            $order = Order::create([
                'user_id' => Auth::id(),
                'produk_id' => $produk->id,
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
            ->with(['user', 'kambing', 'domba'])
            ->firstOrFail();

        // Pastikan hanya user terkait yang bisa akses
        if (auth()->id() !== $order->user_id && !auth()->user()->is_superadmin) {
            abort(403);
        }

        return view('order.manual-invoice', compact('order'));
    }

    public function updateOrderStatus(Request $request, $orderId)
    {
        try {
            $order = Order::with('kambing', 'domba')->findOrFail($orderId);

            $status = $request->input('status');
            $notes = $request->input('notes');

            if (!in_array($status, ['settlement', 'cancel'])) {
                return response()->json(['success' => false, 'message' => 'Status tidak valid'], 400);
            }

            // Update order
            $order->status = $status;
            $order->admin_notes = $notes; // Simpan notes
            $order->save();

            ActivityLog::create([
                'actor_id' => Auth::id(),
                'actor_type' => \App\Models\User::class,
                'type' => 'order_update',
                'module' => 'order',
                'description' => 'Webhook update status. Order ID: ' . $order->order_id .
                    ', Status: ' . $order->status,
            ]);




            // Update produk status
            if ($status === 'settlement') {
                if ($order->kambing) {
                    $order->kambing->update(['for_sale' => 'no', 'is_locked' => false]);
                }
                if ($order->domba) {
                    $order->domba->update(['for_sale' => 'no', 'is_locked' => false]);
                }
            } elseif ($status === 'cancel') {
                if ($order->kambing) {
                    $order->kambing->update(['for_sale' => 'yes', 'is_locked' => false]);
                }
                if ($order->domba) {
                    $order->domba->update(['for_sale' => 'yes', 'is_locked' => false]);
                }
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
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