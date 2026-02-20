<?php

namespace Database\Factories;

use App\Models\Material;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseOrderItemFactory extends Factory
{
    protected $model = PurchaseOrderItem::class;

    public function definition(): array
    {
        $jumlah = $this->faker->numberBetween(5, 50);
        $harga = $this->faker->numberBetween(1000, 10000);

        return [
            'purchase_order_id' => PurchaseOrder::factory(),

            // ❗ Default tidak set relasi apapun
            'material_id' => null,
            'product_id' => null,

            'jumlah' => $jumlah,
            'jumlah_diterima' => null,
            'selisih' => 0,

            'harga_satuan' => $harga,
            'subtotal' => $jumlah * $harga,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | STATES
    |--------------------------------------------------------------------------
    */

    public function forMaterial(?Material $material = null): static
    {
        return $this->state(fn() => [
            'material_id' => $material?->id ?? Material::factory(),
            'product_id' => null,
        ]);
    }

    public function forProduct(?Product $product = null): static
    {
        return $this->state(fn() => [
            'material_id' => null,
            'product_id' => $product?->id ?? Product::factory(),
        ]);
    }

    public function received(?int $qty = null): static
    {
        return $this->state(function (array $attributes) use ($qty) {
            $jumlah = $attributes['jumlah'];
            $diterima = $qty ?? $jumlah;

            return [
                'jumlah_diterima' => $diterima,
                'selisih' => $diterima - $jumlah,
            ];
        });
    }
}