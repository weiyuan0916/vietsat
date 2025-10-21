<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;

/**
 * Category Repository Interface
 */
interface CategoryRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get active categories.
     */
    public function getActiveCategories(): Collection;

    /**
     * Get featured categories.
     */
    public function getFeaturedCategories(): Collection;

    /**
     * Get parent categories.
     */
    public function getParentCategories(): Collection;

    /**
     * Get child categories.
     */
    public function getChildCategories(int $parentId): Collection;

    /**
     * Get category with products count.
     */
    public function getCategoriesWithProductCount(): Collection;
}

