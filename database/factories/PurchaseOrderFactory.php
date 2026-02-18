<?php

namespace Database\Factories;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Admin;
use App\Models\Owner;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseOrderFactory extends Factory
{
    protected $model = PurchaseOrder::class;

    public function definition(): array
    {
        // Default: Owner pesan
        $pemesan = Owner::factory()->create();

        // Default: yang mencatat juga Owner
        $pencatat = $pemesan;

        return [
            'kode_po' => 'PO-' . now()->format('Ymd') . '-' .
                $this->faker->unique()->numberBetween(1000, 9999),

            'supplier_id' => Supplier::factory(),

            'type' => 'material',

            'tanggal_pesan' => $this->faker->date(),

            'status' => 'draft',

            // ✅ Morph dipesan_oleh
            'dipesan_oleh_id' => $pemesan->id,
            'dipesan_oleh_type' => get_class($pemesan),

            // ✅ Morph dicatat_oleh
            'dicatat_oleh_id' => $pencatat->id,
            'dicatat_oleh_type' => get_class($pencatat),

            'catatan_owner' => $this->faker->optional()->sentence(),
        ];
    }

    /**
     * PO dipesan oleh Admin
     */
    public function dipesanOlehAdmin(): self
    {
        return $this->state(function () {
            $admin = Admin::factory()->create();

            return [
                'dipesan_oleh_id' => $admin->id,
                'dipesan_oleh_type' => get_class($admin),
                'dicatat_oleh_id' => $admin->id,
                'dicatat_oleh_type' => get_class($admin),
            ];
        });
    }

    /**
     * Admin mencatat untuk Owner
     */
    public function adminCatatUntukOwner(): self
    {
        return $this->state(function () {
            $owner = Owner::factory()->create();
            $admin = Admin::factory()->create();

            return [
                'dipesan_oleh_id' => $owner->id,
                'dipesan_oleh_type' => get_class($owner),
                'dicatat_oleh_id' => $admin->id,
                'dicatat_oleh_type' => get_class($admin),
            ];
        });
    }

    /**
     * Set status tertentu
     */
    public function status(string $status): self
    {
        return $this->state(fn() => [
            'status' => $status,
        ]);
    }
}
