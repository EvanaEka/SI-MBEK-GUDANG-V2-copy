<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\Domba;
use App\Models\Kambing;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
        $user = User::factory()->create();

        $statuses = [
            'pending',
            'settlement',
            'capture',
            'success',
            'failed',
            'expire',
            'cancel'
        ];

        $paymentMethod = $this->faker->randomElement(['midtrans', 'manual']);
        $status = $this->faker->randomElement($statuses);

        return [
            'user_id' => $user->id,

            // Polymorphic ke Product
            'orderable_id' => $orderable->id,
            'orderable_type' => $type,

            'order_id' => 'ORD-' . strtoupper(Str::random(8)),
            'snap_token' => $paymentMethod === 'midtrans' ? Str::random(32) : null,

            'gross_amount' => $this->faker->numberBetween(100000, 500000),

            'name' => $this->faker->name(),
            'address' => $this->faker->address(),
            'phone' => $this->faker->phoneNumber(),

            'qty' => $this->faker->numberBetween(1, 5),

            'status' => $status,
            'payment_method' => $paymentMethod,

            'bukti_transfer' => $paymentMethod === 'manual'
                ? 'bukti_' . Str::random(6) . '.jpg'
                : null,

            'sender_name' => $paymentMethod === 'manual'
                ? $this->faker->name()
                : null,

            'bank_origin' => $paymentMethod === 'manual'
                ? $this->faker->randomElement(['BCA', 'BRI', 'BNI', 'Mandiri'])
                : null,

            'transfer_date' => $paymentMethod === 'manual'
                ? $this->faker->date()
                : null,

            'admin_notes' => $this->faker->optional()->sentence(),
        ];
    }
}