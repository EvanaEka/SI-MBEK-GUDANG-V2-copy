<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaterialController extends Controller
{
    /**
     * 📋 List semua material (master data)
     */
    public function index()
    {
        $materials = Material::orderBy('nama_bahan')->get();

        return view('admin.material.index', compact('materials'));
    }

    /**
     * ➕ Tambah material baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_bahan' => 'required|string|max:255|unique:materials,nama_bahan',
            'kategori' => 'required|in:pakan,obat',
            'satuan' => 'required|string|max:50',
            'pemakaian_rata_rata' => 'nullable|numeric|min:0',
            'lead_time' => 'nullable|integer|min:0',
            'safety_stock' => 'nullable|integer|min:0',
            'deskripsi' => 'nullable|string',
        ]);

        $material = Material::create([
            'nama_bahan' => $request->nama_bahan,
            'kategori' => $request->kategori,
            'satuan' => $request->satuan,
            'stok' => 0, // default awal
            'pemakaian_rata_rata' => $request->pemakaian_rata_rata ?? 0,
            'lead_time' => $request->lead_time ?? 0,
            'safety_stock' => $request->safety_stock ?? 5,
            'deskripsi' => $request->deskripsi,
        ]);

        return view('admin.material.create');
       
    }

    /**
     * 🔍 Detail material
     */
    public function show(Material $material)
    {
        return view('admin.material.show', compact('material'));
    }

    /**
     * ✏ Update material
     */
    public function update(Request $request, Material $material)
    {
        $request->validate([
            'nama_bahan' => 'required|string|max:255|unique:materials,nama_bahan,' . $material->id,
            'kategori' => 'required|in:pakan,obat',
            'satuan' => 'required|string|max:50',
            'pemakaian_rata_rata' => 'nullable|numeric|min:0',
            'lead_time' => 'nullable|integer|min:0',
            'safety_stock' => 'nullable|integer|min:0',
            'deskripsi' => 'nullable|string',
        ]);

        $material->update([
            'nama_bahan' => $request->nama_bahan,
            'kategori' => $request->kategori,
            'satuan' => $request->satuan,
            'pemakaian_rata_rata' => $request->pemakaian_rata_rata ?? 0,
            'lead_time' => $request->lead_time ?? 0,
            'safety_stock' => $request->safety_stock ?? 5,
            'deskripsi' => $request->deskripsi,
        ]);

        if ($request->source === 'inventory') {
    return back()->with('success', 'Parameter ROP berhasil diperbarui!');
}
        return redirect()->route('admin.materials.index')
            ->with('success', 'Material berhasil diperbarui');
    }

    /**
     * ❌ Hapus material
     */
    public function destroy(Material $material)
    {
        // Cegah hapus jika masih ada stok
        if ($material->stok > 0) {
           return redirect()->back()->with('error', 'Material tidak bisa dihapus karena masih memiliki stok');
        }

        $material->delete();

       return redirect()->route('admin.materials.index')
            ->with('success', 'Material berhasil dihapus');
    }
    public function create()
{
    return view('admin.material.create');
}

}