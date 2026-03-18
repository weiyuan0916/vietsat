<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(2, true),
            'duration_days' => $this->faker->randomElement([30, 60, 90, 180]),
            'price' => $this->faker->randomElement([100000, 150000, 200000, 250000]),
            'is_active' => true,
        ];
    }
}
