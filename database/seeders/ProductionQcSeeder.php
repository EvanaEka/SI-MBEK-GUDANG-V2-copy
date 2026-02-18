<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductionQc;

class ProductionQcSeeder extends Seeder
{
    public function run(): void
    {
        ProductionQc::factory()->count(2)->create();
    }
}
