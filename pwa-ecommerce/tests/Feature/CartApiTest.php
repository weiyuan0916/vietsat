<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CartApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cart_persists_by_session_header(): void
    {
        $service = Service::factory()->create([
            'is_active' => true,
            'price' => 100000,
        ]);
        $sessionId = (string) Str::uuid();

        $this->postJson('/api/v1/cart/items', [
            'service_id' => $service->id,
            'quantity' => 1,
        ], [
            'X-Cart-Session' => $sessionId,
        ])->assertStatus(201)
            ->assertJsonPath('data.session_id', $sessionId);

        $this->postJson('/api/v1/cart/items', [
            'service_id' => $service->id,
            'quantity' => 2,
        ], [
            'X-Cart-Session' => $sessionId,
        ])->assertStatus(201);

        $this->getJson('/api/v1/cart', [
            'X-Cart-Session' => $sessionId,
        ])->assertOk()
            ->assertJsonPath('data.items_count', 3)
            ->assertJsonPath('data.items.0.service_id', $service->id)
            ->assertJsonPath('data.items.0.quantity', 3);

        $this->assertDatabaseHas('carts', [
            'session_id' => $sessionId,
            'user_id' => null,
        ]);
    }

    public function test_authenticated_cart_persists_for_user(): void
    {
        $user = User::factory()->create();
        $service = Service::factory()->create([
            'is_active' => true,
            'price' => 150000,
        ]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/cart/items', [
                'service_id' => $service->id,
                'quantity' => 1,
            ])
            ->assertStatus(201);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/cart/items', [
                'service_id' => $service->id,
                'quantity' => 1,
            ])
            ->assertStatus(201);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/cart')
            ->assertOk()
            ->assertJsonPath('data.items_count', 2)
            ->assertJsonPath('data.items.0.service_id', $service->id)
            ->assertJsonPath('data.items.0.quantity', 2);

        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id,
        ]);
    }
}
