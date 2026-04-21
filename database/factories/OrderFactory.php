<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'address_id' => Address::factory(),
            'unit_price' => 0, // Will be updated by items
            'quantity' => 0, // Will be updated by items
            'total_price' => 0, // Will be updated by items
            'customer_name' => fake()->name(),
            'customer_phone' => fake()->phoneNumber(),
            'customer_address' => fake()->address(),
            'payment_method' => fake()->randomElement(['cash', 'card']),
            'status' => fake()->randomElement(['pending', 'accepted', 'processing', 'shipped', 'delivered', 'cancelled']),
        ];
    }
}
