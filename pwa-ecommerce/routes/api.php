<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LicenseController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    
    // License Management Routes
    Route::prefix('licenses')->name('licenses.')->group(function () {
        
        /**
         * POST /api/v1/licenses/activate
         * Activate a license key with machine information
         * 
         * Request Body:
         * {
         *   "license_key": "LS-XXXX-XXXX-XXXX-XXXX",
         *   "machine_id": "unique-machine-identifier",
         *   "machine_name": "My Computer",
         *   "ip_address": "192.168.1.1",
         *   "hardware_info": {
         *     "cpu": "Intel i7",
         *     "ram": "16GB",
         *     "os": "Windows 11"
         *   }
         * }
         */
        Route::post('activate', [LicenseController::class, 'activate'])
            ->name('activate');
        
        /**
         * POST /api/v1/licenses/validate
         * Validate if a license is still active and not expired
         * 
         * Request Body:
         * {
         *   "license_key": "LS-XXXX-XXXX-XXXX-XXXX",
         *   "machine_id": "unique-machine-identifier",
         *   "app_version": "1.0.0" (optional - for version checking)
         * }
         */
        Route::post('validate', [LicenseController::class, 'validate'])
            ->name('validate');
        
        /**
         * POST /api/v1/licenses/check-status
         * Lightweight status check (returns minimal data)
         * 
         * Request Body:
         * {
         *   "license_key": "LS-XXXX-XXXX-XXXX-XXXX",
         *   "machine_id": "unique-machine-identifier",
         *   "app_version": "1.0.0" (optional - for version checking)
         * }
         */
        Route::post('check-status', [LicenseController::class, 'checkStatus'])
            ->name('check-status');
        
        /**
         * POST /api/v1/licenses/check-update
         * Check if app update is required or available
         * 
         * Request Body:
         * {
         *   "license_key": "LS-XXXX-XXXX-XXXX-XXXX",
         *   "app_version": "1.0.0"
         * }
         * 
         * Response:
         * {
         *   "success": true,
         *   "data": {
         *     "current_version": "1.0.0",
         *     "min_version": "1.0.0",
         *     "latest_version": "1.2.0",
         *     "is_compatible": true,
         *     "has_update": true,
         *     "requires_update": false,
         *     "force_update": false,
         *     "download_url": "https://yourapi.com/api/v1/licenses/download-update/LS-XXXX-XXXX-XXXX-XXXX",
         *     "file_version": "1.2.0",
         *     "file_size": 52428800,
         *     "file_size_formatted": "50.0 MB"
         *   }
         * }
         */
        Route::post('check-update', [LicenseController::class, 'checkUpdate'])
            ->name('check-update');
        
        /**
         * GET /api/v1/licenses/download-update/{licenseKey}
         * Download the update file for a license
         * 
         * This endpoint streams the update file (.exe, .apk, etc.) to the client.
         * The license must be valid and an update file must be uploaded.
         * 
         * Response: Binary file stream (application/x-msdownload, etc.)
         */
        Route::get('download-update/{licenseKey}', [LicenseController::class, 'downloadUpdate'])
            ->name('download-update');
        
        /**
         * POST /api/v1/licenses/renew
         * Renew a license for additional days
         * 
         * Request Body:
         * {
         *   "license_key": "LS-XXXX-XXXX-XXXX-XXXX",
         *   "days": 365
         * }
         */
        Route::post('renew', [LicenseController::class, 'renew'])
            ->name('renew');
        
        /**
         * POST /api/v1/licenses/deactivate
         * Deactivate a license from a specific machine
         * 
         * Request Body:
         * {
         *   "license_key": "LS-XXXX-XXXX-XXXX-XXXX",
         *   "machine_id": "unique-machine-identifier"
         * }
         */
        Route::post('deactivate', [LicenseController::class, 'deactivate'])
            ->name('deactivate');
        
        /**
         * GET /api/v1/licenses/{licenseKey}
         * Get detailed information about a license
         */
        Route::get('{licenseKey}', [LicenseController::class, 'show'])
            ->name('show');
    });
});

