<?php

namespace App\Repositories\Eloquent;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Service;
use App\Repositories\Interfaces\CartRepositoryInterface;

/**
 * Cart Repository
 */
class CartRepository implements CartRepositoryInterface
{
    /**
     * @var Cart
     */
    protected $cart;

    /**
     * @var CartItem
     */
    protected $cartItem;

    /**
     * CartRepository constructor.
     */
    public function __construct(Cart $cart, CartItem $cartItem)
    {
        $this->cart = $cart;
        $this->cartItem = $cartItem;
    }

    /**
     * Get user cart.
     */
    public function getUserCart(int $userId): ?Cart
    {
        return $this->cart->where('user_id', $userId)
            ->with(['items.service'])
            ->first();
    }

    /**
     * Get or create user cart.
     */
    public function getOrCreateCart(int $userId): Cart
    {
        $cart = $this->getUserCart($userId);

        if (!$cart) {
            $cart = $this->cart->create([
                'user_id' => $userId,
                'subtotal' => 0,
                'tax' => 0,
                'shipping' => 0,
                'discount' => 0,
                'total' => 0,
            ]);
        }

        return $cart;
    }

    /**
     * Add item to cart.
     */
    public function addItem(int $cartId, int $serviceId, int $quantity = 1, array $options = []): void
    {
        $service = Service::where('id', $serviceId)
            ->where('is_active', true)
            ->first();

        if (!$service) {
            return;
        }

        $existingItem = $this->cartItem->where('cart_id', $cartId)
            ->where('service_id', $serviceId)
            ->first();

        $price = $service->price;

        if ($existingItem) {
            $existingItem->quantity += $quantity;
            $existingItem->setAttribute('subtotal', (string) ($existingItem->quantity * (float) $price));
            $existingItem->save();
        } else {
            $this->cartItem->create([
                'cart_id' => $cartId,
                'service_id' => $serviceId,
                'quantity' => $quantity,
                'price' => $price,
                'subtotal' => (string) ($quantity * (float) $price),
                'options' => $options,
            ]);
        }

        $this->calculateTotals($cartId);
    }

    /**
     * Update cart item quantity.
     */
    public function updateItemQuantity(int $cartItemId, int $quantity): void
    {
        $cartItem = $this->cartItem->find($cartItemId);

        if (!$cartItem) {
            return;
        }

        $cartItem->quantity = $quantity;
        $cartItem->setAttribute('subtotal', (string) ((float) $quantity * (float) $cartItem->price));
        $cartItem->save();

        $this->calculateTotals($cartItem->cart_id);
    }

    /**
     * Remove item from cart.
     */
    public function removeItem(int $cartItemId): void
    {
        $cartItem = $this->cartItem->find($cartItemId);

        if (!$cartItem) {
            return;
        }

        $cartId = $cartItem->cart_id;
        $cartItem->delete();

        $this->calculateTotals($cartId);
    }

    /**
     * Clear cart.
     */
    public function clearCart(int $cartId): void
    {
        $this->cartItem->where('cart_id', $cartId)->delete();
        
        $cart = $this->cart->find($cartId);
        if ($cart) {
            $cart->update([
                'subtotal' => 0,
                'tax' => 0,
                'shipping' => 0,
                'discount' => 0,
                'total' => 0,
            ]);
        }
    }

    /**
     * Calculate cart totals.
     */
    public function calculateTotals(int $cartId): void
    {
        $cart = $this->cart->with('items')->find($cartId);

        if (!$cart) {
            return;
        }

        $cart->calculateTotals();
    }

    /**
     * Get cart total items count.
     */
    public function getCartItemsCount(int $cartId): int
    {
        return $this->cartItem->where('cart_id', $cartId)->sum('quantity');
    }
}

