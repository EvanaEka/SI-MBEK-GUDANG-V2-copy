<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Material;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Order;
use App\Models\PurchaseOrder;
use App\Models\ActivityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WarehouseDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_loads_successfully()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'admin');

        $response = $this->get(route('admin.warehouse.dashboard'));

        $response->assertStatus(200);
    }

    public function test_summary_counts_are_correct()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'admin');

        /*
        |--------------------------------------------------------------------------
        | MATERIAL
        |--------------------------------------------------------------------------
        */

        // Aman
        Material::factory()->create([
            'stok' => 100,
            'pemakaian_rata_rata' => 5,
            'lead_time' => 5,
            'safety_stock' => 10,
        ]);

        // Below ROP (ROP = 35)
        Material::factory()->create([
            'stok' => 10,
            'pemakaian_rata_rata' => 5,
            'lead_time' => 5,
            'safety_stock' => 10,
        ]);

        /*
        |--------------------------------------------------------------------------
        | PRODUCT
        |--------------------------------------------------------------------------
        */

        Product::factory()->create([
            'stok' => 100,
            'rop' => 20,
        ]);

        Product::factory()->create([
            'stok' => 10,
            'rop' => 50,
        ]);

        /*
        |--------------------------------------------------------------------------
        | SUPPLIER & PO
        |--------------------------------------------------------------------------
        */

        $supplier = Supplier::factory()->create();

        PurchaseOrder::factory()->create([
            'supplier_id' => $supplier->id
        ]);

        /*
        |--------------------------------------------------------------------------
        | ORDER
        |--------------------------------------------------------------------------
        */

        $buyer = User::factory()->create();

        Order::factory()->create([
            'user_id' => $buyer->id,
            'status' => 'success'
        ]);

        /*
        |--------------------------------------------------------------------------
        | ACTIVITY LOG
        |--------------------------------------------------------------------------
        */

        ActivityLog::factory()->create([
            'type' => 'po_received'
        ]);

        $response = $this->get(route('admin.warehouse.dashboard'));

        $response->assertStatus(200);

        // 🔥 TEST SUMMARY
        $response->assertJsonPath('summary.total_material', 2);
        $response->assertJsonPath('summary.material_below_rop', 1);
        $response->assertJsonPath('summary.total_product', 2);
        $response->assertJsonPath('summary.product_below_rop', 1);
        $response->assertJsonPath('summary.total_supplier', 1);
        $response->assertJsonPath('summary.total_buyer', 1);

        // 🔥 TEST LIST DATA
        $response->assertJsonCount(1, 'materialsLow');
        $response->assertJsonCount(1, 'productsLow');
        $response->assertJsonCount(1, 'supplierDistribution');
        $response->assertJsonCount(1, 'buyerDistribution');
        $response->assertJsonCount(1, 'recentActivities');
    }

    public function test_material_below_rop_scope_works()
    {
        $material = Material::factory()->create([
            'stok' => 5,
            'pemakaian_rata_rata' => 10,
            'lead_time' => 2,
            'safety_stock' => 5,
        ]);
        // ROP = 25

        $this->assertTrue($material->fresh()->isBelowRop());
        $this->assertCount(1, Material::belowRop()->get());
    }
}
