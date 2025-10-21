<?php

namespace App\Repositories\Interfaces;

use App\Models\License;
use App\Models\LicenseActivation;
use Illuminate\Database\Eloquent\Collection;

/**
 * License Activation Repository Interface
 */
interface LicenseActivationRepositoryInterface
{
    /**
     * Find activation by license and machine.
     */
    public function findByLicenseAndMachine(License $license, string $machineId): ?LicenseActivation;

    /**
     * Create a new activation.
     */
    public function create(array $data): LicenseActivation;

    /**
     * Update an activation.
     */
    public function update(LicenseActivation $activation, array $data): bool;

    /**
     * Delete an activation.
     */
    public function delete(LicenseActivation $activation): bool;

    /**
     * Get all activations for a license.
     */
    public function getByLicense(License $license): Collection;

    /**
     * Get active activations for a license.
     */
    public function getActiveByLicense(License $license): Collection;

    /**
     * Deactivate an activation.
     */
    public function deactivate(LicenseActivation $activation): bool;

    /**
     * Update last validation timestamp.
     */
    public function updateValidation(LicenseActivation $activation): bool;

    /**
     * Check if machine is already activated.
     */
    public function isActivated(License $license, string $machineId): bool;

    /**
     * Get activation count for a license.
     */
    public function getActivationCount(License $license): int;
}

