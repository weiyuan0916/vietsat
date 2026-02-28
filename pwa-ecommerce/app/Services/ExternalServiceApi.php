<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExternalServiceApi
{
    private string $baseUrl;
    private int $cacheTtl;

    public function __construct()
    {
        $this->baseUrl = config('services.external_api.base_url', 'https://tiemnhaduy.com/api/v1');
        $this->cacheTtl = config('services.external_api.cache_ttl', 300); // 5 minutes default
    }

    /**
     * Get all services from external API with pagination.
     *
     * @param int $page
     * @param int $perPage
     * @return array|null
     */
    public function getServices(int $page = 1, int $perPage = 10): ?array
    {
        $cacheKey = "external_services_page_{$page}_per_{$perPage}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($page, $perPage) {
            try {
                $response = Http::timeout(30)
                    ->get("{$this->baseUrl}/services", [
                        'page' => $page,
                        'per_page' => $perPage,
                    ]);

                if ($response->successful()) {
                    $data = $response->json();

                    // Transform to match our API format
                    return $this->transformServicesResponse($data);
                }

                Log::warning('External service API failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return null;
            } catch (\Exception $e) {
                Log::error('External service API error', [
                    'message' => $e->getMessage(),
                ]);

                return null;
            }
        });
    }

    /**
     * Get the default service from external API.
     *
     * @return array|null
     */
    public function getDefaultService(): ?array
    {
        $cacheKey = 'external_service_default';

        return Cache::remember($cacheKey, $this->cacheTtl, function () {
            try {
                // Fetch first page and get first active service
                $response = Http::timeout(30)
                    ->get("{$this->baseUrl}/services", [
                        'page' => 1,
                        'per_page' => 100,
                    ]);

                if ($response->successful()) {
                    $data = $response->json();

                    if (!empty($data['data']['items'])) {
                        // Get first active service as default
                        foreach ($data['data']['items'] as $service) {
                            if ($service['is_active'] ?? false) {
                                return $this->transformServiceItem($service);
                            }
                        }
                    }
                }

                return null;
            } catch (\Exception $e) {
                Log::error('External service API default error', [
                    'message' => $e->getMessage(),
                ]);

                return null;
            }
        });
    }

    /**
     * Get a specific service by ID.
     *
     * @param int $id
     * @return array|null
     */
    public function getServiceById(int $id): ?array
    {
        $cacheKey = "external_service_{$id}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($id) {
            try {
                // Since external API doesn't have get-by-id endpoint,
                // we fetch all and filter
                $response = Http::timeout(30)
                    ->get("{$this->baseUrl}/services", [
                        'page' => 1,
                        'per_page' => 100,
                    ]);

                if ($response->successful()) {
                    $data = $response->json();

                    if (!empty($data['data']['items'])) {
                        foreach ($data['data']['items'] as $service) {
                            if ($service['id'] === $id) {
                                return $this->transformServiceItem($service);
                            }
                        }
                    }
                }

                return null;
            } catch (\Exception $e) {
                Log::error('External service API get by ID error', [
                    'message' => $e->getMessage(),
                    'service_id' => $id,
                ]);

                return null;
            }
        });
    }

    /**
     * Transform external API response to our API format.
     *
     * @param array $data
     * @return array
     */
    private function transformServicesResponse(array $data): array
    {
        $items = [];
        $rawItems = $data['data']['items'] ?? [];
        $meta = $data['data']['meta'] ?? [];
        $links = $data['data']['links'] ?? [];

        foreach ($rawItems as $service) {
            $items[] = $this->transformServiceItem($service);
        }

        return [
            'items' => $items,
            'meta' => [
                'current_page' => $meta['current_page'] ?? 1,
                'last_page' => $meta['last_page'] ?? 1,
                'per_page' => $meta['per_page'] ?? 10,
                'total' => $meta['total'] ?? count($items),
                'from' => $meta['from'] ?? 1,
                'to' => $meta['to'] ?? count($items),
            ],
            'links' => [
                'first' => $links['first'] ?? null,
                'last' => $links['last'] ?? null,
                'prev' => $links['prev'] ?? null,
                'next' => $links['next'] ?? null,
            ],
        ];
    }

    /**
     * Transform a single service item to our format.
     *
     * @param array $service
     * @return array
     */
    private function transformServiceItem(array $service): array
    {
        return [
            'id' => $service['id'],
            'name' => $service['name'],
            'duration_days' => $service['duration_days'],
            'price' => $service['price'],
            'formatted_price' => number_format($service['price']) . ' VND',
            'is_active' => $service['is_active'],
            'created_at' => $service['created_at'] ?? now()->toIso8601String(),
            'updated_at' => $service['updated_at'] ?? now()->toIso8601String(),
        ];
    }

    /**
     * Clear cache for services.
     *
     * @return void
     */
    public function clearCache(): void
    {
        Cache::forget('external_service_default');
        // Clear paginated cache (simplified - clear all)
        Cache::flush();
    }
}



