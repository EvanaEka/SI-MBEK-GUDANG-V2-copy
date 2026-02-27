<?php

namespace Database\Factories;

use App\Models\Disposal;
use App\Models\Admin;
use App\Models\Production;
use App\Models\Material;
use Illuminate\Database\Eloquent\Factories\Factory;

class DisposalFactory extends Factory
{
    protected $model = Disposal::class;

    public function definition(): array
    {
        return [
            'disposable_id' => null,
            'disposable_type' => null,
            'quantity' => $this->faker->numberBetween(1, 50),
            'reason' => $this->faker->word(),
            'notes' => $this->faker->sentence(),
            'created_by' => Admin::factory(),
        ];
    }

    public function forProduction()
    {
        return $this->state(function () {
            $production = Production::factory()->create();

            return [
                'disposable_id' => $production->id,
                'disposable_type' => Production::class,
            ];
        });
    }

    public function forMaterial()
    {
        return $this->state(function () {
            $material = Material::factory()->create();

            return [
                'disposable_id' => $material->id,
                'disposable_type' => Material::class,
            ];
        });
    }
}