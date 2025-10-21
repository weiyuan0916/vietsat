<?php

namespace App\Services;

use App\Models\License;
use App\Models\LicenseActivation;
use App\Repositories\Interfaces\LicenseRepositoryInterface;
use App\Repositories\Interfaces\LicenseActivationRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Exception;

/**
 * License Service
 * 
 * Handles business logic for license management and activation.
 */
class LicenseService
{
    /**
     * Constructor
     */
    public function __construct(
        protected LicenseRepositoryInterface $licenseRepository,
        protected LicenseActivationRepositoryInterface $activationRepository
    ) {}

    /**
     * Activate a license with machine information.
     * 
     * @throws Exception
     */
    public function activateLicense(
        string $licenseKey,
        string $machineId,
        ?string $machineName = null,
        ?string $ipAddress = null,
        ?array $hardwareInfo = null
    ): array {
        DB::beginTransaction();

        try {
            // Find license
            $license = $this->licenseRepository->findByKey($licenseKey);

            if (!$license) {
                throw new Exception('License key not found.');
            }

            // Validate license
            if (!$license->isValid()) {
                if ($license->isExpired()) {
                    throw new Exception('License has expired.');
                }
                throw new Exception('License is not active.');
            }

            // Check if already activated on this machine
            $existingActivation = $this->activationRepository->findByLicenseAndMachine($license, $machineId);

            if ($existingActivation && $existingActivation->isActive()) {
                // Update validation timestamp
                $this->activationRepository->updateValidation($existingActivation);
                
                DB::commit();

                return [
                    'success' => true,
                    'message' => 'License already activated on this machine.',
                    'license' => $license,
                    'activation' => $existingActivation,
                ];
            }

            // Check activation limit
            if (!$license->canActivate()) {
                throw new Exception('License activation limit reached. Maximum activations: ' . $license->max_activations);
            }

            // Create new activation
            $activation = $this->activationRepository->create([
                'license_id' => $license->id,
                'machine_id' => $machineId,
                'machine_name' => $machineName,
                'ip_address' => $ipAddress,
                'hardware_info' => $hardwareInfo,
                'status' => 'active',
                'activated_at' => now(),
                'last_validated_at' => now(),
            ]);

            // Increment activation count
            $this->licenseRepository->incrementActivations($license);

            DB::commit();

            return [
                'success' => true,
                'message' => 'License activated successfully.',
                'license' => $license->fresh(),
                'activation' => $activation,
            ];

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Validate a license for a machine.
     * 
     * @throws Exception
     */
    public function validateLicense(string $licenseKey, string $machineId): array
    {
        $license = $this->licenseRepository->findByKey($licenseKey);

        if (!$license) {
            throw new Exception('License key not found.');
        }

        // Check if license is expired
        if ($license->isExpired()) {
            // Update license status to expired
            $this->licenseRepository->updateStatus($license, 'expired');
            throw new Exception('License has expired on ' . $license->expires_at->format('Y-m-d'));
        }

        // Check if license is valid
        if (!$license->isValid()) {
            throw new Exception('License is not active.');
        }

        // Check activation for this machine
        $activation = $this->activationRepository->findByLicenseAndMachine($license, $machineId);

        if (!$activation || !$activation->isActive()) {
            throw new Exception('License not activated on this machine.');
        }

        // Update last validation timestamp
        $this->activationRepository->updateValidation($activation);

        return [
            'success' => true,
            'message' => 'License is valid.',
            'valid' => true,
            'license' => $license,
            'activation' => $activation,
            'days_remaining' => $license->daysRemaining(),
            'expires_at' => $license->expires_at->toDateTimeString(),
        ];
    }

    /**
     * Renew a license.
     * 
     * @throws Exception
     */
    public function renewLicense(string $licenseKey, int $days = 365): array
    {
        DB::beginTransaction();

        try {
            $license = $this->licenseRepository->findByKey($licenseKey);

            if (!$license) {
                throw new Exception('License key not found.');
            }

            // Renew the license
            $this->licenseRepository->renew($license, $days);

            DB::commit();

            return [
                'success' => true,
                'message' => 'License renewed successfully.',
                'license' => $license->fresh(),
                'new_expiration' => $license->fresh()->expires_at->toDateTimeString(),
                'days_added' => $days,
            ];

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Deactivate a license from a machine.
     * 
     * @throws Exception
     */
    public function deactivateLicense(string $licenseKey, string $machineId): array
    {
        DB::beginTransaction();

        try {
            $license = $this->licenseRepository->findByKey($licenseKey);

            if (!$license) {
                throw new Exception('License key not found.');
            }

            $activation = $this->activationRepository->findByLicenseAndMachine($license, $machineId);

            if (!$activation) {
                throw new Exception('No activation found for this machine.');
            }

            if (!$activation->isActive()) {
                throw new Exception('License is not active on this machine.');
            }

            // Deactivate
            $this->activationRepository->deactivate($activation);

            // Decrement activation count
            $this->licenseRepository->decrementActivations($license);

            DB::commit();

            return [
                'success' => true,
                'message' => 'License deactivated successfully.',
                'license' => $license->fresh(),
            ];

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get license information.
     * 
     * @throws Exception
     */
    public function getLicenseInfo(string $licenseKey): array
    {
        $license = $this->licenseRepository->findByKey($licenseKey);

        if (!$license) {
            throw new Exception('License key not found.');
        }

        $activations = $this->activationRepository->getByLicense($license);

        return [
            'success' => true,
            'license' => $license,
            'activations' => $activations,
            'is_valid' => $license->isValid(),
            'is_expired' => $license->isExpired(),
            'days_remaining' => $license->daysRemaining(),
        ];
    }

    /**
     * Create a new license.
     * 
     * @throws Exception
     */
    public function createLicense(array $data): License
    {
        DB::beginTransaction();

        try {
            // Generate license key if not provided
            if (!isset($data['license_key'])) {
                $data['license_key'] = License::generateKey();
            }

            // Set default dates if not provided
            if (!isset($data['issued_at'])) {
                $data['issued_at'] = now();
            }

            if (!isset($data['expires_at'])) {
                $data['expires_at'] = now()->addYear();
            }

            $license = $this->licenseRepository->create($data);

            DB::commit();

            return $license;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

