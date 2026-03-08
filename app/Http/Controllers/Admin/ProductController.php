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

        return view('admin.product.index', compact('products'));
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
           return redirect()->back()->withInput()->with('error', 'Produk produksi wajib memiliki formula');
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

       return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan');
    }

    /**
     * 🔍 Detail produk
     */
    public function show(Product $product)
    {
        $product->load('formula');
        $formulas = \App\Models\Formula::all(); // Dibutuhkan untuk dropdown saat mode edit di halaman show
        return view('admin.product.show', compact('product', 'formulas'));
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
           return redirect()->back()->with('error', 'Produk produksi wajib memiliki formula');
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

       return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui');
    }

    /**
     * ❌ Hapus produk
     */
    public function destroy(Product $product)
    {
        if ($product->stok > 0) {
           return redirect()->back()->with('error', 'Produk tidak bisa dihapus karena masih memiliki stok');
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus');
    }
  public function create()
{
    // Mengambil data resep dari database
   $formulas = Formula::orderBy('nama_formula')->get();
    
    // Mengirim ke view agar dropdown ada isinya
    return view('admin.product.create', compact('formulas'));
}

}