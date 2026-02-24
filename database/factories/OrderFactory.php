<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use App\Models\Kambing;
use App\Models\Domba;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        // Pilih tipe orderable secara random
        $type = $this->faker->randomElement([
            Kambing::class,
            Domba::class,
            Product::class
        ]);

        $orderable = $type::factory()->create();

        return [
            'order_id' => 'ORD-' . $this->faker->unique()->randomNumber(6),

            'user_id' => User::factory(),

            'orderable_id' => $orderable->id,
            'orderable_type' => $type,

            'snap_token' => $this->faker->uuid,
            'gross_amount' => $this->faker->numberBetween(100000, 10000000),

            'name' => $this->faker->name,
            'address' => $this->faker->address,
            'phone' => $this->faker->phoneNumber,

            'qty' => $this->faker->numberBetween(1, 3),

            'status' => 'pending',

            'payment_method' => $this->faker->randomElement(['midtrans', 'manual']),

            'bukti_transfer' => null,
            'sender_name' => null,
            'bank_origin' => null,
            'transfer_date' => null,

            'admin_notes' => null,
        ];
    }
}