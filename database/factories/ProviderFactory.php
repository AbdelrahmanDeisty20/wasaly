<?php

namespace Database\Factories;

use App\Models\Provider;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProviderFactory extends Factory
{
    protected $model = Provider::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state(['type' => 'service_provider']),
            'title_ar' => fake('ar_SA')->company(),
            'title_en' => fake()->company(),
            'service_description_ar' => fake('ar_SA')->sentence(),
            'service_description_en' => fake()->sentence(),
            'phone' => fake()->phoneNumber(),
            'price' => fake()->numberBetween(50, 500),
            'from_day' => 'Saturday',
            'to_day' => 'Thursday',
            'start_time' => '09:00:00',
            'end_time' => '21:00:00',
            'status' => 'active',
        ];
    }
}
