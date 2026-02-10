<?php

namespace App\Http\Controllers\Api;

use App\Events\PaymentPending;
use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Create a new service order.
     *
     * POST /api/v1/orders
     *
     * Request Body:
     * {
     *   "facebook_profile_link": "https://facebook.com/..."
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
            'service_id' => ['required', 'exists:services,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Dữ liệu đầu vào không hợp lệ.',
                'data' => null,
                'errors' => $validator->errors(),
            ], 422);
        }

        $service = Service::find($request->service_id);

        if (! $service || ! $service->is_active) {
            return response()->json([
                'status' => false,
                'message' => 'Dịch vụ không hợp lệ hoặc đã ngừng hoạt động.',
                'data' => null,
            ], 404);
        }

        return DB::transaction(function () use ($request, $service) {
            // Delete all pending orders older than 5 minutes before creating new order
            ServiceOrder::where('status', ServiceOrder::STATUS_PENDING)
                ->where('expires_at', '<', now())
                ->delete();

            $order = ServiceOrder::create([
                'order_code' => 'ORDFB' . Str::upper(Str::random(10)),
                'service_id' => $service->id,
                'amount' => $service->price,
                'status' => ServiceOrder::STATUS_PENDING,
                'expires_at' => now()->addMinutes(5),
                'facebook_profile_link' => $request->facebook_profile_link,
            ]);

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
                        'id' => $service->id,
                        'name' => $service->name,
                        'duration_days' => $service->duration_days,
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
                'service' => [
                    'id' => $order->service->id,
                    'name' => $order->service->name,
                    'duration_days' => $order->service->duration_days,
                ],
            ],
        ]);
    }
}
