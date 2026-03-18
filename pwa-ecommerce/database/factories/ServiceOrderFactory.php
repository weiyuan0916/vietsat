<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\ServiceOrder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ServiceOrderFactory extends Factory
{
    protected $model = ServiceOrder::class;

    public function definition(): array
    {
        $createdAt = now();
        $expiresAt = $createdAt->copy()->addMinutes(5);
        $status = ServiceOrder::STATUS_PENDING;

        return [
            'order_code' => ServiceOrder::generateOrderCode(),
            'service_id' => Service::factory(),
            'user_id' => null,
            'service_data' => null,
            'amount' => 100000,
            'status' => $status,
            'expires_at' => $expiresAt,
            'paid_at' => null,
            'bank_txn_id' => null,
            'facebook_profile_link' => 'https://facebook.com/' . $this->faker->userName(),
            'device_fingerprint' => hash('sha256', $this->faker->uuid()),
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
            'processing_started_at' => null,
            'processing_completed_at' => null,
            'extension_result' => null,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ];
    }

    public function paid(): static
    {
        return $this->state(function () {
            $paidAt = now();

            return [
                'status' => ServiceOrder::STATUS_PAID,
                'paid_at' => $paidAt,
                'bank_txn_id' => 'TXN-' . strtoupper(fake()->bothify('########')),
                'expires_at' => $paidAt->copy()->addMinutes(5),
            ];
        });
    }

    public function expired(): static
    {
        return $this->state(function () {
            $expiresAt = now()->subMinute();

            return [
                'status' => ServiceOrder::STATUS_PENDING,
                'expires_at' => $expiresAt,
            ];
        });
    }

    public function forUser(?User $user = null): static
    {
        return $this->state(fn () => [
            'user_id' => $user?->id ?? User::factory(),
        ]);
    }

    public function externalService(array $serviceData = []): static
    {
        return $this->state(function () use ($serviceData) {
            $payload = array_merge([
                'id' => 999,
                'name' => 'External Service',
                'duration_days' => 90,
                'price' => 100000,
            ], $serviceData);

            return [
                'service_id' => null,
                'service_data' => $payload,
                'amount' => $payload['price'],
            ];
        });
    }
}
