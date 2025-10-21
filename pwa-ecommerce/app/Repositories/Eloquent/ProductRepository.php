<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Product Repository
 */
class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    /**
     * ProductRepository constructor.
     */
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    /**
     * Get active products.
     */
    public function getActiveProducts(int $perPage = 15)
    {
        return $this->model->active()
            ->with(['category', 'vendor', 'primaryImage'])
            ->paginate($perPage);
    }

    /**
     * Get featured products.
     */
    public function getFeaturedProducts(int $limit = 10): Collection
    {
        return $this->model->active()
            ->featured()
            ->with(['category', 'vendor', 'primaryImage'])
            ->limit($limit)
            ->get();
    }

    /**
     * Get new products.
     */
    public function getNewProducts(int $limit = 10): Collection
    {
        return $this->model->active()
            ->new()
            ->with(['category', 'vendor', 'primaryImage'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get best sellers.
     */
    public function getBestSellers(int $limit = 10): Collection
    {
        return $this->model->active()
            ->with(['category', 'vendor', 'primaryImage'])
            ->orderBy('sales_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get flash sale products.
     */
    public function getFlashSaleProducts(int $limit = 10): Collection
    {
        return $this->model->active()
            ->whereHas('flashSale', function($query) {
                $query->active();
            })
            ->with(['category', 'vendor', 'primaryImage', 'flashSale'])
            ->limit($limit)
            ->get();
    }

    /**
     * Get products by category.
     */
    public function getProductsByCategory(int $categoryId, int $perPage = 15)
    {
        return $this->model->active()
            ->where('category_id', $categoryId)
            ->with(['category', 'vendor', 'primaryImage'])
            ->paginate($perPage);
    }

    /**
     * Get products by vendor.
     */
    public function getProductsByVendor(int $vendorId, int $perPage = 15)
    {
        return $this->model->active()
            ->where('vendor_id', $vendorId)
            ->with(['category', 'vendor', 'primaryImage'])
            ->paginate($perPage);
    }

    /**
     * Search products.
     */
    public function search(string $query, int $perPage = 15)
    {
        return $this->model->search($query)
            ->where('is_active', true)
            ->paginate($perPage);
    }

    /**
     * Update product rating.
     */
    public function updateRating(int $productId): void
    {
        $product = $this->find($productId);
        
        if (!$product) {
            return;
        }

        $reviews = $product->reviews()->approved()->get();
        
        $totalReviews = $reviews->count();
        $averageRating = $reviews->avg('rating') ?? 0;

        $product->update([
            'rating' => round($averageRating, 2),
            'review_count' => $totalReviews,
        ]);
    }

    /**
     * Increment view count.
     */
    public function incrementViewCount(int $productId): void
    {
        $this->model->where('id', $productId)->increment('view_count');
    }

    /**
     * Increment sales count.
     */
    public function incrementSalesCount(int $productId, int $quantity = 1): void
    {
        $this->model->where('id', $productId)->increment('sales_count', $quantity);
    }
}

