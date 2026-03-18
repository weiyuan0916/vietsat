<?php

namespace Tests\Feature;

use App\Events\PaymentExpired;
use App\Events\PaymentPending;
use App\Events\PaymentSuccess;
use App\Models\Service;
use App\Models\ServiceOrder;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_local_order(): void
    {
        $service = Service::factory()->create([
            'is_active' => true,
            'price' => 100000,
        ]);
        Event::fake([PaymentPending::class]);

        $response = $this->postJson('/api/v1/orders', [
            'facebook_profile_link' => 'https://facebook.com/testuser',
            'service_id' => $service->id,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'status' => true,
                'data' => [
                    'amount' => 100000,
                    'status' => ServiceOrder::STATUS_PENDING,
                    'service' => [
                        'id' => $service->id,
                        'name' => $service->name,
                        'duration_days' => $service->duration_days,
                    ],
                ],
            ]);

        $this->assertDatabaseHas('service_orders', [
            'service_id' => $service->id,
            'facebook_profile_link' => 'https://facebook.com/testuser',
            'status' => ServiceOrder::STATUS_PENDING,
        ]);

        Event::assertDispatched(PaymentPending::class);
    }

    public function test_can_create_external_order_when_external_service_mode_enabled(): void
    {
        config()->set('services.service.use_external_api', true);
        config()->set('services.external_api.base_url', 'https://external.test/api/v1');

        Http::fake([
            'https://external.test/api/v1/services*' => Http::response([
                'status' => true,
                'data' => [
                    'items' => [
                        [
                            'id' => 77,
                            'name' => 'External Plan',
                            'duration_days' => 30,
                            'price' => 125000,
                            'is_active' => true,
                        ],
                    ],
                ],
            ]),
        ]);
        Event::fake([PaymentPending::class]);

        $response = $this->postJson('/api/v1/orders', [
            'facebook_profile_link' => 'https://facebook.com/external-user',
            'service_id' => 77,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'status' => true,
                'data' => [
                    'amount' => 125000,
                    'service' => [
                        'id' => 77,
                        'name' => 'External Plan',
                        'duration_days' => 30,
                    ],
                ],
            ]);

        $order = ServiceOrder::query()->latest('id')->first();

        $this->assertNotNull($order);
        $this->assertNull($order->service_id);
        $this->assertSame('External Plan', $order->service_data['name']);
        $this->assertSame(125000, $order->amount);
        Event::assertDispatched(PaymentPending::class);
    }

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

    public function test_returns_404_for_nonexistent_order(): void
    {
        $response = $this->getJson('/api/v1/orders/NONEXISTENT');

        $response->assertStatus(404);
    }

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
                'service_id' => $service->id,
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('service_orders', [
            'user_id' => $user->id,
            'facebook_profile_link' => 'https://facebook.com/testuser',
        ]);
    }

    public function test_can_get_user_orders(): void
    {
        $user = User::factory()->create();
        $currentUserOrder = ServiceOrder::factory()->forUser($user)->create();
        ServiceOrder::factory()->forUser(User::factory()->create())->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/orders/my-orders');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data',
            ])
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.order_code', $currentUserOrder->order_code);
    }

    public function test_cannot_get_user_orders_unauthenticated(): void
    {
        $response = $this->getJson('/api/v1/orders/my-orders');

        $response->assertStatus(401);
    }

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

    public function test_can_verify_payment(): void
    {
        $order = ServiceOrder::factory()->create([
            'status' => ServiceOrder::STATUS_PENDING,
        ]);
        Event::fake([PaymentSuccess::class]);

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
            'status' => ServiceOrder::STATUS_PAID,
            'bank_txn_id' => 'TPB123456',
        ]);

        Event::assertDispatched(PaymentSuccess::class);
    }

    public function test_verify_payment_fails_for_paid_order(): void
    {
        $order = ServiceOrder::factory()->paid()->create();

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

    public function test_show_marks_expired_pending_order_and_broadcasts_event(): void
    {
        $order = ServiceOrder::factory()->expired()->create();
        Event::fake([PaymentExpired::class]);

        $response = $this->getJson('/api/v1/orders/' . $order->order_code);

        $response->assertOk()
            ->assertJson([
                'status' => true,
                'data' => [
                    'order_code' => $order->order_code,
                    'status' => ServiceOrder::STATUS_EXPIRED,
                ],
            ]);

        $this->assertDatabaseHas('service_orders', [
            'id' => $order->id,
            'status' => ServiceOrder::STATUS_EXPIRED,
        ]);

        Event::assertDispatched(PaymentExpired::class);
    }
}

