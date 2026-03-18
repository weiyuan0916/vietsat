<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    /**
     * GET /api/v1/cart
     */
    public function index(Request $request): JsonResponse
    {
        $cart = $this->resolveCart($request);

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
            'message' => 'Lấy giỏ hàng thành công.',
            'data' => $this->formatCart($cart),
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
                'errors' => $validator->errors(),
            ], 422);
        }

        $service = Service::where('id', $request->service_id)
            ->where('is_active', true)
            ->first();

        if (!$service) {
            return response()->json([
                'status' => false,
                'message' => 'Dịch vụ không tồn tại hoặc đã ngừng hoạt động.',
            ], 404);
        }

        $cart = $this->resolveOrCreateCart($request);
        $quantity = $request->input('quantity', 1);

        $existingItem = CartItem::where('cart_id', $cart->id)
            ->where('service_id', $service->id)
            ->first();

        if ($existingItem) {
            $existingItem->quantity += $quantity;
            $existingItem->subtotal = $existingItem->quantity * $existingItem->price;
            $existingItem->save();
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'service_id' => $service->id,
                'quantity' => $quantity,
                'price' => $service->price,
                'subtotal' => $quantity * $service->price,
            ]);
        }

        $cart->calculateTotals();
        $cart->load('items.service');

        return response()->json([
            'status' => true,
            'message' => 'Thêm vào giỏ hàng thành công.',
            'data' => $this->formatCart($cart),
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
                'errors' => $validator->errors(),
            ], 422);
        }

        $cart = $this->resolveCart($request);
        if (!$cart) {
            return response()->json(['status' => false, 'message' => 'Giỏ hàng không tồn tại.'], 404);
        }

        $item = CartItem::where('id', $itemId)->where('cart_id', $cart->id)->first();
        if (!$item) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy sản phẩm trong giỏ.'], 404);
        }

        $item->quantity = $request->quantity;
        $item->subtotal = $item->quantity * $item->price;
        $item->save();

        $cart->calculateTotals();
        $cart->load('items.service');

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật giỏ hàng thành công.',
            'data' => $this->formatCart($cart),
        ]);
    }

    /**
     * DELETE /api/v1/cart/items/{itemId}
     */
    public function removeItem(Request $request, int $itemId): JsonResponse
    {
        $cart = $this->resolveCart($request);
        if (!$cart) {
            return response()->json(['status' => false, 'message' => 'Giỏ hàng không tồn tại.'], 404);
        }

        $item = CartItem::where('id', $itemId)->where('cart_id', $cart->id)->first();
        if (!$item) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy sản phẩm trong giỏ.'], 404);
        }

        $item->delete();
        $cart->calculateTotals();
        $cart->load('items.service');

        return response()->json([
            'status' => true,
            'message' => 'Xoá khỏi giỏ hàng thành công.',
            'data' => $this->formatCart($cart),
        ]);
    }

    /**
     * DELETE /api/v1/cart
     */
    public function clear(Request $request): JsonResponse
    {
        $cart = $this->resolveCart($request);
        if (!$cart) {
            return response()->json(['status' => true, 'message' => 'Giỏ hàng đã trống.']);
        }

        CartItem::where('cart_id', $cart->id)->delete();
        $cart->update(['subtotal' => 0, 'total' => 0, 'tax' => 0, 'discount' => 0, 'shipping' => 0]);

        return response()->json([
            'status' => true,
            'message' => 'Đã xoá toàn bộ giỏ hàng.',
            'data' => $this->formatCart($cart),
        ]);
    }

    /**
     * GET /api/v1/cart/count
     */
    public function count(Request $request): JsonResponse
    {
        $cart = $this->resolveCart($request);
        $count = $cart ? CartItem::where('cart_id', $cart->id)->sum('quantity') : 0;

        return response()->json([
            'status' => true,
            'data' => ['count' => (int) $count],
        ]);
    }

    // ── Helpers ───────────────────────────────────────────

    private function resolveCart(Request $request): ?Cart
    {
        if ($request->user()) {
            return Cart::where('user_id', $request->user()->id)->first();
        }

        $sessionId = $request->header('X-Cart-Session') ?? $request->cookie('cart_session');
        if ($sessionId) {
            return Cart::where('session_id', $sessionId)->first();
        }

        return null;
    }

    private function resolveOrCreateCart(Request $request): Cart
    {
        $cart = $this->resolveCart($request);
        if ($cart) {
            return $cart;
        }

        $sessionId = $request->header('X-Cart-Session') ?? $request->cookie('cart_session') ?? \Illuminate\Support\Str::uuid()->toString();

        return Cart::create([
            'user_id' => $request->user()?->id,
            'session_id' => $sessionId,
            'subtotal' => 0,
            'tax' => 0,
            'shipping' => 0,
            'discount' => 0,
            'total' => 0,
        ]);
    }

    private function formatCart(Cart $cart): array
    {
        $items = $cart->items->map(function (CartItem $item) {
            $service = $item->service;
            $serviceName = $service ? $service->name : 'Dịch vụ đã xoá';

            return [
                'id' => $item->id,
                'service_id' => $item->service_id,
                'name' => $serviceName,
                'service_name' => $serviceName,
                'duration_days' => $service ? $service->duration_days : null,
                'quantity' => $item->quantity,
                'price' => (int) $item->price,
                'subtotal' => (int) $item->subtotal,
            ];
        });

        return [
            'cart_id' => $cart->id,
            'session_id' => $cart->session_id,
            'items' => $items,
            'subtotal' => (int) $cart->subtotal,
            'total' => (int) $cart->total,
            'items_count' => $cart->items->sum('quantity'),
        ];
    }
}
