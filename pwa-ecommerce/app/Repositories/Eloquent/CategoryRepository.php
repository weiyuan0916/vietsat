<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Category Repository
 */
class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    /**
     * CategoryRepository constructor.
     */
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }

    /**
     * Get active categories.
     */
    public function getActiveCategories(): Collection
    {
        return $this->model->active()
            ->orderBy('order')
            ->get();
    }

    /**
     * Get featured categories.
     */
    public function getFeaturedCategories(): Collection
    {
        return $this->model->active()
            ->featured()
            ->orderBy('order')
            ->get();
    }

    /**
     * Get parent categories.
     */
    public function getParentCategories(): Collection
    {
        return $this->model->active()
            ->parent()
            ->orderBy('order')
            ->get();
    }

    /**
     * Get child categories.
     */
    public function getChildCategories(int $parentId): Collection
    {
        return $this->model->active()
            ->where('parent_id', $parentId)
            ->orderBy('order')
            ->get();
    }

    /**
     * Get category with products count.
     */
    public function getCategoriesWithProductCount(): Collection
    {
        return $this->model->active()
            ->withCount(['products' => function($query) {
                $query->active();
            }])
            ->orderBy('order')
            ->get();
    }
}

