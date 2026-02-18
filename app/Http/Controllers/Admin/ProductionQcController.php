<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Production;
use App\Models\ProductionQc;
use App\Models\QcIndicator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductionQcController extends Controller
{
    /**
     * Simpan hasil QC produksi
     */
    public function store(Request $request, Production $production)
    {
        $validated = $request->validate([
            'indicators' => 'required|array',
            'threshold' => 'required|integer|min:70|max:90',
            'catatan' => 'nullable|string',
        ]);

        $threshold = (int) $validated['threshold'];

        // Ambil indikator aktif saja
        $indicators = QcIndicator::active()->get();

        DB::beginTransaction();

        try {

            $failedCritical = false;
            $totalNonCritical = 0;
            $passedNonCritical = 0;

            foreach ($indicators as $indicator) {

                // Default jika tidak dikirim dianggap gagal
                $isPassed = ($validated['indicators'][$indicator->id] ?? 'gagal') === 'lulus';

                // 🔴 Jika indikator kritis gagal → langsung tidak layak
                if ($indicator->is_critical && !$isPassed) {
                    $failedCritical = true;
                    break;
                }

                // 🟡 Hitung non-kritis
                if (!$indicator->is_critical) {
                    $totalNonCritical++;

                    if ($isPassed) {
                        $passedNonCritical++;
                    }
                }
            }

            // Tentukan status akhir
            if ($failedCritical) {
                $percentage = 0;
                $status = 'tidak_layak';
            } else {
                $percentage = $totalNonCritical > 0
                    ? ($passedNonCritical / $totalNonCritical) * 100
                    : 100;

                $status = $percentage >= $threshold
                    ? 'layak'
                    : 'tidak_layak';
            }

            $percentage = round($percentage, 2);

            // ✅ Simpan log QC (tambahkan score_non_kritis agar tidak error DB)
            ProductionQc::create([
                'production_id' => $production->id,
                'created_by' => auth('admin')->id(),
                'status' => $status,
                'percentage' => $percentage,
                'score_non_kritis' => $percentage, // WAJIB isi untuk DB
                'threshold' => $threshold,
                'catatan' => $validated['catatan'] ?? null,
            ]);

            // ✅ Update ringkasan di tabel productions
            $production->update([
                'qc_status' => $status,
                'qc_percentage' => $percentage,
                'qc_threshold' => $threshold,
            ]);

            DB::commit();

            return back()->with(
                $status === 'layak' ? 'success' : 'warning',
                "QC selesai. Status: " . strtoupper($status)
            );

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
