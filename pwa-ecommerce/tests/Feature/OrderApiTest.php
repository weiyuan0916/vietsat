<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\ServiceOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test can create order.
     */
    public function test_can_create_order(): void
    {
        $service = Service::factory()->create([
            'is_active' => true,
            'price' => 100000,
        ]);

        $response = $this->postJson('/api/v1/orders', [
            'facebook_profile_link' => 'https://facebook.com/testuser',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'order_code',
                    'amount',
                    'expires_at',
                    'qr_content',
                    'status',
                    'service',
                ],
            ]);

        $this->assertDatabaseHas('service_orders', [
            'facebook_profile_link' => 'https://facebook.com/testuser',
            'status' => 'pending',
        ]);
    }

    /**
     * Test order creation requires valid Facebook URL.
     */
    public function test_order_requires_valid_facebook_url(): void
    {
        Service::factory()->create([
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/v1/orders', [
            'facebook_profile_link' => 'invalid-url',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['facebook_profile_link']);
    }

    /**
     * Test can get order details.
     */
    public function test_can_get_order_details(): void
    {
        $order = ServiceOrder::factory()->create();

        $response = $this->getJson('/api/v1/orders/' . $order->order_code);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'data' => [
                    'order_code' => $order->order_code,
                ],
            ]);
    }

    /**
     * Test returns 404 for non-existent order.
     */
    public function test_returns_404_for_nonexistent_order(): void
    {
        $response = $this->getJson('/api/v1/orders/NONEXISTENT');

        $response->assertStatus(404);
    }

    /**
     * Test order is linked to user when authenticated.
     */
    public function test_order_linked_to_authenticated_user(): void
    {
        $user = User::factory()->create();
        $service = Service::factory()->create([
            'is_active' => true,
            'price' => 100000,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/orders', [
                'facebook_profile_link' => 'https://facebook.com/testuser',
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('service_orders', [
            'user_id' => $user->id,
            'facebook_profile_link' => 'https://facebook.com/testuser',
        ]);
    }

    /**
     * Test can get user's orders when authenticated.
     */
    public function test_can_get_user_orders(): void
    {
        $user = User::factory()->create();
        $order = ServiceOrder::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/orders/my-orders');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data',
            ]);
    }

    /**
     * Test cannot get user orders when unauthenticated.
     */
    public function test_cannot_get_user_orders_unauthenticated(): void
    {
        $response = $this->getJson('/api/v1/orders/my-orders');

        $response->assertStatus(401);
    }

    /**
     * Test returns empty orders list with friendly message.
     */
    public function test_returns_empty_orders_with_message_when_user_has_no_orders(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/orders/my-orders');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Không có đơn hàng.',
                'data' => [],
            ]);
    }

    /**
     * Test can verify payment.
     */
    public function test_can_verify_payment(): void
    {
        $order = ServiceOrder::factory()->create([
            'status' => 'pending',
        ]);

        $response = $this->postJson('/api/v1/orders/verify-payment', [
            'order_code' => $order->order_code,
            'bank_txn_id' => 'TPB123456',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Xác nhận thanh toán thành công.',
            ]);

        $this->assertDatabaseHas('service_orders', [
            'order_code' => $order->order_code,
            'status' => 'paid',
            'bank_txn_id' => 'TPB123456',
        ]);
    }

    /**
     * Test verify payment fails for already paid order.
     */
    public function test_verify_payment_fails_for_paid_order(): void
    {
        $order = ServiceOrder::factory()->create([
            'status' => 'paid',
        ]);

        $response = $this->postJson('/api/v1/orders/verify-payment', [
            'order_code' => $order->order_code,
            'bank_txn_id' => 'TPB123456',
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'status' => false,
                'message' => 'Đơn hàng đã được thanh toán.',
            ]);
    }
}

