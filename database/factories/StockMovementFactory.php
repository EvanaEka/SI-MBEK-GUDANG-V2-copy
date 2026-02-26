<?php

namespace Database\Factories;

use App\Models\StockMovement;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockMovementFactory extends Factory
{
    protected $model = StockMovement::class;

    public function definition(): array
    {
        $type = $this->faker->randomElement(['in', 'out']);

        return [
            // morph ke Product
            'stockable_id' => Product::factory(),
            'stockable_type' => Product::class,

            'type' => $type,

            'quantity' => $this->faker->numberBetween(1, 20),

            'source' => $this->faker->randomElement([
                'purchase',
                'production',
                'sale',
                'manual_adjustment'
            ]),

            'reference_id' => $this->faker->optional()->numberBetween(1, 50),

            'catatan' => $this->faker->optional()->sentence(),

            'movement_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}