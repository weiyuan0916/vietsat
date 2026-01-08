<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePcInfoRequest;
use App\Models\PcInfo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

/**
 * PcInfo API Controller
 *
 * Handles API endpoints for PC information management.
 */
class PcInfoController extends Controller
{
    /**
     * Store PC information with smart duplicate handling.
     *
     * Logic:
     * - If public_ip_address equals local_ip_address: Update existing record with same IP
     * - If public_ip_address differs from local_ip_address: Always create new record
     *
     * @param StorePcInfoRequest $request
     * @return JsonResponse
     */
    public function store(StorePcInfoRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $publicIp = $validatedData['public_ip_address'] ?? null;
            $localIp = $validatedData['local_ip_address'] ?? null;

            // Check if public IP equals local IP
            if ($publicIp && $localIp && $publicIp === $localIp) {
                // Same IP: Update existing record with this IP
                $existingPcInfo = PcInfo::where(function ($query) use ($publicIp) {
                    $query->where('public_ip_address', $publicIp)
                          ->orWhere('local_ip_address', $publicIp);
                })->first();

                if ($existingPcInfo) {
                    // Update existing record
                    $existingPcInfo->update($validatedData);
                    $pcInfo = $existingPcInfo;
                    $message = 'PC information updated successfully (same IP detected)';
                    $statusCode = 200; // Updated
                    $created = false;
                } else {
                    // No existing record with this IP, create new
                    $pcInfo = PcInfo::create($validatedData);
                    $message = 'PC information stored successfully';
                    $statusCode = 201; // Created
                    $created = true;
                }
            } else {
                // Different IPs or missing IPs: Always create new record
                $pcInfo = PcInfo::create($validatedData);
                $message = 'PC information stored successfully (new record)';
                $statusCode = 201; // Created
                $created = true;
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'pc_info' => $pcInfo,
                    'created' => $created,
                    'ip_comparison' => [
                        'public_ip' => $publicIp,
                        'local_ip' => $localIp,
                        'same_ip' => $publicIp === $localIp,
                    ],
                ],
            ], $statusCode);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to store PC information',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all PC information.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = PcInfo::query();

            // Apply filters
            if ($request->has('host_name') && $request->host_name) {
                $query->byHostName($request->host_name);
            }

            if ($request->has('user_name') && $request->user_name) {
                $query->byUserName($request->user_name);
            }

            if ($request->has('ip_address') && $request->ip_address) {
                $query->byIpAddress($request->ip_address);
            }

            // Apply sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortDirection = $request->get('sort_direction', 'desc');

            if (in_array($sortBy, ['host_name', 'user_name', 'local_ip_address', 'public_ip_address', 'created_at', 'updated_at'])) {
                $query->orderBy($sortBy, $sortDirection);
            }

            // Paginate results
            $perPage = $request->get('per_page', 15);
            $pcInfos = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => [
                    'pc_infos' => $pcInfos->items(),
                    'pagination' => [
                        'current_page' => $pcInfos->currentPage(),
                        'last_page' => $pcInfos->lastPage(),
                        'per_page' => $pcInfos->perPage(),
                        'total' => $pcInfos->total(),
                        'from' => $pcInfos->firstItem(),
                        'to' => $pcInfos->lastItem(),
                    ],
                ],
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve PC information',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get specific PC information.
     *
     * @param PcInfo $pcInfo
     * @return JsonResponse
     */
    public function show(PcInfo $pcInfo): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => [
                    'pc_info' => $pcInfo,
                ],
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve PC information',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update PC information.
     *
     * @param StorePcInfoRequest $request
     * @param PcInfo $pcInfo
     * @return JsonResponse
     */
    public function update(StorePcInfoRequest $request, PcInfo $pcInfo): JsonResponse
    {
        try {
            $pcInfo->update($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'PC information updated successfully',
                'data' => [
                    'pc_info' => $pcInfo,
                ],
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update PC information',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete PC information.
     *
     * @param PcInfo $pcInfo
     * @return JsonResponse
     */
    public function destroy(PcInfo $pcInfo): JsonResponse
    {
        try {
            $pcInfo->delete();

            return response()->json([
                'success' => true,
                'message' => 'PC information deleted successfully',
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete PC information',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get PC statistics.
     *
     * @return JsonResponse
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = [
                'total_pcs' => PcInfo::count(),
                'pcs_with_host_name' => PcInfo::whereNotNull('host_name')->count(),
                'pcs_with_user_name' => PcInfo::whereNotNull('user_name')->count(),
                'pcs_with_local_ip' => PcInfo::whereNotNull('local_ip_address')->count(),
                'pcs_with_public_ip' => PcInfo::whereNotNull('public_ip_address')->count(),
                'recent_pcs' => PcInfo::where('created_at', '>=', now()->subDays(7))->count(),
                'updated_today' => PcInfo::whereDate('updated_at', today())->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'statistics' => $stats,
                ],
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve statistics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
