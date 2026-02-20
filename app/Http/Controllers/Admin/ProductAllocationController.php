<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductAllocation;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductAllocationController extends Controller
{
    /**
     * 1️⃣ Simpan / update alokasi stok
     */
    public function storeOrUpdate(Request $request, Product $product)
    {
        $validated = $request->validate([
            'type' => 'required|in:jual,internal',
            'qty' => 'required|integer|min:0',
        ]);

        try {
            // Ambil alokasi lain (selain type yang sedang diubah)
            $allocatedOther = $product->allocations()
                ->where('type', '!=', $request->type)
                ->sum('qty');

            $totalAfter = $allocatedOther + $request->qty;

            // ❗ VALIDASI TOTAL ALOKASI
            if ($totalAfter > $product->stok) {
                return back()->withErrors([
                    'qty' => 'Total alokasi melebihi stok produk',
                ]);
            }

            ProductAllocation::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'type' => $request->type,
                ],
                [
                    'qty' => $request->qty,
                    'created_by' => auth('admin')->id(),
                ]
            );

            $allocation = ProductAllocation::create($validated);

            $actor = $this->getCurrentActor();
            if ($actor) {
                ActivityLog::create([
                    'actor_id' => $actor->id,
                    'actor_type' => get_class($actor),
                    'type' => 'allocation_created',
                    'module' => 'product_allocation',
                    'description' => 'Membuat alokasi produk #' . $allocation->id
                ]);
            }


            DB::commit();

            return back()->with('success', 'Alokasi stok berhasil disimpan');

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 2️⃣ Pemakaian internal
     * - kurangi stok produk
     * - kurangi alokasi internal
     */
    public function useInternal(Request $request, Product $product)
    {
        $request->validate([
            'qty' => 'required|integer|min:1',
        ]);

        try {
            DB::transaction(function () use ($product, $request) {

                $allocation = $product->allocations()
                    ->where('type', 'internal')
                    ->lockForUpdate()
                    ->first();

                if (!$allocation) {
                    throw new \Exception('Alokasi internal belum tersedia');
                }

                if ($request->qty > $allocation->qty) {
                    throw new \Exception('Qty melebihi alokasi internal');
                }

                if ($request->qty > $product->stok) {
                    throw new \Exception('Stok produk tidak mencukupi');
                }

                $product->decrement('stok', $request->qty);
                $allocation->decrement('qty', $request->qty);
            });

            // 🔔 ROP CHECK
            $product->refresh();

            if ($product->isBelowRop()) {
                return back()->with([
                    'success' => 'Pemakaian internal berhasil',
                    'warning' => '⚠️ Stok produk sudah mencapai batas minimum (ROP)',
                ]);
            }

            return back()->with('success', 'Pemakaian internal berhasil dicatat');

        } catch (\Throwable $e) {
            return back()->withErrors([
                'qty' => $e->getMessage(),
            ]);
        }
    }


    /**
     * 3️⃣ Penjualan produk
     * - kurangi stok produk
     * - kurangi alokasi jual
     */
    public function sell(Request $request, Product $product)
    {
        $request->validate([
            'qty' => 'required|integer|min:1',
        ]);

        try {
            DB::transaction(function () use ($product, $request) {

                $allocation = $product->allocations()
                    ->where('type', 'jual')
                    ->lockForUpdate()
                    ->first();

                if (!$allocation) {
                    throw new \Exception('Alokasi jual belum tersedia');
                }

                if ($request->qty > $allocation->qty) {
                    throw new \Exception('Qty melebihi alokasi jual');
                }

                if ($request->qty > $product->stok) {
                    throw new \Exception('Stok produk tidak mencukupi');
                }

                $product->decrement('stok', $request->qty);
                $allocation->decrement('qty', $request->qty);
            });

            // 🔔 ROP CHECK
            $product->refresh();

            if ($product->isBelowRop()) {
                return back()->with([
                    'success' => 'Penjualan berhasil',
                    'warning' => '⚠️ Stok produk sudah mencapai batas minimum (ROP)',
                ]);
            }

            return back()->with('success', 'Penjualan berhasil dicatat');

        } catch (\Throwable $e) {
            return back()->withErrors([
                'qty' => $e->getMessage(),
            ]);
        }
    }
}