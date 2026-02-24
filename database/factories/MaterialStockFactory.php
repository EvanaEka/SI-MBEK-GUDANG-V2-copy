<?php

namespace Database\Factories;

use App\Models\MaterialStock;
use App\Models\Material;
use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaterialStockFactory extends Factory
{
    protected $model = MaterialStock::class;

    public function definition(): array
    {
        $receivedDate = $this->faker->dateTimeBetween('-3 months', 'now');

        return [
            'material_id' => Material::factory(),

            // Batch masuk realistis
            'qty' => $this->faker->numberBetween(50, 200),

            // FIFO based on received_date
            'received_date' => $receivedDate->format('Y-m-d'),

            // 50% kemungkinan punya expired date
            'expired_date' => $this->faker->boolean(70)
                ? (clone $receivedDate)
                    ->modify('+6 months')
                    ->format('Y-m-d')
                : null,

            // Harga optional
            'price_per_unit' => $this->faker->optional()->randomFloat(2, 1000, 10000),

            // Wajib sesuai migration
            'created_by' => Admin::factory(),

        ];
    }

    /**
     * State: Batch Expired
     */
    public function expired(): static
    {
        return $this->state(fn() => [
            'expired_date' => now()->subDays(5)->format('Y-m-d'),
        ]);
    }

    /**
     * State: Batch Tanpa Expired
     */
    public function noExpired(): static
    {
        return $this->state(fn() => [
            'expired_date' => null,
        ]);
    }

    /**
     * State: Stok Kecil (untuk testing kekurangan stok)
     */
    public function lowStock(): static
    {
        return $this->state(fn() => [
            'qty' => 5,
        ]);
    }
}
