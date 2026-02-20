<?php

namespace Database\Factories;

use App\Models\ActivityLog;
use App\Models\Admin;
use App\Models\Owner;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityLogFactory extends Factory
{
    protected $model = ActivityLog::class;

    public function definition(): array
    {
        // Default: actor adalah Admin
        $actor = Admin::factory()->create();

        return [
            'actor_id' => $actor->id,
            'actor_type' => get_class($actor),

            'type' => $this->faker->randomElement([
                'create',
                'update',
                'received',
                'checked',
                'approve'
            ]),

            'module' => $this->faker->randomElement([
                'purchase_order',
                'production',
                'product_allocation',
                'production_qc',
                'disposal',
                'order'
            ]),

            'description' => $this->faker->optional()->sentence(),
        ];
    }

    /**
     * Actor adalah Owner
     */
    public function byOwner(): self
    {
        return $this->state(function () {
            $owner = Owner::factory()->create();

            return [
                'actor_id' => $owner->id,
                'actor_type' => get_class($owner),
            ];
        });
    }

    /**
     * Actor adalah Admin
     */
    public function byAdmin(): self
    {
        return $this->state(function () {
            $admin = Admin::factory()->create();

            return [
                'actor_id' => $admin->id,
                'actor_type' => get_class($admin),
            ];
        });
    }
}
