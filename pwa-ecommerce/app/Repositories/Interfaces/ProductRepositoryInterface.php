<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;

/**
 * Product Repository Interface
 */
interface ProductRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get active products.
     */
    public function getActiveProducts(int $perPage = 15);

    /**
     * Get featured products.
     */
    public function getFeaturedProducts(int $limit = 10): Collection;

    /**
     * Get new products.
     */
    public function getNewProducts(int $limit = 10): Collection;

    /**
     * Get best sellers.
     */
    public function getBestSellers(int $limit = 10): Collection;

    /**
     * Get flash sale products.
     */
    public function getFlashSaleProducts(int $limit = 10): Collection;

    /**
     * Get products by category.
     */
    public function getProductsByCategory(int $categoryId, int $perPage = 15);

    /**
     * Get products by vendor.
     */
    public function getProductsByVendor(int $vendorId, int $perPage = 15);

    /**
     * Search products.
     */
    public function search(string $query, int $perPage = 15);

    /**
     * Update product rating.
     */
    public function updateRating(int $productId): void;

    /**
     * Increment view count.
     */
    public function incrementViewCount(int $productId): void;

    /**
     * Increment sales count.
     */
    public function incrementSalesCount(int $productId, int $quantity = 1): void;
}

