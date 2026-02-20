<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Admin;
use App\Models\Owner;
use App\Models\Supplier;
use App\Models\Material;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\MaterialStock;
use App\Models\ActivityLog;

class PurchaseOrderTest extends TestCase
{
    use RefreshDatabase;

    /* =========================================================
     * STORE
     * ========================================================= */

    /** @test */
    public function admin_can_create_po()
    {
        $admin = Admin::factory()->create();
        $supplier = Supplier::factory()->create();
        $material = Material::factory()->create();

        $this->actingAs($admin, 'admin');

        $this->post(route('admin.purchase-orders.store'), [
            'supplier_id' => $supplier->id,
            'tanggal_pesan' => now()->toDateString(),
            'type' => 'material',
            'items' => [
                [
                    'material_id' => $material->id,
                    'jumlah' => 5,
                    'harga_satuan' => 1000,
                ]
            ]
        ])->assertRedirect();

        $this->assertDatabaseHas('purchase_orders', [
            'status' => 'draft'
        ]);
    }

    /** @test */
    public function cannot_create_po_without_items()
    {
        $admin = Admin::factory()->create();
        $supplier = Supplier::factory()->create();

        $this->actingAs($admin, 'admin');

        $this->post(route('admin.purchase-orders.store'), [
            'supplier_id' => $supplier->id,
            'tanggal_pesan' => now()->toDateString(),
        ])->assertSessionHasErrors('items');
    }

    /* =========================================================
     * APPROVE
     * ========================================================= */

    /** @test */
    public function owner_can_approve_po()
    {
        $owner = Owner::factory()->create();
        $po = PurchaseOrder::factory()->create(['status' => 'draft']);

        $this->actingAs($owner, 'owner');

        $this->patch(route('owner.purchase-orders.approve', $po));

        $this->assertEquals('dipesan', $po->fresh()->status);
    }

    /** @test */
    public function cannot_approve_if_not_draft()
    {
        $owner = Owner::factory()->create();
        $po = PurchaseOrder::factory()->create(['status' => 'dipesan']);

        $this->actingAs($owner, 'owner');

        $this->patch(route('owner.purchase-orders.approve', $po))
            ->assertStatus(500);
    }

    /* =========================================================
     * RECEIVE - MATERIAL
     * ========================================================= */

    /** @test */
    public function receive_material_success()
    {
        $admin = Admin::factory()->create();
        $material = Material::factory()->create(['stok' => 0]);

        $po = PurchaseOrder::factory()->create([
            'status' => 'dipesan',
            'type' => 'material'
        ]);

        $item = PurchaseOrderItem::factory()->create([
            'purchase_order_id' => $po->id,
            'material_id' => $material->id,
            'jumlah' => 10,
        ]);

        $this->actingAs($admin, 'admin');

        $this->post(route('admin.purchase-orders.receive', $po), [
            'items' => [
                [
                    'id' => $item->id,
                    'jumlah_diterima' => 10,
                ]
            ]
        ]);

        $this->assertEquals(10, $material->fresh()->stok);
        $this->assertEquals('diterima', $po->fresh()->status);
        $this->assertDatabaseHas('material_stocks', [
            'material_id' => $material->id,
            'qty' => 10
        ]);
    }

    /* =========================================================
     * RECEIVE - PRODUCT SUCCESS
     * ========================================================= */

    /** @test */
    public function receive_product_obat_success()
    {
        $admin = Admin::factory()->create();

        $product = Product::factory()->create([
            'type' => 'obat',
            'asal' => 'pembelian',
            'stok' => 0
        ]);

        $po = PurchaseOrder::factory()->create([
            'status' => 'dipesan',
            'type' => 'product'
        ]);

        $item = PurchaseOrderItem::factory()
            ->forProduct($product) // <-- PASS PRODUCT NYA DI SINI
            ->for($po)
            ->create([
                'jumlah' => 5,
            ]);

        $this->actingAs($admin, 'admin');

        $this->post(route('admin.purchase-orders.receive', $po), [
            'items' => [
                [
                    'id' => $item->id,
                    'jumlah_diterima' => 5,
                ]
            ]
        ]);

        $this->assertEquals(5, $product->fresh()->stok);
    }

    /* =========================================================
     * RECEIVE - PRODUCT EXCEPTION BRANCH
     * ========================================================= */

    /** @test */
    public function cannot_receive_product_if_not_obat()
    {
        $this->withoutExceptionHandling();

        $admin = Admin::factory()->create();

        $product = Product::factory()->create([
            'type' => 'pakan',
            'asal' => 'pembelian',
        ]);

        $po = PurchaseOrder::factory()->create([
            'status' => 'dipesan',
            'type' => 'product'
        ]);

        $item = PurchaseOrderItem::factory()->create([
            'purchase_order_id' => $po->id,
            'product_id' => $product->id,
            'jumlah' => 5,
        ]);

        $this->actingAs($admin, 'admin');

        $this->expectException(\Exception::class);

        $this->post(route('admin.purchase-orders.receive', $po), [
            'items' => [
                [
                    'id' => $item->id,
                    'jumlah_diterima' => 5,
                ]
            ]
        ]);
    }

    /** @test */
    public function cannot_receive_product_if_asal_not_pembelian()
    {
        $this->withoutExceptionHandling();

        $admin = Admin::factory()->create();

        $product = Product::factory()->create([
            'type' => 'obat',
            'asal' => 'produksi',
        ]);

        $po = PurchaseOrder::factory()->create([
            'status' => 'dipesan',
            'type' => 'product'
        ]);

        $item = PurchaseOrderItem::factory()->create([
            'purchase_order_id' => $po->id,
            'product_id' => $product->id,
            'jumlah' => 5,
        ]);

        $this->actingAs($admin, 'admin');

        $this->expectException(\Exception::class);

        $this->post(route('admin.purchase-orders.receive', $po), [
            'items' => [
                [
                    'id' => $item->id,
                    'jumlah_diterima' => 5,
                ]
            ]
        ]);
    }

    /* =========================================================
     * SECURITY & EDGE CASE
     * ========================================================= */

    /** @test */
    public function cannot_receive_if_already_completed()
    {
        $admin = Admin::factory()->create();

        $po = PurchaseOrder::factory()->create([
            'status' => 'diterima',
            'type' => 'material'
        ]);

        $this->actingAs($admin, 'admin');

        $this->post(route('admin.purchase-orders.receive', $po), [
            'items' => []
        ])->assertSessionHas('error');
    }

    /** @test */
    public function cannot_receive_item_not_belonging_to_po()
    {
        $admin = Admin::factory()->create();
        $material = Material::factory()->create();

        $po1 = PurchaseOrder::factory()->create(['status' => 'dipesan', 'type' => 'material']);
        $po2 = PurchaseOrder::factory()->create(['status' => 'dipesan', 'type' => 'material']);

        $item = PurchaseOrderItem::factory()->create([
            'purchase_order_id' => $po2->id,
            'material_id' => $material->id,
            'jumlah' => 10,
        ]);

        $this->actingAs($admin, 'admin');

        $this->post(route('admin.purchase-orders.receive', $po1), [
            'items' => [
                [
                    'id' => $item->id,
                    'jumlah_diterima' => 10,
                ]
            ]
        ])->assertSessionHas('error');
    }
}