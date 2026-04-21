<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\Provider;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        return [
            'provider_id' => Provider::factory(),
            'service' => fake()->word(),
        ];
    }
}
