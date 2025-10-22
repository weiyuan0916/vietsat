<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActivateLicenseRequest;
use App\Http\Requests\ValidateLicenseRequest;
use App\Http\Requests\RenewLicenseRequest;
use App\Http\Requests\DeactivateLicenseRequest;
use App\Http\Resources\LicenseResource;
use App\Services\LicenseService;
use App\Models\License;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Exception;

/**
 * License API Controller
 * 
 * Handles API endpoints for software license management.
 */
class LicenseController extends Controller
{
    /**
     * Constructor
     */
    public function __construct(
        protected LicenseService $licenseService
    ) {}

    /**
     * Activate a license with machine information.
     * 
     * @param ActivateLicenseRequest $request
     * @return JsonResponse
     */
    public function activate(ActivateLicenseRequest $request): JsonResponse
    {
        try {
            $result = $this->licenseService->activateLicense(
                licenseKey: $request->input('license_key'),
                machineId: $request->input('machine_id'),
                machineName: $request->input('machine_name'),
                ipAddress: $request->input('ip_address', $request->ip()),
                hardwareInfo: $request->input('hardware_info')
            );

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data' => [
                    'license' => new LicenseResource($result['license']),
                    'activation' => $result['activation'],
                ],
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => 'ACTIVATION_FAILED',
            ], 200);
        }
    }

    /**
     * Validate a license for a machine.
     * 
     * @param ValidateLicenseRequest $request
     * @return JsonResponse
     */
    public function validate(ValidateLicenseRequest $request): JsonResponse
    {
        try {
            $result = $this->licenseService->validateLicense(
                licenseKey: $request->input('license_key'),
                machineId: $request->input('machine_id')
            );

            $response = [
                'success' => true,
                'message' => $result['message'],
                'data' => [
                    'valid' => $result['valid'],
                    'license' => new LicenseResource($result['license']),
                    'days_remaining' => $result['days_remaining'],
                    'expires_at' => $result['expires_at'],
                ],
            ];

            // Add version checking if app_version is provided
            if ($request->has('app_version')) {
                $versionStatus = $result['license']->getVersionStatus($request->input('app_version'));
                $response['data']['version_status'] = $versionStatus;
            }

            return response()->json($response, 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => 'VALIDATION_FAILED',
                'data' => [
                    'valid' => false,
                ],
            ], 200);
        }
    }

    /**
     * Renew a license.
     * 
     * @param RenewLicenseRequest $request
     * @return JsonResponse
     */
    public function renew(RenewLicenseRequest $request): JsonResponse
    {
        try {
            $result = $this->licenseService->renewLicense(
                licenseKey: $request->input('license_key'),
                days: $request->getDays()
            );

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data' => [
                    'license' => new LicenseResource($result['license']),
                    'new_expiration' => $result['new_expiration'],
                    'days_added' => $result['days_added'],
                ],
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => 'RENEWAL_FAILED',
            ], 200);
        }
    }

    /**
     * Deactivate a license from a machine.
     * 
     * @param DeactivateLicenseRequest $request
     * @return JsonResponse
     */
    public function deactivate(DeactivateLicenseRequest $request): JsonResponse
    {
        try {
            $result = $this->licenseService->deactivateLicense(
                licenseKey: $request->input('license_key'),
                machineId: $request->input('machine_id')
            );

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data' => [
                    'license' => new LicenseResource($result['license']),
                ],
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => 'DEACTIVATION_FAILED',
            ], 200);
        }
    }

    /**
     * Get license information.
     * 
     * @param string $licenseKey
     * @return JsonResponse
     */
    public function show(string $licenseKey): JsonResponse
    {
        try {
            $result = $this->licenseService->getLicenseInfo($licenseKey);

            return response()->json([
                'success' => true,
                'data' => [
                    'license' => new LicenseResource($result['license']->load('activations')),
                    'is_valid' => $result['is_valid'],
                    'is_expired' => $result['is_expired'],
                    'days_remaining' => $result['days_remaining'],
                ],
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => 'LICENSE_NOT_FOUND',
            ], 200);
        }
    }

    /**
     * Check license status (lightweight version of validate).
     * 
     * @param ValidateLicenseRequest $request
     * @return JsonResponse
     */
    public function checkStatus(ValidateLicenseRequest $request): JsonResponse
    {
        try {
            $result = $this->licenseService->validateLicense(
                licenseKey: $request->input('license_key'),
                machineId: $request->input('machine_id')
            );

            $response = [
                'success' => true,
                'valid' => $result['valid'],
                'days_remaining' => $result['days_remaining'],
                'expires_at' => $result['expires_at'],
            ];

            // Add version checking if app_version is provided
            if ($request->has('app_version')) {
                $versionStatus = $result['license']->getVersionStatus($request->input('app_version'));
                $response['version_status'] = $versionStatus;
            }

            return response()->json($response, 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'valid' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }

    /**
     * Check if app update is required or available.
     * 
     * @param ValidateLicenseRequest $request
     * @return JsonResponse
     */
    public function checkUpdate(ValidateLicenseRequest $request): JsonResponse
    {
        try {
            $licenseKey = $request->input('license_key');
            $appVersion = $request->input('app_version');

            $result = $this->licenseService->getLicenseInfo($licenseKey);
            $license = $result['license'];

            if (!$license->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'License is not valid or has expired',
                    'error' => 'INVALID_LICENSE',
                ], 200);
            }

            $versionStatus = $license->getVersionStatus($appVersion);

            return response()->json([
                'success' => true,
                'data' => $versionStatus,
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => 'CHECK_UPDATE_FAILED',
            ], 200);
        }
    }

    /**
     * Download update file for a license.
     * 
     * @param string $licenseKey
     * @return StreamedResponse|JsonResponse
     */
    public function downloadUpdate(string $licenseKey): StreamedResponse|JsonResponse
    {
        try {
            $license = License::where('license_key', $licenseKey)->first();

            if (!$license) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid license key',
                    'error' => 'INVALID_LICENSE',
                ], 404);
            }

            if (!$license->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'License is not active or has expired',
                    'error' => 'LICENSE_INVALID',
                ], 403);
            }

            if (!$license->update_file_path) {
                return response()->json([
                    'success' => false,
                    'message' => 'No update file available for this license',
                    'error' => 'NO_UPDATE_FILE',
                ], 404);
            }

            $filePath = $license->getUpdateFilePath();

            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Update file not found on server',
                    'error' => 'FILE_NOT_FOUND',
                ], 404);
            }

            // Get original filename from path
            $fileName = basename($license->update_file_path);
            
            // Determine content type
            $extension = pathinfo($fileName, PATHINFO_EXTENSION);
            $contentTypes = [
                'exe' => 'application/x-msdownload',
                'apk' => 'application/vnd.android.package-archive',
                'ipa' => 'application/octet-stream',
                'dmg' => 'application/x-apple-diskimage',
                'zip' => 'application/zip',
            ];
            
            $contentType = $contentTypes[$extension] ?? 'application/octet-stream';

            // Stream the file download
            return response()->streamDownload(function () use ($filePath) {
                echo file_get_contents($filePath);
            }, $fileName, [
                'Content-Type' => $contentType,
                'Content-Length' => filesize($filePath),
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                'Cache-Control' => 'no-cache, must-revalidate',
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => 'DOWNLOAD_FAILED',
            ], 500);
        }
    }
}

