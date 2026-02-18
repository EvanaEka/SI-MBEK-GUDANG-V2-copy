<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Formula;
use App\Models\Product;
use App\Models\Material;
use App\Models\MaterialStock;
use App\Models\Production;
use App\Models\QcIndicator;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductionFlowTest extends TestCase
{
    use RefreshDatabase;
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::factory()->create();
        $this->actingAs($this->admin, 'admin');
    }

    /** @test */
    public function production_store_success_and_reduce_material_stock()
    {
        $material = Material::factory()->create(['stok' => 100]);

        $formula = Formula::factory()->create();
        $formula->materials()->attach($material->id, [
            'persentase' => 50,
        ]);

        $product = Product::factory()->create([
            'formula_id' => $formula->id,
        ]);

        MaterialStock::create([
            'material_id' => $material->id,
            'qty' => 100,
            'received_date' => now(),
            'expired_date' => null,
            'created_by' => $this->admin->id,
        ]);

        $response = $this->post(route('admin.productions.store'), [
            'formula_id' => $formula->id,
            'product_id' => $product->id,
            'qty_produksi' => 10,
        ]);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('productions', [
            'status' => 'diproses',
        ]);

        $this->assertEquals(95, $material->fresh()->stok);
    }

    /** @test */
    public function production_store_fail_if_material_not_enough()
    {
        $material = Material::factory()->create(['stok' => 5]);

        $formula = Formula::factory()->create();
        $formula->materials()->attach($material->id, [
            'persentase' => 100,
        ]);

        $product = Product::factory()->create([
            'formula_id' => $formula->id,
        ]);

        $response = $this->post(route('admin.productions.store'), [
            'formula_id' => $formula->id,
            'product_id' => $product->id,
            'qty_produksi' => 10,
        ]);

        $response->assertSessionHasErrors();
    }

    /** @test */
    public function qc_fail_auto_create_disposal_and_reject()
    {
        $production = Production::factory()->create([
            'status' => 'diproses',
            'qty_produksi' => 20,
        ]);

        $indicator = QcIndicator::factory()->create([
            'is_critical' => true,
        ]);

        $response = $this->put(
            route('admin.productions.qc', $production),
            [
                'indicators' => [
                    $indicator->id => 'gagal',
                ],
                'qc_threshold' => 80,
            ]
        );

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('disposals', [
            'disposable_id' => $production->id,
            'reason' => 'qc_failed',
        ]);

        $this->assertEquals(
            'rejected',
            $production->fresh()->status
        );
    }

    /** @test */
    public function qc_layak_can_be_finished_and_product_stock_increase()
    {
        $product = Product::factory()->create(['stok' => 0]);

        $production = Production::factory()->create([
            'product_id' => $product->id,
            'status' => 'diproses',
            'qty_produksi' => 30,

        ]);

        $indicator = QcIndicator::factory()->create([
            'is_critical' => false,
        ]);

        $this->put(route('admin.productions.qc', $production), [
            'indicators' => [
                $indicator->id => 'lulus',
            ],
            'qc_threshold' => 70,
        ]);

        $this->put(route('admin.productions.selesai', $production));

        $this->assertEquals(
            30,
            $product->fresh()->stok
        );

        $this->assertEquals(
            'selesai',
            $production->fresh()->status
        );
    }
}
