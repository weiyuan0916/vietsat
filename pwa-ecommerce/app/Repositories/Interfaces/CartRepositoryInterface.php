<?php

namespace App\Repositories\Interfaces;

use App\Models\Cart;

/**
 * Cart Repository Interface
 */
interface CartRepositoryInterface
{
    /**
     * Get user cart.
     */
    public function getUserCart(int $userId): ?Cart;

    /**
     * Get or create user cart.
     */
    public function getOrCreateCart(int $userId): Cart;

    /**
     * Add item to cart.
     */
    public function addItem(int $cartId, int $productId, int $quantity = 1, array $options = []): void;

    /**
     * Update cart item quantity.
     */
    public function updateItemQuantity(int $cartItemId, int $quantity): void;

    /**
     * Remove item from cart.
     */
    public function removeItem(int $cartItemId): void;

    /**
     * Clear cart.
     */
    public function clearCart(int $cartId): void;

    /**
     * Calculate cart totals.
     */
    public function calculateTotals(int $cartId): void;

    /**
     * Get cart total items count.
     */
    public function getCartItemsCount(int $cartId): int;
}

