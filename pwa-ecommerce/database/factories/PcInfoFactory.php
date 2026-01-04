<?php

namespace Database\Factories;

use App\Models\PcInfo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * PcInfo Factory
 *
 * Generates fake PC information data for testing
 */
class PcInfoFactory extends Factory
{
    protected $model = PcInfo::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $hostNames = [
            'DESKTOP-' . strtoupper($this->faker->lexify('???????')),
            'LAPTOP-' . strtoupper($this->faker->lexify('???????')),
            'WORKSTATION-' . strtoupper($this->faker->lexify('???????')),
            'PC-' . strtoupper($this->faker->lexify('???????')),
        ];

        return [
            'host_name' => $this->faker->randomElement($hostNames),
            'user_name' => $this->faker->userName(),
            'password' => $this->faker->password(),
            'local_ip_address' => $this->faker->localIpv4(),
            'public_ip_address' => $this->faker->ipv4(),
            'created_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
        ];
    }

    /**
     * Indicate that the PC info has minimal data.
     */
    public function minimal(): static
    {
        return $this->state(fn (array $attributes) => [
            'host_name' => null,
            'user_name' => null,
            'password' => null,
            'local_ip_address' => $this->faker->localIpv4(),
            'public_ip_address' => null,
        ]);
    }

    /**
     * Indicate that the PC info is from a recent connection.
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'created_at' => $this->faker->dateTimeBetween('-1 day', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 hour', 'now'),
        ]);
    }

    /**
     * Indicate that the PC info has complete data.
     */
    public function complete(): static
    {
        return $this->state(fn (array $attributes) => [
            'host_name' => 'DESKTOP-' . strtoupper($this->faker->lexify('???????')),
            'user_name' => $this->faker->userName(),
            'password' => $this->faker->password(),
            'local_ip_address' => $this->faker->localIpv4(),
            'public_ip_address' => $this->faker->ipv4(),
        ]);
    }
}


