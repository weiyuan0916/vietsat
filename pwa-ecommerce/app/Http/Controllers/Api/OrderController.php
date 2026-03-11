<?php

namespace App\Http\Controllers\Api;

use App\Events\PaymentPending;
use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceOrder;
use App\Services\ExternalServiceApi;
use App\Services\DeviceTrackingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    private ExternalServiceApi $externalServiceApi;
    private DeviceTrackingService $deviceTrackingService;
    private bool $useExternalApi;

    public function __construct()
    {
        $this->externalServiceApi = new ExternalServiceApi();
        $this->deviceTrackingService = new DeviceTrackingService();
        $this->useExternalApi = config('services.service.use_external_api', true);
    }

    /**
     * Create a new service order.
     *
     * POST /api/v1/orders
     *
     * Request Body:
     * {
     *   "facebook_profile_link": "https://facebook.com/...",
     *   "service_id": 1 (optional - if not provided, uses default service)
     * }
     *
     * Response (201) - Success:
     * {
     *   "status": true,
     *   "message": "Tạo đơn hàng thành công.",
     *   "data": {
     *     "order_code": "ORD-XXXXXXXXXX",
     *     "amount": 100000,
     *     "expires_at": "2026-01-31T10:05:00Z",
     *     "qr_content": "bank:ORD-XXXXXXXXXX:100000",
     *     "status": "pending",
     *     "service": {
     *       "id": 1,
     *       "name": "Default Plan",
     *       "duration_days": 90
     *     }
     *   }
     * }
     *
     * Response (422) - Validation failed:
     * {
     *   "status": false,
     *   "message": "Dữ liệu đầu vào không hợp lệ.",
     *   "data": null,
     *   "errors": {
     *     "facebook_profile_link": ["The facebook profile link field is required."]
     *   }
     * }
     *
     * Response (404) - No active service:
     * {
     *   "status": false,
     *   "message": "Không tìm thấy dịch vụ hoạt động.",
     *   "data": null
     * }
     */
    public function store(Request $request): JsonResponse
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'facebook_profile_link' => ['required', 'url', 'regex:/facebook\.com/'],
            'service_id' => ['nullable', 'integer'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Dữ liệu đầu vào không hợp lệ.',
                'data' => null,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Get service from external API or local database
        $serviceData = null;
        $localServiceId = null;

        if ($this->useExternalApi) {
            // Try to get from external API
            if ($request->service_id) {
                $serviceData = $this->externalServiceApi->getServiceById($request->service_id);
            } else {
                $serviceData = $this->externalServiceApi->getDefaultService();
            }

            if (!$serviceData) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy dịch vụ hoạt động.',
                    'data' => null,
                ], 404);
            }
        } else {
            // Use local database
            $serviceId = $request->service_id;
            
            // If no service_id provided, get default service
            if (!$serviceId) {
                $localService = Service::where('is_active', true)->first();
            } else {
                $localService = Service::find($serviceId);
            }

            if (!$localService || !$localService->is_active) {
                return response()->json([
                    'status' => false,
                    'message' => 'Dịch vụ không hợp lệ hoặc đã ngừng hoạt động.',
                    'data' => null,
                ], 404);
            }

            $localServiceId = $localService->id;
            $serviceData = [
                'id' => $localService->id,
                'name' => $localService->name,
                'duration_days' => $localService->duration_days,
                'price' => $localService->price,
            ];
        }

        $amount = $serviceData['price'];

        return DB::transaction(function () use ($request, $serviceData, $localServiceId, $amount) {
            // Delete all pending orders older than 5 minutes before creating new order
            ServiceOrder::where('status', ServiceOrder::STATUS_PENDING)
                ->where('expires_at', '<', now())
                ->delete();

            // Generate device fingerprint
            $deviceFingerprint = $this->deviceTrackingService->generateFingerprint($request);

            // Prepare order data
            $orderData = [
                'order_code' => 'ORDFB' . Str::upper(Str::random(10)),
                'amount' => $amount,
                'status' => ServiceOrder::STATUS_PENDING,
                'expires_at' => now()->addMinutes(5),
                'facebook_profile_link' => $request->facebook_profile_link,
                'device_fingerprint' => $deviceFingerprint,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ];

            // Add user_id if authenticated
            if ($request->user()) {
                $orderData['user_id'] = $request->user()->id;
            }

            // Add service data based on API source
            if ($this->useExternalApi) {
                // Store external service data as JSON
                $orderData['service_data'] = $serviceData;
                $orderData['service_id'] = null; // No local service
            } else {
                // Use local service
                $orderData['service_id'] = $localServiceId;
            }

            $order = ServiceOrder::create($orderData);

            event(new PaymentPending($order));

            return response()->json([
                'status' => true,
                'message' => 'Tạo đơn hàng thành công.',
                'data' => [
                    'order_code' => $order->order_code,
                    'amount' => $order->amount,
                    'expires_at' => $order->expires_at->toIso8601String(),
                    'qr_content' => 'bank:' . $order->order_code . ':' . $order->amount,
                    'status' => $order->status,
                    'service' => [
                        'id' => $serviceData['id'],
                        'name' => $serviceData['name'],
                        'duration_days' => $serviceData['duration_days'],
                    ],
                ],
            ], 201);
        });
    }

    /**
     * Get order details and status.
     *
     * GET /api/v1/orders/{orderCode}
     *
     * Response (200) - Pending order:
     * {
     *   "status": true,
     *   "message": "Lấy thông tin đơn hàng thành công.",
     *   "data": {
     *     "order_code": "ORD-XXXXXXXXXX",
     *     "amount": 100000,
     *     "status": "pending",
     *     "expires_at": "2026-01-31T10:05:00Z",
     *     "paid_at": null,
     *     "created_at": "2026-01-31T10:00:00Z",
     *     "service": {
     *       "id": 1,
     *       "name": "Default Plan",
     *       "duration_days": 90
     *     }
     *   }
     * }
     *
     * Response (200) - Paid order:
     * {
     *   "status": true,
     *   "message": "Lấy thông tin đơn hàng thành công.",
     *   "data": {
     *     "order_code": "ORD-XXXXXXXXXX",
     *     "amount": 100000,
     *     "status": "paid",
     *     "expires_at": "2026-01-31T10:05:00Z",
     *     "paid_at": "2026-01-31T10:02:00Z",
     *     "created_at": "2026-01-31T10:00:00Z",
     *     "service": {
     *       "id": 1,
     *       "name": "Default Plan",
     *       "duration_days": 90
     *     }
     *   }
     * }
     *
     * Response (404) - Order not found:
     * {
     *   "status": false,
     *   "message": "Không tìm thấy đơn hàng.",
     *   "data": null
     * }
     */
    public function show(string $orderCode): JsonResponse
    {
        $order = ServiceOrder::where('order_code', $orderCode)->first();

        if (! $order) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy đơn hàng.',
                'data' => null,
            ], 404);
        }

        // Get service info (from external or local)
        $serviceInfo = $order->getServiceInfo();

        return response()->json([
            'status' => true,
            'message' => 'Lấy thông tin đơn hàng thành công.',
            'data' => [
                'order_code' => $order->order_code,
                'amount' => $order->amount,
                'status' => $order->status,
                'expires_at' => $order->expires_at->toIso8601String(),
                'paid_at' => $order->paid_at?->toIso8601String(),
                'created_at' => $order->created_at->toIso8601String(),
                'service' => $serviceInfo,
            ],
        ]);
    }

    /**
     * Verify payment from TPBank webhook.
     *
     * POST /api/v1/orders/verify-payment
     */
    public function verifyPayment(Request $request): JsonResponse
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'order_code' => 'required|string',
            'bank_txn_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Dữ liệu đầu vào không hợp lệ.',
                'data' => null,
                'errors' => $validator->errors(),
            ], 422);
        }

        $order = ServiceOrder::where('order_code', $request->order_code)->first();

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy đơn hàng.',
                'data' => null,
            ], 404);
        }

        // Check if order is already paid
        if ($order->status === ServiceOrder::STATUS_PAID) {
            return response()->json([
                'status' => false,
                'message' => 'Đơn hàng đã được thanh toán.',
                'data' => null,
            ], 400);
        }

        // Check if order is expired
        if ($order->isTimeExpired()) {
            $order->update(['status' => ServiceOrder::STATUS_EXPIRED]);
            return response()->json([
                'status' => false,
                'message' => 'Đơn hàng đã hết hạn.',
                'data' => null,
            ], 400);
        }

        // Verify device fingerprint (security check)
        if (!$this->deviceTrackingService->verifyDevice($request, $order)) {
            \Log::warning('[Payment] Device verification failed', [
                'order_code' => $order->order_code,
                'request_ip' => $request->ip(),
                'order_ip' => $order->ip_address,
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Thiết bị không khớp với đơn hàng.',
                'data' => null,
            ], 403);
        }

        // Update order as paid
        $order->update([
            'status' => ServiceOrder::STATUS_PAID,
            'paid_at' => now(),
            'bank_txn_id' => $request->bank_txn_id,
        ]);

        // Trigger payment success event
        event(new \App\Events\PaymentSuccess($order));

        return response()->json([
            'status' => true,
            'message' => 'Xác nhận thanh toán thành công.',
            'data' => [
                'order_code' => $order->order_code,
                'status' => $order->status,
                'paid_at' => $order->paid_at->toIso8601String(),
            ],
        ]);
    }

    /**
     * Get user's orders.
     *
     * GET /api/v1/orders/my-orders
     */
    public function myOrders(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Vui lòng đăng nhập.',
                'data' => null,
            ], 401);
        }

        $orders = ServiceOrder::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Lấy danh sách đơn hàng thành công.',
            'data' => $orders->map(function ($order) {
                return [
                    'order_code' => $order->order_code,
                    'amount' => $order->amount,
                    'status' => $order->status,
                    'facebook_profile_link' => $order->facebook_profile_link,
                    'expires_at' => $order->expires_at?->toIso8601String(),
                    'paid_at' => $order->paid_at?->toIso8601String(),
                    'created_at' => $order->created_at->toIso8601String(),
                    'service' => $order->getServiceInfo(),
                ];
            }),
        ]);
    }
}
