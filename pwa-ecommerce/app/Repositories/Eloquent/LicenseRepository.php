<?php

namespace App\Repositories\Eloquent;

use App\Models\License;
use App\Repositories\Interfaces\LicenseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * License Repository Implementation
 */
class LicenseRepository implements LicenseRepositoryInterface
{
    /**
     * Find license by key.
     */
    public function findByKey(string $key): ?License
    {
        return License::where('license_key', $key)->first();
    }

    /**
     * Create a new license.
     */
    public function create(array $data): License
    {
        return License::create($data);
    }

    /**
     * Update a license.
     */
    public function update(License $license, array $data): bool
    {
        return $license->update($data);
    }

    /**
     * Delete a license.
     */
    public function delete(License $license): bool
    {
        return $license->delete();
    }

    /**
     * Get all valid licenses.
     */
    public function getValidLicenses(): Collection
    {
        return License::valid()->get();
    }

    /**
     * Get expired licenses.
     */
    public function getExpiredLicenses(): Collection
    {
        return License::expired()->get();
    }

    /**
     * Renew a license.
     */
    public function renew(License $license, int $days): bool
    {
        return $license->update([
            'expires_at' => now()->addDays($days),
            'last_renewed_at' => now(),
            'status' => 'active',
        ]);
    }

    /**
     * Increment activation count.
     */
    public function incrementActivations(License $license): bool
    {
        return $license->increment('current_activations');
    }

    /**
     * Decrement activation count.
     */
    public function decrementActivations(License $license): bool
    {
        if ($license->current_activations > 0) {
            return $license->decrement('current_activations');
        }

        return false;
    }

    /**
     * Update license status.
     */
    public function updateStatus(License $license, string $status): bool
    {
        return $license->update(['status' => $status]);
    }

    /**
     * Check if license key exists.
     */
    public function keyExists(string $key): bool
    {
        return License::where('license_key', $key)->exists();
    }
}

