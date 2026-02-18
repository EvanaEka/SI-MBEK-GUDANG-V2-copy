<?php

namespace Database\Factories;

use App\Models\QcIndicator;
use Illuminate\Database\Eloquent\Factories\Factory;

class QcIndicatorFactory extends Factory
{
    protected $model = QcIndicator::class;

    public function definition(): array
    {
        return [
            'name' => 'Indikator ' . $this->faker->unique()->word(),
            'is_critical' => false,
            'is_active' => true,
        ];
    }

    /**
     * State: indikator kritis
     */
    public function critical(): static
    {
        return $this->state(fn () => [
            'is_critical' => true,
        ]);
    }

    /**
     * State: indikator non-kritis
     */
    public function nonCritical(): static
    {
        return $this->state(fn () => [
            'is_critical' => false,
        ]);
    }

    /**
     * State: indikator tidak aktif
     */
    public function inactive(): static
    {
        return $this->state(fn () => [
            'is_active' => false,
        ]);
    }
}
