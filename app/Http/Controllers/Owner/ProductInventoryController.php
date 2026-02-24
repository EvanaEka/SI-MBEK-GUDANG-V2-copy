<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductInventoryController extends Controller
{
    /**
     * 📦 List semua produk + stok total
     */
    public function index()
    {
        $products = Product::withSum('stocks', 'qty')
            ->orderBy('name')
            ->get();

        return view('inventory.product.index', compact('products'));
    }

    /**
     * 📋 Detail batch per produk
     */
    public function show(Product $product)
    {
        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductStock> $batches */
        $batches = $product->stocks()
            ->where('qty', '>', 0)
            ->orderBy('received_date', 'asc')
            ->get();

        return view('inventory.product.show', compact('product', 'batches'));
    }

    /**
     * 🔄 Sync summary stok dengan total batch
     */
    public function sync(Product $product)
    {
        DB::transaction(function () use ($product) {

            $realStock = $product->stocks()->sum('qty');

            $product->update([
                'stok' => $realStock
            ]);
        });

        return back()->with('success', 'Stok berhasil disinkronkan');
    }


    /**
     * Adjust Manual
     */
    public function adjust(Request $request, Product $product)
    {
        $request->validate([
            'qty' => 'required|integer|min:1',
            'type' => 'required|in:in,out',
            'reason' => 'nullable|string'
        ]);

        DB::transaction(function () use ($request, $product) {

            if ($request->type === 'in') {

                // Tambah batch baru (manual adjustment)
                ProductStock::create([
                    'product_id' => $product->id,
                    'qty' => $request->qty,
                    'source' => 'manual_adjustment',
                    'reference_id' => null,
                    'received_date' => now(),
                    'expired_date' => null,
                ]);

                $product->increment('stok', $request->qty);
            }

            if ($request->type === 'out') {

                $remainingQty = $request->qty;

                /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductStock> $batches */
                $batches = ProductStock::where('product_id', $product->id)
                    ->where('qty', '>', 0)
                    ->orderBy('received_date', 'asc')
                    ->lockForUpdate()
                    ->get();

                foreach ($batches as $batch) {

                    if ($remainingQty <= 0)
                        break;

                    if ($batch->qty >= $remainingQty) {
                        $batch->decrement('qty', $remainingQty);
                        $remainingQty = 0;
                    } else {
                        $remainingQty -= $batch->qty;
                        $batch->update(['qty' => 0]);
                    }
                }

                if ($remainingQty > 0) {
                    throw new \Exception('Stok batch tidak mencukupi untuk adjustment');
                }

                $product->decrement('stok', $request->qty);
            }

            StockMovement::create([
                'stockable_id' => $product->id,
                'stockable_type' => Product::class,
                'type' => $request->type,
                'quantity' => $request->qty,
                'source' => 'manual_adjustment',
                'reference_id' => null,
                'movement_date' => now(),
            ]);
        });

        return back()->with('success', 'Stok berhasil disesuaikan');
    }
}