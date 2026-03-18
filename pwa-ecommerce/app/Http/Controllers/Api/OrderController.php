<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Create a new service order.
     *
     * POST /api/v1/orders
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

        $userId = $request->user()?->id;

        $result = $this->orderService->createOrder(
            $request->facebook_profile_link,
            $request->service_id,
            $userId,
            $request
        );

        if (!$result['success']) {
            $status = $result['error'] === 'service_not_found' ? 404 : 400;
            return response()->json([
                'status' => false,
                'message' => $result['message'],
                'data' => null,
            ], $status);
        }

        return response()->json([
            'status' => true,
            'message' => 'Tạo đơn hàng thành công.',
            'data' => $result['data'],
        ], 201);
    }

    /**
     * Get order details and status.
     *
     * GET /api/v1/orders/{orderCode}
     */
    public function show(string $orderCode): JsonResponse
    {
        $order = $this->orderService->getOrder($orderCode);

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy đơn hàng.',
                'data' => null,
            ], 404);
        }

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

        $result = $this->orderService->verifyPayment(
            $request->order_code,
            $request->bank_txn_id
        );

        if (!$result['success']) {
            $status = match ($result['error']) {
                'order_not_found' => 404,
                'already_paid', 'order_expired' => 400,
                default => 400,
            };

            return response()->json([
                'status' => false,
                'message' => $result['message'],
                'data' => null,
            ], $status);
        }

        return response()->json([
            'status' => true,
            'message' => 'Xác nhận thanh toán thành công.',
            'data' => $result['data'],
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

        $orders = $this->orderService->getUserOrders($user->id);

        if (empty($orders)) {
            return response()->json([
                'status' => true,
                'message' => 'Không có đơn hàng.',
                'data' => [],
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Lấy danh sách đơn hàng thành công.',
            'data' => $orders,
        ]);
    }
}
