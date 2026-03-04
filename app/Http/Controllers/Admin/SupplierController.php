<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * 📋 List supplier
     */
    public function index()
    {
        $suppliers = Supplier::orderBy('nama_supplier')->get();
        return view('admin.suppliers.index', compact('suppliers'));
    }

    /**
     * ➕ Form tambah supplier
     */
    public function create()
    {
        return view('admin.suppliers.create');
    }

    /**
     * 💾 Simpan supplier baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_supplier' => 'required|string|max:255',
            'kontak'        => 'nullable|string|max:100',
            'alamat'        => 'nullable|string',
            'kota'          => 'nullable|string|max:100',
            'provinsi'      => 'nullable|string|max:100',
            'catatan'       => 'nullable|string',
        ]);

        Supplier::create($request->all());

        return redirect()
            ->route('admin.suppliers.index')
            ->with('success', 'Supplier berhasil ditambahkan');
    }

    /**
     * ✏ Form edit
     */
    public function edit(Supplier $supplier)
    {
        return view('admin.suppliers.edit', compact('supplier'));
    }

    /**
     * 🔄 Update supplier
     */
    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'nama_supplier' => 'required|string|max:255',
            'kontak'        => 'nullable|string|max:100',
            'alamat'        => 'nullable|string',
            'kota'          => 'nullable|string|max:100',
            'provinsi'      => 'nullable|string|max:100',
            'catatan'       => 'nullable|string',
        ]);

        $supplier->update($request->all());

        return redirect()
            ->route('admin.suppliers.index')
            ->with('success', 'Supplier berhasil diperbarui');
    }

    /**
     * ❌ Hapus supplier
     */
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()
            ->route('admin.suppliers.index')
            ->with('success', 'Supplier berhasil dihapus');
    }
}