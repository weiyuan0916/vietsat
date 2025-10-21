<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;

/**
 * Vendor Repository Interface
 */
interface VendorRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get active vendors.
     */
    public function getActiveVendors(int $perPage = 15);

    /**
     * Get verified vendors.
     */
    public function getVerifiedVendors(int $perPage = 15);

    /**
     * Get trusted vendors.
     */
    public function getTrustedVendors(int $perPage = 15);

    /**
     * Get vendors by location.
     */
    public function getVendorsByLocation(string $city): Collection;

    /**
     * Get top rated vendors.
     */
    public function getTopRatedVendors(int $limit = 10): Collection;

    /**
     * Update vendor rating.
     */
    public function updateRating(int $vendorId): void;
}

