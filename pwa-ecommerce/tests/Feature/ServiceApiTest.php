<?php

namespace Tests\Feature;

use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test can get all services.
     */
    public function test_can_get_all_services(): void
    {
        Service::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/services');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data',
            ]);
    }

    /**
     * Test can get default service.
     */
    public function test_can_get_default_service(): void
    {
        Service::factory()->create([
            'name' => 'Default Plan',
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/v1/services/default');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'name',
                    'duration_days',
                    'price',
                ],
            ]);
    }

    /**
     * Test returns 404 when no active service.
     */
    public function test_returns_404_when_no_active_service(): void
    {
        $response = $this->getJson('/api/v1/services/default');

        $response->assertStatus(404);
    }

    /**
     * Test can get service by ID.
     */
    public function test_can_get_service_by_id(): void
    {
        $service = Service::factory()->create();

        $response = $this->getJson('/api/v1/services/' . $service->id);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'data' => [
                    'id' => $service->id,
                    'name' => $service->name,
                ],
            ]);
    }
}

