<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $total = $this->faker->randomFloat(2, 10, 500);
        $discount = $this->faker->randomFloat(2, 0, $total * 0.5);
        $final = $total - $discount;

        return [
            'user_id' => User::factory()->student(),
            'total_amount' => $total,
            'discount_amount' => $discount,
            'final_amount' => $final,
            'currency' => 'THB',
            'status' => 'pending',
            'payment_method' => null,
            'payment_ref' => null,
            'paid_at' => null,
        ];
    }

    public function paid(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
            'payment_method' => $this->faker->randomElement(['stripe', 'promptpay']),
            'payment_ref' => 'TXN-' . $this->faker->numerify('##########'),
            'paid_at' => now()->subDays($this->faker->numberBetween(1, 60)),
        ]);
    }
}
