<?php

namespace Database\Factories;

use App\Models\AppNotification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppNotificationFactory extends Factory
{
    protected $model = AppNotification::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(),
            'message' => fake()->sentence(),
            'type' => 'general',
            'data' => json_encode(['foo' => 'bar']),
            'is_read' => fake()->boolean(30),
        ];
    }
}
