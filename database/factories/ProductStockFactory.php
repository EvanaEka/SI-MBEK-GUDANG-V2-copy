<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductStockFactory extends Factory
{
    protected $model = ProductStock::class;

    public function definition(): array
    {
        $source = $this->faker->randomElement([
            'production',
            'purchase',
            'manual_adjustment'
        ]);

        return [
            'product_id' => Product::factory(), // otomatis buat product kalau belum ada
            'qty' => $this->faker->numberBetween(10, 500),

            'source' => $source,

            // reference_id nullable tergantung source
            'reference_id' => $source === 'manual_adjustment'
                ? null
                : $this->faker->numberBetween(1, 50),

            'received_date' => $this->faker->dateTimeBetween('-3 months', 'now'),

            'expired_date' => $this->faker->optional(0.7)
                ->dateTimeBetween('now', '+6 months'),

            'price_per_unit' => $this->faker->optional(0.9)
                ->randomFloat(2, 1000, 50000),
        ];
    }
}