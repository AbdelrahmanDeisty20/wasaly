<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->word(),
            'address' => fake()->address(),
            'is_default' => fake()->boolean(20),
        ];
    }
}
