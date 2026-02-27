<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\StockMovement;
use App\Models\Production;
use App\Models\Disposal;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate()
    {
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');
        return $admin;
    }

    /** @test */
    public function can_get_stock_report()
    {
        $this->authenticate();

        StockMovement::factory()->create([
            'type' => 'in',
            'quantity' => 100,
            'movement_date' => now(),
        ]);

        $response = $this->getJson('/admin/report/stock');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'filters',
                'data' => [
                    'current_page',
                    'data'
                ]
            ]);
    }

    /** @test */
    public function can_filter_stock_report_by_type()
    {
        $this->authenticate();

        StockMovement::factory()->create([
            'type' => 'in',
            'movement_date' => now(),
        ]);

        StockMovement::factory()->create([
            'type' => 'out',
            'movement_date' => now(),
        ]);

        $response = $this->getJson('/admin/report/stock?type=in');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'type' => 'in'
            ])
            ->assertJsonMissing([
                'type' => 'out'
            ]);
    }

    /** @test */
    public function can_get_production_report()
    {
        $this->authenticate();

        Production::factory()->create([
            'status' => 'selesai',
            'production_date' => now(),
            'qty_produksi' => 200,
        ]);

        $response = $this->getJson('/admin/report/production');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'filters',
                'data' => [
                    'current_page',
                    'data'
                ]
            ]);
    }

    /** @test */
    public function can_get_disposal_report()
    {
        $this->authenticate();

        Disposal::factory()
            ->forProduction()
            ->create();

        $response = $this->getJson('/admin/report/disposal');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'filters',
                'data' => [
                    'current_page',
                    'data'
                ]
            ]);
    }

    /** @test */
    public function can_get_monthly_report()
    {
        $this->authenticate();

        StockMovement::factory()->create([
            'type' => 'in',
            'quantity' => 100,
            'movement_date' => now(),
        ]);

        $response = $this->getJson('/admin/report/monthly?year=' . now()->year);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'year',
                'data' => [
                    'stock_summary',
                    'production_summary'
                ]
            ]);
    }
    
    public function guest_cannot_access_report()
    {
        $response = $this->getJson('/admin/report/stock');

        $response->assertStatus(401);
    }
}