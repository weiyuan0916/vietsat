<?php

namespace App\Repositories\Interfaces;

use App\Models\License;
use Illuminate\Database\Eloquent\Collection;

/**
 * License Repository Interface
 */
interface LicenseRepositoryInterface
{
    /**
     * Find license by key.
     */
    public function findByKey(string $key): ?License;

    /**
     * Create a new license.
     */
    public function create(array $data): License;

    /**
     * Update a license.
     */
    public function update(License $license, array $data): bool;

    /**
     * Delete a license.
     */
    public function delete(License $license): bool;

    /**
     * Get all valid licenses.
     */
    public function getValidLicenses(): Collection;

    /**
     * Get expired licenses.
     */
    public function getExpiredLicenses(): Collection;

    /**
     * Renew a license.
     */
    public function renew(License $license, int $days): bool;

    /**
     * Increment activation count.
     */
    public function incrementActivations(License $license): bool;

    /**
     * Decrement activation count.
     */
    public function decrementActivations(License $license): bool;

    /**
     * Update license status.
     */
    public function updateStatus(License $license, string $status): bool;

    /**
     * Check if license key exists.
     */
    public function keyExists(string $key): bool;
}

