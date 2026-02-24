<?php

namespace Database\Factories;

use App\Models\Material;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaterialFactory extends Factory
{
    protected $model = Material::class;

    public function definition(): array
    {
        $pemakaian = $this->faker->numberBetween(1, 20);
        $leadTime = $this->faker->numberBetween(1, 7);
        $safetyStock = $this->faker->numberBetween(0, 30);

        return [
            'nama_bahan' => $this->faker->words(2, true),
            'kategori' => $this->faker->randomElement(['Pakan', 'Obat']),
            'satuan' => $this->faker->randomElement(['kg', 'liter', 'pcs']),
            'stok' => 0,

            'pemakaian_rata_rata' => $pemakaian,
            'lead_time' => $leadTime,
            'safety_stock' => $safetyStock,

            'deskripsi' => $this->faker->sentence(),
        ];
    }

    /**
     * STATE: stok berada di bawah ROP
     */
    public function belowRop(): static
    {
        return $this->state(function (array $attributes) {
            $rop = ($attributes['pemakaian_rata_rata'] * $attributes['lead_time'])
                + $attributes['safety_stock'];

            return [
                'stok' => max(0, $rop - 1),
            ];
        });
    }
}
