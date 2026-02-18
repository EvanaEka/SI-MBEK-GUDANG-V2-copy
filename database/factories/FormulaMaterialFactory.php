<?php

namespace Database\Factories;

use App\Models\FormulaMaterial;
use App\Models\Formula;
use App\Models\Material;
use Illuminate\Database\Eloquent\Factories\Factory;

class FormulaMaterialFactory extends Factory
{
    protected $model = FormulaMaterial::class;

    public function definition(): array
    {
        return [
            'formula_id' => Formula::factory(),
            'material_id' => Material::factory(),
            'percentage' => $this->faker->randomFloat(2, 1, 100),
        ];
    }
}
