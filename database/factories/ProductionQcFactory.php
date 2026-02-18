<?php

namespace Database\Factories;

use App\Models\ProductionQc;
use App\Models\Production;
use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductionQcFactory extends Factory
{
    protected $model = ProductionQc::class;

    public function definition(): array
    {
        $score = $this->faker->numberBetween(70, 100);
        $threshold = 80;

        return [
            'production_id' => Production::factory(),
            'score_non_kritis' => $score,
            'threshold' => $threshold,
            'status' => $score >= $threshold ? 'layak' : 'tidak_layak',
            'catatan' => $this->faker->optional()->sentence(),
            'created_by' => Admin::factory(),
        ];
    }
}
