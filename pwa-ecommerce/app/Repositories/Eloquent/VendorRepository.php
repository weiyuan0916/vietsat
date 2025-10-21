<?php

namespace App\Repositories\Eloquent;

use App\Models\Vendor;
use App\Repositories\Interfaces\VendorRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Vendor Repository
 */
class VendorRepository extends BaseRepository implements VendorRepositoryInterface
{
    /**
     * VendorRepository constructor.
     */
    public function __construct(Vendor $model)
    {
        parent::__construct($model);
    }

    /**
     * Get active vendors.
     */
    public function getActiveVendors(int $perPage = 15)
    {
        return $this->model->active()->paginate($perPage);
    }

    /**
     * Get verified vendors.
     */
    public function getVerifiedVendors(int $perPage = 15)
    {
        return $this->model->verified()->paginate($perPage);
    }

    /**
     * Get trusted vendors.
     */
    public function getTrustedVendors(int $perPage = 15)
    {
        return $this->model->trusted()->paginate($perPage);
    }

    /**
     * Get vendors by location.
     */
    public function getVendorsByLocation(string $city): Collection
    {
        return $this->model->where('city', $city)
            ->active()
            ->get();
    }

    /**
     * Get top rated vendors.
     */
    public function getTopRatedVendors(int $limit = 10): Collection
    {
        return $this->model->active()
            ->orderBy('rating', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Update vendor rating.
     */
    public function updateRating(int $vendorId): void
    {
        $vendor = $this->find($vendorId);
        
        if (!$vendor) {
            return;
        }

        $reviews = $vendor->reviews()->approved()->get();
        
        $totalReviews = $reviews->count();
        $averageRating = $reviews->avg('rating') ?? 0;
        $positiveReviews = $reviews->where('rating', '>=', 4)->count();
        $positivePercentage = $totalReviews > 0 ? ($positiveReviews / $totalReviews) * 100 : 0;

        $vendor->update([
            'rating' => round($averageRating, 2),
            'total_reviews' => $totalReviews,
            'positive_rating_percentage' => round($positivePercentage, 2),
        ]);
    }
}

