<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Formula;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
     * ➕ Form tambah produk baru
     */
    public function create()
    {
        // Mengambil data resep dari database
        $formulas = Formula::orderBy('nama_formula')->get();
        
        // Mengirim ke view agar dropdown ada isinya
        return view('admin.product.create', compact('formulas'));
    }

    /**
     * 💾 Simpan produk baru
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Validasi gambar
        ]);

        if ($request->source === 'produksi' && !$request->formula_id) {
           return redirect()->back()->withInput()->with('error', 'Produk produksi wajib memiliki formula');
        }

        // Handle upload image
        $imagePath = null;
if ($request->hasFile('image')) {
    $file = $request->file('image');
    $fileName = time() . '_' . $file->getClientOriginalName();
    // Pindahkan langsung ke public/uploads/products
    $file->move(public_path('uploads/products'), $fileName);
    $imagePath = 'uploads/products/' . $fileName;
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
            'image' => $imagePath, // Simpan path gambar
            'created_by' => auth('admin')->id(),
        ]);

       return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan');
    }

    /**
     * 🔍 Detail produk & Form Edit
     */
    public function show(Product $product)
    {
        $product->load('formula');
        $formulas = Formula::all();
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Validasi gambar
        ]);

        if ($request->source === 'produksi' && !$request->formula_id) {
           return redirect()->back()->with('error', 'Produk produksi wajib memiliki formula');
        }

        // Handle update image
        $dataToUpdate = [
            'kode' => $request->kode,
            'nama' => $request->nama,
            'harga' => $request->harga,
            'rop' => $request->rop ?? 0,
            'formula_id' => $request->formula_id,
            'type' => $request->type,
            'source' => $request->source,
        ];

        if ($request->hasFile('image')) {
    // Hapus foto lama jika ada
    if ($product->image && file_exists(public_path($product->image))) {
        unlink(public_path($product->image));
    }
    
    $file = $request->file('image');
    $fileName = time() . '_' . $file->getClientOriginalName();
    $file->move(public_path('uploads/products'), $fileName);
    $dataToUpdate['image'] = 'uploads/products/' . $fileName;
}

        $product->update($dataToUpdate);

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

        // Hapus file gambar kalau ada
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus');
    }
}