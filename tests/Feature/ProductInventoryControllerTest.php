<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\StockMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductInventoryControllerTest extends TestCase
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
    public function index_menampilkan_daftar_produk()
    {
        Product::factory()->count(3)->create();

        $response = $this->get(route('admin.product.index'));

        $response->assertOk();
        $response->assertJsonCount(3);
    }

    /** @test */
    public function show_menampilkan_batch_produk()
    {
        $product = Product::factory()->create();

        ProductStock::create([
            'product_id' => $product->id,
            'qty' => 50,
            'received_date' => now(),
            'expired_date' => null,
        ]);

        $response = $this->get(route('admin.product.show', $product));

        $response->assertOk();
        $response->assertJsonStructure([
            'product',
            'batches'
        ]);
    }

    /** @test */
    public function adjust_in_menambah_batch_dan_update_summary()
    {
        $product = Product::factory()->create([
            'stok' => 0
        ]);

        $response = $this->post(route('admin.inventory.product.adjust', $product), [
            'type' => 'in',
            'qty' => 100
        ]);

        $response->assertRedirect();

        // Batch tercipta
        $this->assertDatabaseHas('product_stocks', [
            'product_id' => $product->id,
            'qty' => 100
        ]);

        // Summary naik
        $this->assertEquals(100, $product->fresh()->stok);

        // Movement tercatat
        $this->assertDatabaseHas('stock_movements', [
            'stockable_id' => $product->id,
            'stockable_type' => Product::class,
            'type' => 'in',
            'quantity' => 100,
        ]);
    }

    /** @test */
    public function adjust_out_mengurangi_stok_fifo()
    {
        $product = Product::factory()->create([
            'stok' => 100
        ]);

        // Batch lama
        ProductStock::create([
            'product_id' => $product->id,
            'qty' => 50,
            'received_date' => now()->subDays(5),
        ]);

        // Batch baru
        ProductStock::create([
            'product_id' => $product->id,
            'qty' => 50,
            'received_date' => now(),
        ]);

        $response = $this->post(route('admin.inventory.product.adjust', $product), [
            'type' => 'out',
            'qty' => 70
        ]);

        $response->assertRedirect();

        // Batch pertama habis
        $oldestBatch = ProductStock::orderBy('received_date')->first();
        $this->assertEquals(0, $oldestBatch->qty);

        // Batch kedua sisa 30
        $latestBatch = ProductStock::orderBy('received_date', 'desc')->first();
        $this->assertEquals(30, $latestBatch->qty);

        // Summary turun
        $this->assertEquals(30, $product->fresh()->stok);

        // Movement tercatat
        $this->assertDatabaseHas('stock_movements', [
            'type' => 'out',
            'quantity' => 70
        ]);
    }

    /** @test */
    public function adjust_out_gagal_jika_stok_tidak_cukup()
    {
        $product = Product::factory()->create([
            'stok' => 10
        ]);

        ProductStock::create([
            'product_id' => $product->id,
            'qty' => 10,
            'received_date' => now(),
        ]);

        $response = $this->post(route('admin.inventory.product.adjust', $product), [
            'type' => 'out',
            'qty' => 50
        ]);

        $response->assertStatus(500);

        // Stok tidak berubah
        $this->assertEquals(10, $product->fresh()->stok);

        // Movement tidak tercatat
        $this->assertDatabaseMissing('stock_movements', [
            'quantity' => 50
        ]);
    }

    /** @test */
    public function sync_menyamakan_summary_dengan_total_batch()
    {
        $product = Product::factory()->create([
            'stok' => 0
        ]);

        ProductStock::create([
            'product_id' => $product->id,
            'qty' => 40,
            'received_date' => now(),
        ]);

        ProductStock::create([
            'product_id' => $product->id,
            'qty' => 60,
            'received_date' => now(),
        ]);

        $response = $this->post(route('admin.inventory.product.sync', $product));

        $response->assertRedirect();

        $this->assertEquals(100, $product->fresh()->stok);
    }
}