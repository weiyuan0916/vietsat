<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ExternalServiceApi;
use Illuminate\Http\JsonResponse;

class ServiceController extends Controller
{
    private ExternalServiceApi $externalServiceApi;
    private bool $useExternalApi;

    public function __construct()
    {
        $this->externalServiceApi = new ExternalServiceApi();
        $this->useExternalApi = config('services.service.use_external_api', true);
    }

    /**
     * Get all services with pagination.
     *
     * GET /api/v1/services
     *
     * Query Parameters:
     * - page: Trang hiện tại (mặc định: 1)
     * - per_page: Số item mỗi trang (mặc định: 10, tối đa: 100)
     *
     * Response:
     * {
     *   "status": true,
     *   "message": "Lấy danh sách dịch vụ thành công.",
     *   "data": {
     *     "items": [...],
     *     "meta": {...},
     *     "links": {...}
     *   }
     * }
     */
    public function index(): JsonResponse
    {
        $perPage = min((int) request('per_page', 10), 100);
        $page = (int) request('page', 1);

        // Use external API
        $services = $this->externalServiceApi->getServices($page, $perPage);

        if ($services === null) {
            return response()->json([
                'status' => false,
                'message' => 'Không thể kết nối đến dịch vụ.',
                'data' => null,
            ], 503);
        }

        return response()->json([
            'status' => true,
            'message' => 'Lấy danh sách dịch vụ thành công.',
            'data' => [
                'items' => $services['items'],
                'meta' => $services['meta'],
                'links' => $services['links'],
            ],
        ]);
    }

    /**
     * Get the default service plan.
     *
     * GET /api/v1/services/default
     *
     * Response (200):
     * {
     *   "status": true,
     *   "message": "Lấy thông tin dịch vụ thành công.",
     *   "data": {
     *     "id": 1,
     *     "name": "Default Plan",
     *     "duration_days": 90,
     *     "price": 100000,
     *     "formatted_price": "100,000 VND"
     *   }
     * }
     *
     * Response (404):
     * {
     *   "status": false,
     *   "message": "Không tìm thấy dịch vụ hoạt động.",
     *   "data": null
     * }
     */
    public function default(): JsonResponse
    {
        $service = $this->externalServiceApi->getDefaultService();

        if (! $service) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy dịch vụ hoạt động.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Lấy thông tin dịch vụ thành công.',
            'data' => [
                'id' => $service['id'],
                'name' => $service['name'],
                'duration_days' => $service['duration_days'],
                'price' => $service['price'],
                'formatted_price' => $service['formatted_price'],
            ],
        ]);
    }

    /**
     * Get a specific service by ID.
     *
     * GET /api/v1/services/{id}
     *
     * Response (200):
     * {
     *   "status": true,
     *   "message": "Lấy thông tin dịch vụ thành công.",
     *   "data": {
     *     "id": 1,
     *     "name": "Default Plan",
     *     "duration_days": 90,
     *     "price": 100000,
     *     "formatted_price": "100,000 VND",
     *     "is_active": true,
     *     "created_at": "2026-01-30T10:00:00Z",
     *     "updated_at": "2026-01-30T10:00:00Z"
     *   }
     * }
     *
     * Response (404):
     * {
     *   "status": false,
     *   "message": "Không tìm thấy dịch vụ.",
     *   "data": null
     * }
     */
    public function show(int $id): JsonResponse
    {
        $service = $this->externalServiceApi->getServiceById($id);

        if (! $service) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy dịch vụ.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Lấy thông tin dịch vụ thành công.',
            'data' => $service,
        ]);
    }
}
