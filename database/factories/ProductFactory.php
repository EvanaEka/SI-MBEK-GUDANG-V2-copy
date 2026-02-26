<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Formula;
use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'kode' => 'PRD-' . strtoupper(Str::random(6)),
            'nama' => $this->faker->words(2, true),
            'harga' => $this->faker->numberBetween(10000, 150000),
            'stok' => 0,
            'rop' => $this->faker->numberBetween(5, 20),

            // default jadi pakan produksi
            'type' => 'pakan',
            'source' => 'produksi',

            'formula_id' => Formula::factory(),
            'created_by' => Admin::factory(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | STATE: PAKAN
    |--------------------------------------------------------------------------
    */
    public function pakan(): static
    {
        return $this->state(fn() => [
            'type' => 'pakan',
            'source' => 'produksi',
            'formula_id' => Formula::factory(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | STATE: OBAT
    |--------------------------------------------------------------------------
    */
    public function obat(): static
    {
        return $this->state(fn() => [
            'type' => 'obat',
            'source' => 'pembelian',
            'formula_id' => null, // obat biasanya tidak pakai formula
        ]);
    }
}