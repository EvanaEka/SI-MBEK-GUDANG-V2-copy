<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Production;
use App\Models\QcIndicator;
use Illuminate\Foundation\Testing\RefreshDatabase;


class QcProductionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function qc_layak_jika_semua_indikator_critical_lulus()
    {
        $admin = Admin::factory()->create([
            'must_change_password' => false,
        ]);

        $this->actingAs($admin, 'admin');

        $production = Production::factory()->create([
            'status' => 'diproses',
            'qc_status' => 'pending',
        ]);

        $indicatorCritical = QcIndicator::factory()->create([
            'is_active' => true,
            'is_critical' => true,
        ]);

        $indicatorNonCritical = QcIndicator::factory()->create([
            'is_active' => true,
            'is_critical' => false,
        ]);

        $response = $this->post(route('admin.qc.store', $production), [
            'indicators' => [
                $indicatorCritical->id => 'lulus',
                $indicatorNonCritical->id => 'lulus',
            ],
            'threshold' => 80, // ✅ tambahkan ini
            'catatan' => 'QC aman',
        ]);


        $response->assertStatus(302);

        // ✅ Status akhir ada di productions
        $this->assertDatabaseHas('productions', [
            'id' => $production->id,
            'qc_status' => 'layak',
        ]);

        // ✅ Log QC tersimpan
        $this->assertDatabaseHas('production_qcs', [
            'production_id' => $production->id,
            'status' => 'layak',
        ]);
    }

    /** @test */
    public function qc_tidak_layak_jika_indikator_critical_gagal()
    {
        $admin = Admin::factory()->create([
            'must_change_password' => false,
        ]);

        $this->actingAs($admin, 'admin');

        $production = Production::factory()->create([
            'status' => 'diproses',
            'qc_status' => 'pending',
        ]);

        $indicatorCritical = QcIndicator::factory()->create([
            'is_active' => true,
            'is_critical' => true,
        ]);

        $response = $this->post(route('admin.qc.store', $production), [
            'indicators' => [
                $indicatorCritical->id => 'gagal',
            ],
            'threshold' => 80, // ✅ tambahkan ini
            'catatan' => 'Produk cacat',
        ]);


        $response->assertStatus(302);

        // ✅ productions terupdate
        $this->assertDatabaseHas('productions', [
            'id' => $production->id,
            'qc_status' => 'tidak_layak',
        ]);

        // ✅ log tersimpan
        $this->assertDatabaseHas('production_qcs', [
            'production_id' => $production->id,
            'status' => 'tidak_layak',
        ]);
    }
}
