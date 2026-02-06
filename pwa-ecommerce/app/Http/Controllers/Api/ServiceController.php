<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\JsonResponse;

class ServiceController extends Controller
{
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

        $services = Service::where('is_active', true)
            ->orderBy('id')
            ->paginate($perPage);

        return response()->json([
            'status' => true,
            'message' => 'Lấy danh sách dịch vụ thành công.',
            'data' => [
                'items' => $services->items(),
                'meta' => [
                    'current_page' => $services->currentPage(),
                    'last_page' => $services->lastPage(),
                    'per_page' => $services->perPage(),
                    'total' => $services->total(),
                    'from' => $services->firstItem(),
                    'to' => $services->lastItem(),
                ],
                'links' => [
                    'first' => $services->url(1),
                    'last' => $services->url($services->lastPage()),
                    'prev' => $services->previousPageUrl(),
                    'next' => $services->nextPageUrl(),
                ],
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
        $service = Service::where('is_active', true)
            ->orderBy('id')
            ->first();

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
                'id' => $service->id,
                'name' => $service->name,
                'duration_days' => $service->duration_days,
                'price' => $service->price,
                'formatted_price' => number_format($service->price) . ' VND',
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
        $service = Service::where('is_active', true)
            ->find($id);

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
            'data' => [
                'id' => $service->id,
                'name' => $service->name,
                'duration_days' => $service->duration_days,
                'price' => $service->price,
                'formatted_price' => number_format($service->price) . ' VND',
                'is_active' => $service->is_active,
                'created_at' => $service->created_at,
                'updated_at' => $service->updated_at,
            ],
        ]);
    }
}
