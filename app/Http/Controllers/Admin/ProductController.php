<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Formula;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * 📋 List semua produk
     */
    public function index()
    {
        $products = Product::with('formula')
            ->orderBy('nama')
            ->get();

        return response()->json($products);
    }

    /**
     * ➕ Tambah produk baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|max:50|unique:products,kode',
            'nama' => 'required|string|max:255',
            'harga' => 'nullable|numeric|min:0',
            'rop' => 'nullable|integer|min:0',
            'formula_id' => 'nullable|exists:formulas,id',
            'type' => 'required|in:pakan,obat',
            'source' => 'required|in:produksi,pembelian',
        ]);

        // Jika source produksi → wajib ada formula
        if ($request->source === 'produksi' && !$request->formula_id) {
            return response()->json([
                'success' => false,
                'message' => 'Produk dengan source produksi wajib memiliki formula'
            ], 422);
        }

        $product = Product::create([
            'kode' => $request->kode,
            'nama' => $request->nama,
            'harga' => $request->harga,
            'stok' => 0,
            'rop' => $request->rop ?? 0,
            'formula_id' => $request->formula_id,
            'type' => $request->type,
            'source' => $request->source,
            'created_by' => auth('admin')->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan',
            'data' => $product
        ]);
    }

    /**
     * 🔍 Detail produk
     */
    public function show(Product $product)
    {
        $product->load('formula');

        return response()->json($product);
    }

    /**
     * ✏ Update produk
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'kode' => 'required|string|max:50|unique:products,kode,' . $product->id,
            'nama' => 'required|string|max:255',
            'harga' => 'nullable|numeric|min:0',
            'rop' => 'nullable|integer|min:0',
            'formula_id' => 'nullable|exists:formulas,id',
            'type' => 'required|in:pakan,obat',
            'source' => 'required|in:produksi,pembelian',
        ]);

        if ($request->source === 'produksi' && !$request->formula_id) {
            return response()->json([
                'success' => false,
                'message' => 'Produk produksi wajib memiliki formula'
            ], 422);
        }

        $product->update([
            'kode' => $request->kode,
            'nama' => $request->nama,
            'harga' => $request->harga,
            'rop' => $request->rop ?? 0,
            'formula_id' => $request->formula_id,
            'type' => $request->type,
            'source' => $request->source,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil diperbarui',
            'data' => $product
        ]);
    }

    /**
     * ❌ Hapus produk
     */
    public function destroy(Product $product)
    {
        if ($product->stok > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak bisa dihapus karena masih memiliki stok'
            ], 422);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus'
        ]);
    }
}