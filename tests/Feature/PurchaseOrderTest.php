<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Owner;
use App\Models\Supplier;
use App\Models\Material;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\MaterialStock;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PurchaseOrderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_create_purchase_order()
    {
        $admin = Admin::factory()->create();
        $supplier = Supplier::factory()->create();
        $material = Material::factory()->create();

        $this->actingAs($admin, 'admin');

        $response = $this->post(route('admin.purchase-orders.store'), [
            'supplier_id' => $supplier->id,
            'tanggal_pesan' => now()->toDateString(),
            'items' => [
                [
                    'material_id' => $material->id,
                    'jumlah' => 100,
                    'harga_satuan' => 5000,
                ]
            ]
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('purchase_orders', [
            'supplier_id' => $supplier->id,
            'status' => 'draft',
        ]);

        $this->assertDatabaseHas('purchase_order_items', [
            'material_id' => $material->id,
            'jumlah' => 100,
        ]);
    }

    /** @test */
    public function owner_can_approve_purchase_order()
    {
        $owner = Owner::factory()->create([
            'must_change_password' => false,
        ]);
        $po = PurchaseOrder::factory()->create([
            'status' => 'draft'
        ]);

        $this->actingAs($owner, 'owner');

        $response = $this->patch(route('owner.purchase-orders.approve', $po));

        $response->assertRedirect();

        $this->assertDatabaseHas('purchase_orders', [
            'id' => $po->id,
            'status' => 'dipesan',
        ]);
    }

    /** @test */
    public function admin_cannot_approve_purchase_order()
    {
        $admin = Admin::factory()->create([
            'must_change_password' => false,
        ]);

        $po = PurchaseOrder::factory()->create([
            'status' => 'draft'
        ]);

        $this->actingAs($admin, 'admin');

        $response = $this->patch(route('admin.purchase-orders.approve', $po));

        $response->assertStatus(403);
    }

    /** @test */
    public function cannot_receive_if_not_approved()
    {
        $admin = Admin::factory()->create([
            'must_change_password' => false,
        ]);
        $po = PurchaseOrder::factory()->create([
            'status' => 'draft',
            'type' => 'material'
        ]);

        $this->actingAs($admin, 'admin');

        $response = $this->post(route('admin.purchase-orders.receive', $po), [
            'items' => []
        ]);

        $response->assertSessionHas('error');
    }

    /** @test */
    public function receive_material_creates_stock_and_updates_material()
    {
        $admin = Admin::factory()->create();
        $supplier = Supplier::factory()->create();
        $material = Material::factory()->create(['stok' => 0]);

        $po = PurchaseOrder::factory()->create([
            'status' => 'dipesan',
            'type' => 'material',
            'supplier_id' => $supplier->id,
        ]);

        $item = PurchaseOrderItem::factory()->create([
            'purchase_order_id' => $po->id,
            'material_id' => $material->id,
            'jumlah' => 100,
            'jumlah_diterima' => 0,
        ]);

        $this->actingAs($admin, 'admin');

        $this->post(route('admin.purchase-orders.receive', $po), [
            'items' => [
                [
                    'id' => $item->id,
                    'jumlah_diterima' => 100,
                    'expired_date' => now()->addMonth()->toDateString(),
                ]
            ]
        ]);

        $this->assertDatabaseHas('material_stocks', [
            'material_id' => $material->id,
            'qty' => 100,
        ]);

        $this->assertEquals(100, $material->fresh()->stok);

        $this->assertEquals('diterima', $po->fresh()->status);
    }

    /** @test */
    public function over_delivery_is_recorded()
    {
        $admin = Admin::factory()->create();
        $material = Material::factory()->create(['stok' => 0]);

        $po = PurchaseOrder::factory()->create([
            'status' => 'dipesan',
            'type' => 'material',
        ]);

        $item = PurchaseOrderItem::factory()->create([
            'purchase_order_id' => $po->id,
            'material_id' => $material->id,
            'jumlah' => 100,
        ]);

        $this->actingAs($admin, 'admin');

        $this->post(route('admin.purchase-orders.receive', $po), [
            'items' => [
                [
                    'id' => $item->id,
                    'jumlah_diterima' => 120,
                ]
            ]
        ]);

        $this->assertDatabaseHas('purchase_order_items', [
            'id' => $item->id,
            'selisih' => 20,
        ]);
    }

    /** @test */
    public function partial_delivery_is_recorded()
    {
        $admin = Admin::factory()->create();
        $material = Material::factory()->create(['stok' => 0]);

        $po = PurchaseOrder::factory()->create([
            'status' => 'dipesan',
            'type' => 'material',
        ]);

        $item = PurchaseOrderItem::factory()->create([
            'purchase_order_id' => $po->id,
            'material_id' => $material->id,
            'jumlah' => 100,
        ]);

        $this->actingAs($admin, 'admin');

        $this->post(route('admin.purchase-orders.receive', $po), [
            'items' => [
                [
                    'id' => $item->id,
                    'jumlah_diterima' => 80,
                ]
            ]
        ]);

        $this->assertDatabaseHas('purchase_order_items', [
            'id' => $item->id,
            'selisih' => -20,
        ]);
    }

    /** @test */
    public function cannot_double_receive_item()
    {

        $admin = Admin::factory()->create();
        $material = Material::factory()->create(['stok' => 0]);

        $po = PurchaseOrder::factory()->create([
            'status' => 'dipesan',
            'type' => 'material',
        ]);

        $item = PurchaseOrderItem::factory()->create([
            'purchase_order_id' => $po->id,
            'material_id' => $material->id,
            'jumlah' => 100,
            'jumlah_diterima' => 50,
        ]);

        $this->actingAs($admin, 'admin');

        $response = $this->post(route('admin.purchase-orders.receive', $po), [
            'items' => [
                [
                    'id' => $item->id,
                    'jumlah_diterima' => 50,
                ]
            ]
        ]);

        $response->assertStatus(500);
    }
}
