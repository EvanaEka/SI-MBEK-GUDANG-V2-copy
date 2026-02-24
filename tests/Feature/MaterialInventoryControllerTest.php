<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Material;
use App\Models\MaterialStock;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MaterialInventoryControllerTest extends TestCase
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
    public function index_menampilkan_daftar_material()
    {
        Material::factory()->count(3)->create();

        $response = $this->get(route('admin.material.index'));

        $response->assertOk(); // tidak peduli view
    }

    /** @test */
    public function show_menampilkan_detail_material()
    {
        $material = Material::factory()->create();

        MaterialStock::factory()->create([
            'material_id'   => $material->id,
            'qty'           => 50,
            'received_date' => now(),
            'created_by'    => $this->admin->id,
        ]);

        $response = $this->get(route('admin.material.show', $material));

        $response->assertOk();
    }

    /** @test */
    public function adjust_in_menambah_batch_dan_update_summary()
    {
        $material = Material::factory()->create([
            'stok' => 0
        ]);

        $response = $this->post(route('admin.inventory.material.adjust', $material), [
            'type'     => 'in',
            'quantity' => 100
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('material_stocks', [
            'material_id' => $material->id,
            'qty'         => 100
        ]);

        $this->assertEquals(100, $material->fresh()->stok);

        $this->assertDatabaseHas('stock_movements', [
            'stockable_id' => $material->id,
            'type'         => 'in',
            'quantity'     => 100,
        ]);
    }

    /** @test */
    public function adjust_out_mengurangi_stok_dengan_fifo()
    {
        $material = Material::factory()->create([
            'stok' => 0
        ]);

        // batch lama (lebih tua)
        MaterialStock::factory()->create([
            'material_id'   => $material->id,
            'qty'           => 50,
            'received_date' => now()->subDays(5),
            'created_by'    => $this->admin->id,
        ]);

        // batch baru
        MaterialStock::factory()->create([
            'material_id'   => $material->id,
            'qty'           => 50,
            'received_date' => now(),
            'created_by'    => $this->admin->id,
        ]);

        $material->update(['stok' => 100]);

        $response = $this->post(route('admin.inventory.material.adjust', $material), [
            'type'     => 'out',
            'quantity' => 70
        ]);

        $response->assertOk();

        $stocks = MaterialStock::where('material_id', $material->id)
            ->orderBy('received_date')
            ->get();

        // batch pertama habis
        $this->assertEquals(0, $stocks[0]->qty);

        // batch kedua sisa 30
        $this->assertEquals(30, $stocks[1]->qty);

        $this->assertEquals(30, $material->fresh()->stok);
    }

    /** @test */
    public function adjust_out_gagal_jika_stok_tidak_cukup()
    {
        $material = Material::factory()->create([
            'stok' => 10
        ]);

        MaterialStock::factory()->create([
            'material_id'   => $material->id,
            'qty'           => 10,
            'received_date' => now(),
            'created_by'    => $this->admin->id
        ]);

        $response = $this->post(route('admin.inventory.material.adjust', $material), [
            'type'     => 'out',
            'quantity' => 50
        ]);

        // tergantung implementasi kamu:
        // kalau pakai validation -> 422
        // kalau throw exception -> 500
        $response->assertStatus(500);

        $this->assertEquals(10, $material->fresh()->stok);

        $this->assertDatabaseMissing('stock_movements', [
            'stockable_id' => $material->id,
            'quantity'     => 50
        ]);
    }

    /** @test */
    public function sync_menyamakan_summary_dengan_total_batch()
    {
        $material = Material::factory()->create([
            'stok' => 0
        ]);

        MaterialStock::factory()->create([
            'material_id'   => $material->id,
            'qty'           => 40,
            'received_date' => now(),
            'created_by'    => $this->admin->id,
        ]);

        MaterialStock::factory()->create([
            'material_id'   => $material->id,
            'qty'           => 60,
            'received_date' => now(),
            'created_by'    => $this->admin->id,
        ]);

        $response = $this->post(route('admin.inventory.material.sync', $material));

        $response->assertOk();

        $this->assertEquals(100, $material->fresh()->stok);
    }
}