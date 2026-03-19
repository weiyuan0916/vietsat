<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CartService
{
    public function getCart(?int $userId, ?string $sessionId): ?Cart
    {
        if ($userId) {
            return Cart::where('user_id', $userId)->first();
        }

        if ($sessionId) {
            return Cart::where('session_id', $sessionId)->first();
        }

        return null;
    }

    public function getOrCreateCart(?int $userId, ?string $sessionId): Cart
    {
        $cart = $this->getCart($userId, $sessionId);

        if ($cart) {
            return $cart;
        }

        $newSessionId = $sessionId ?? Str::uuid()->toString();

        return Cart::create([
            'user_id' => $userId,
            'session_id' => $newSessionId,
            'subtotal' => 0,
            'tax' => 0,
            'shipping' => 0,
            'discount' => 0,
            'total' => 0,
        ]);
    }

    public function addItem(Cart $cart, int $serviceId, int $quantity = 1): array
    {
        try {
            $result = DB::transaction(function () use ($cart, $serviceId, $quantity) {
                $service = Service::where('id', $serviceId)
                    ->where('is_active', true)
                    ->lockForUpdate()
                    ->first();

                if (!$service) {
                    return [
                        'success' => false,
                        'error' => 'service_not_found',
                        'message' => 'Dịch vụ không tồn tại hoặc đã ngừng hoạt động.',
                    ];
                }

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

                $this->recalculateTotals($cart);

                return ['success' => true];
            });

            if (is_array($result) && ! ($result['success'] ?? false)) {
                return $result;
            }

            $cart->load('items.service');

            return [
                'success' => true,
                'data' => $this->formatCart($cart),
            ];
        } catch (\Exception $e) {
            Log::error('Cart addItem transaction failed', [
                'cart_id' => $cart->id,
                'service_id' => $serviceId,
                'quantity' => $quantity,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => 'transaction_failed',
                'message' => 'Có lỗi xảy ra khi thêm vào giỏ hàng.',
            ];
        }
    }

    public function updateItemQuantity(Cart $cart, int $itemId, int $quantity): array
    {
        try {
            $result = DB::transaction(function () use ($cart, $itemId, $quantity) {
                $item = CartItem::where('id', $itemId)
                    ->where('cart_id', $cart->id)
                    ->first();

                if (!$item) {
                    return [
                        'success' => false,
                        'error' => 'item_not_found',
                        'message' => 'Không tìm thấy sản phẩm trong giỏ.',
                    ];
                }

                $item->quantity = $quantity;
                $item->subtotal = $item->quantity * $item->price;
                $item->save();

                $this->recalculateTotals($cart);

                return ['success' => true];
            });

            if (!$result['success']) {
                return $result;
            }

            $cart->load('items.service');

            return [
                'success' => true,
                'data' => $this->formatCart($cart),
            ];
        } catch (\Exception $e) {
            Log::error('Cart updateItemQuantity transaction failed', [
                'cart_id' => $cart->id,
                'item_id' => $itemId,
                'quantity' => $quantity,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'transaction_failed',
                'message' => 'Có lỗi xảy ra khi cập nhật giỏ hàng.',
            ];
        }
    }

    public function removeItem(Cart $cart, int $itemId): array
    {
        try {
            $result = DB::transaction(function () use ($cart, $itemId) {
                $item = CartItem::where('id', $itemId)
                    ->where('cart_id', $cart->id)
                    ->first();

                if (!$item) {
                    return [
                        'success' => false,
                        'error' => 'item_not_found',
                        'message' => 'Không tìm thấy sản phẩm trong giỏ.',
                    ];
                }

                $item->delete();

                $this->recalculateTotals($cart);

                return ['success' => true];
            });

            if (!$result['success']) {
                return $result;
            }

            $cart->load('items.service');

            return [
                'success' => true,
                'data' => $this->formatCart($cart),
            ];
        } catch (\Exception $e) {
            Log::error('Cart removeItem transaction failed', [
                'cart_id' => $cart->id,
                'item_id' => $itemId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'transaction_failed',
                'message' => 'Có lỗi xảy ra khi xóa sản phẩm.',
            ];
        }
    }

    public function clearCart(Cart $cart): array
    {
        try {
            DB::transaction(function () use ($cart) {
                CartItem::where('cart_id', $cart->id)->delete();

                $cart->update([
                    'subtotal' => 0,
                    'total' => 0,
                    'tax' => 0,
                    'discount' => 0,
                    'shipping' => 0,
                ]);
            });

            return [
                'success' => true,
                'data' => $this->formatCart($cart),
            ];
        } catch (\Exception $e) {
            Log::error('Cart clearCart transaction failed', [
                'cart_id' => $cart->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'transaction_failed',
                'message' => 'Có lỗi xảy ra khi xóa giỏ hàng.',
            ];
        }
    }

    public function getItemCount(Cart $cart): int
    {
        return CartItem::where('cart_id', $cart->id)->sum('quantity');
    }

    public function recalculateTotals(Cart $cart): void
    {
        $cart->load('items.service');

        $subtotal = $cart->items->sum(function ($item) {
            return $item->quantity * $item->price;
        });

        $cart->subtotal = $subtotal;
        $cart->total = $subtotal + $cart->tax + $cart->shipping - $cart->discount;
        $cart->save();
    }

    public function formatCart(Cart $cart): array
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

    public function resolveCartFromRequest(Request $request): ?Cart
    {
        return $this->getCart(
            $request->user()?->id,
            $request->header('X-Cart-Session') ?? $request->cookie('cart_session')
        );
    }

    public function resolveOrCreateCartFromRequest(Request $request): Cart
    {
        $sessionId = $request->header('X-Cart-Session') ?? $request->cookie('cart_session');

        return $this->getOrCreateCart(
            $request->user()?->id,
            $sessionId
        );
    }
}
