<?php

namespace App\Repositories\Eloquent;

use App\Models\License;
use App\Models\LicenseActivation;
use App\Repositories\Interfaces\LicenseActivationRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * License Activation Repository Implementation
 */
class LicenseActivationRepository implements LicenseActivationRepositoryInterface
{
    /**
     * Find activation by license and machine.
     */
    public function findByLicenseAndMachine(License $license, string $machineId): ?LicenseActivation
    {
        return LicenseActivation::where('license_id', $license->id)
            ->where('machine_id', $machineId)
            ->first();
    }

    /**
     * Create a new activation.
     */
    public function create(array $data): LicenseActivation
    {
        return LicenseActivation::create($data);
    }

    /**
     * Update an activation.
     */
    public function update(LicenseActivation $activation, array $data): bool
    {
        return $activation->update($data);
    }

    /**
     * Delete an activation.
     */
    public function delete(LicenseActivation $activation): bool
    {
        return $activation->delete();
    }

    /**
     * Get all activations for a license.
     */
    public function getByLicense(License $license): Collection
    {
        return LicenseActivation::where('license_id', $license->id)->get();
    }

    /**
     * Get active activations for a license.
     */
    public function getActiveByLicense(License $license): Collection
    {
        return LicenseActivation::where('license_id', $license->id)
            ->where('status', 'active')
            ->get();
    }

    /**
     * Deactivate an activation.
     */
    public function deactivate(LicenseActivation $activation): bool
    {
        return $activation->update([
            'status' => 'deactivated',
            'deactivated_at' => now(),
        ]);
    }

    /**
     * Update last validation timestamp.
     */
    public function updateValidation(LicenseActivation $activation): bool
    {
        return $activation->update([
            'last_validated_at' => now(),
        ]);
    }

    /**
     * Check if machine is already activated.
     */
    public function isActivated(License $license, string $machineId): bool
    {
        return LicenseActivation::where('license_id', $license->id)
            ->where('machine_id', $machineId)
            ->where('status', 'active')
            ->exists();
    }

    /**
     * Get activation count for a license.
     */
    public function getActivationCount(License $license): int
    {
        return LicenseActivation::where('license_id', $license->id)
            ->where('status', 'active')
            ->count();
    }
}

