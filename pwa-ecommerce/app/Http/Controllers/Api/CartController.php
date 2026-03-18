<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    private CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * GET /api/v1/cart
     */
    public function index(Request $request): JsonResponse
    {
        $cart = $this->cartService->resolveCartFromRequest($request);

        if (!$cart) {
            return response()->json([
                'status' => true,
                'message' => 'Giỏ hàng trống.',
                'data' => [
                    'items' => [],
                    'subtotal' => 0,
                    'total' => 0,
                    'items_count' => 0,
                ],
            ]);
        }

        $cart->load('items.service');

        return response()->json([
            'status' => true,
            'message' => 'Lấy Giỏ hàng thành công.',
            'data' => $this->cartService->formatCart($cart),
        ]);
    }

    /**
     * POST /api/v1/cart/items
     */
    public function addItem(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'service_id' => 'required|integer|exists:services,id',
            'quantity' => 'nullable|integer|min:1|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'data' => null,
                'errors' => $validator->errors(),
            ], 422);
        }

        $cart = $this->cartService->resolveOrCreateCartFromRequest($request);
        $quantity = $request->input('quantity', 1);

        $result = $this->cartService->addItem($cart, $request->service_id, $quantity);

        if (!$result['success']) {
            return response()->json([
                'status' => false,
                'message' => $result['message'],
                'data' => null,
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Thêm vào giỏ hàng thành công.',
            'data' => $result['data'],
        ], 201);
    }

    /**
     * PUT /api/v1/cart/items/{itemId}
     */
    public function updateItem(Request $request, int $itemId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'data' => null,
                'errors' => $validator->errors(),
            ], 422);
        }

        $cart = $this->cartService->resolveCartFromRequest($request);

        if (!$cart) {
            return response()->json([
                'status' => false,
                'message' => 'Giỏ hàng không tồn tại.',
                'data' => null,
            ], 404);
        }

        $result = $this->cartService->updateItemQuantity($cart, $itemId, $request->quantity);

        if (!$result['success']) {
            return response()->json([
                'status' => false,
                'message' => $result['message'],
                'data' => null,
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật giỏ hàng thành công.',
            'data' => $result['data'],
        ]);
    }

    /**
     * DELETE /api/v1/cart/items/{itemId}
     */
    public function removeItem(Request $request, int $itemId): JsonResponse
    {
        $cart = $this->cartService->resolveCartFromRequest($request);

        if (!$cart) {
            return response()->json([
                'status' => false,
                'message' => 'Giỏ hàng không tồn tại.',
                'data' => null,
            ], 404);
        }

        $result = $this->cartService->removeItem($cart, $itemId);

        if (!$result['success']) {
            return response()->json([
                'status' => false,
                'message' => $result['message'],
                'data' => null,
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Xoá khỏi giỏ hàng thành công.',
            'data' => $result['data'],
        ]);
    }

    /**
     * DELETE /api/v1/cart
     */
    public function clear(Request $request): JsonResponse
    {
        $cart = $this->cartService->resolveCartFromRequest($request);

        if (!$cart) {
            return response()->json([
                'status' => true,
                'message' => 'Giỏ hàng đã trống.',
                'data' => [
                    'items' => [],
                    'subtotal' => 0,
                    'total' => 0,
                    'items_count' => 0,
                ],
            ]);
        }

        $result = $this->cartService->clearCart($cart);

        return response()->json([
            'status' => true,
            'message' => 'Đã xoá toàn bộ giỏ hàng.',
            'data' => $result['data'],
        ]);
    }

    /**
     * GET /api/v1/cart/count
     */
    public function count(Request $request): JsonResponse
    {
        $cart = $this->cartService->resolveCartFromRequest($request);
        $count = $cart ? $this->cartService->getItemCount($cart) : 0;

        return response()->json([
            'status' => true,
            'data' => ['count' => (int) $count],
        ]);
    }
}
