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

        return response()->json($materials);
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

        return response()->json([
            'success' => true,
            'message' => 'Material berhasil ditambahkan',
            'data' => $material
        ]);
    }

    /**
     * 🔍 Detail material
     */
    public function show(Material $material)
    {
        return response()->json($material);
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

        return response()->json([
            'success' => true,
            'message' => 'Material berhasil diperbarui',
            'data' => $material
        ]);
    }

    /**
     * ❌ Hapus material
     */
    public function destroy(Material $material)
    {
        // Cegah hapus jika masih ada stok
        if ($material->stok > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Material tidak bisa dihapus karena masih memiliki stok'
            ], 422);
        }

        $material->delete();

        return response()->json([
            'success' => true,
            'message' => 'Material berhasil dihapus'
        ]);
    }
}