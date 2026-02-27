<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Formula;
use App\Models\Material;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FormulaController extends Controller
{
    /**
     * 1️⃣ List Formula
     */
    public function index()
    {
        $formulas = Formula::with('materials')
            ->latest()
            ->get();

        return view('admin.formula.index', compact('formulas'));
    }

    /**
     * 2️⃣ Form Create
     */
    public function create()
    {
        $materials = Material::where('is_active', true)->get();

        return view('admin.formula.create', compact('materials'));
    }

    /**
     * 3️⃣ Store Formula
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_formula' => 'required|unique:formulas,kode_formula',
            'nama_formula' => 'required|string|max:255',
            'materials' => 'required|array|min:1',
            'materials.*.material_id' => 'required|exists:materials,id',
            'materials.*.persentase' => 'required|numeric|min:0.01|max:100',
        ]);

        // 🔥 Validasi total 100%
        $totalPersentase = collect($request->materials)
            ->sum('persentase');

        if ($totalPersentase != 100) {
            return back()->withInput()->withErrors([
                'persentase' => 'Total persentase bahan harus 100%',
            ]);
        }

        DB::beginTransaction();

        try {

            $formula = Formula::create([
                'kode_formula' => $request->kode_formula,
                'nama_formula' => $request->nama_formula,
                'deskripsi' => $request->deskripsi,
                'created_by' => auth('admin')->id(),
                'is_active' => true,
            ]);

            // Attach materials
            foreach ($request->materials as $item) {
                $formula->materials()->attach(
                    $item['material_id'],
                    ['persentase' => $item['persentase']]
                );
            }

            $this->logActivity('formula_created', $formula->id);

            DB::commit();

            return redirect()->route('admin.formula.index')
                ->with('success', 'Formula berhasil dibuat');

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 4️⃣ Edit
     */
    public function edit(Formula $formula)
    {
        $formula->load('materials');
        $materials = Material::where('is_active', true)->get();

        return view('admin.formula.edit', compact('formula', 'materials'));
    }

    /**
     * 5️⃣ Update
     */
    public function update(Request $request, Formula $formula)
    {
        $request->validate([
            'nama_formula' => 'required|string|max:255',
            'materials' => 'required|array|min:1',
            'materials.*.material_id' => 'required|exists:materials,id',
            'materials.*.persentase' => 'required|numeric|min:0.01|max:100',
        ]);

        $totalPersentase = collect($request->materials)
            ->sum('persentase');

        if ($totalPersentase != 100) {
            return back()->withInput()->withErrors([
                'persentase' => 'Total persentase bahan harus 100%',
            ]);
        }

        DB::transaction(function () use ($request, $formula) {

            $formula->update([
                'nama_formula' => $request->nama_formula,
                'deskripsi' => $request->deskripsi,
            ]);

            // Sync ulang pivot
            $syncData = [];

            foreach ($request->materials as $item) {
                $syncData[$item['material_id']] = [
                    'persentase' => $item['persentase']
                ];
            }

            $formula->materials()->sync($syncData);

            $this->logActivity('formula_updated', $formula->id);
        });

        return redirect()->route('admin.formula.index')
            ->with('success', 'Formula berhasil diperbarui');
    }

    /**
     * 6️⃣ Nonaktifkan Formula (Soft Disable)
     */
    public function destroy(Formula $formula)
    {
        // Cegah hapus kalau sudah pernah dipakai produksi
        if ($formula->productions()->exists()) {
            return back()->withErrors([
                'formula' => 'Formula sudah pernah digunakan dalam produksi'
            ]);
        }

        $formula->update([
            'is_active' => false
        ]);

        $this->logActivity('formula_deactivated', $formula->id);

        return back()->with('success', 'Formula berhasil dinonaktifkan');
    }

    /**
     * 🔐 Activity Log Helper
     */
    private function logActivity($type, $formulaId)
    {
        $actor = $this->getCurrentActor();

        if ($actor) {
            ActivityLog::create([
                'actor_id' => $actor->id,
                'actor_type' => get_class($actor),
                'type' => $type,
                'module' => 'formula',
                'description' => 'Formula #' . $formulaId
            ]);
        }
    }
}