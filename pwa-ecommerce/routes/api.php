<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiDocumentationController;
use App\Http\Controllers\Api\FacebookProfileController;
use App\Http\Controllers\Api\LicenseController;
use App\Http\Controllers\Api\PcInfoController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\OrderController;

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
         * POST /api/v1/licenses/check-key
         * Simple license key status check (only requires license key)
         * 
         * Request Body:
         * {
         *   "license_key": "LS-XXXX-XXXX-XXXX-XXXX"
         * }
         * 
         * Response:
         * {
         *   "status": true/false
         * }
         */
        Route::post('check-key', [LicenseController::class, 'checkKey'])
            ->name('check-key');
        
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

    // PC Information Routes
    Route::prefix('pc-infos')->name('pc-infos.')->group(function () {

        /**
         * POST /api/v1/pc-infos
         * Store PC information with smart duplicate handling
         *
         * Logic:
         * - If public_ip_address equals local_ip_address: Update existing record
         * - If public_ip_address differs from local_ip_address: Create new record
         * - Always saves the data regardless of existing records
         *
         * Request Body:
         * {
         *   "host_name": "DESKTOP-ABC123",
         *   "user_name": "john_doe",
         *   "password": "encrypted_password",
         *   "local_ip_address": "192.168.1.100",
         *   "public_ip_address": "203.0.113.1"
         * }
         *
         * Response:
         * - 201: Created (new record)
         * - 200: Updated (existing record)
         */
        Route::post('/', [PcInfoController::class, 'store'])
            ->name('store');

        /**
         * GET /api/v1/pc-infos
         * Get all PC information with optional filtering and pagination
         *
         * Query Parameters:
         * - host_name: Filter by host name
         * - user_name: Filter by user name
         * - ip_address: Filter by IP address
         * - sort_by: Sort field (default: created_at)
         * - sort_direction: Sort direction (asc/desc, default: desc)
         * - per_page: Items per page (default: 15)
         */
        Route::get('/', [PcInfoController::class, 'index'])
            ->name('index');

        /**
         * GET /api/v1/pc-infos/{pcInfo}
         * Get specific PC information
         */
        Route::get('{pcInfo}', [PcInfoController::class, 'show'])
            ->name('show');

        /**
         * PUT/PATCH /api/v1/pc-infos/{pcInfo}
         * Update PC information
         */
        Route::match(['put', 'patch'], '{pcInfo}', [PcInfoController::class, 'update'])
            ->name('update');

        /**
         * DELETE /api/v1/pc-infos/{pcInfo}
         * Delete PC information
         */
        Route::delete('{pcInfo}', [PcInfoController::class, 'destroy'])
            ->name('destroy');

        /**
         * GET /api/v1/pc-infos/statistics/overview
         * Get PC information statistics
         */
        Route::get('statistics/overview', [PcInfoController::class, 'statistics'])
            ->name('statistics');
    });

    // Service Routes
    Route::prefix('services')->name('services.')->group(function () {
        /**
         * GET /api/v1/services
         * Get all services with pagination
         *
         * Query Parameters:
         * - page: Trang hiện tại (mặc định: 1)
         * - per_page: Số item mỗi trang (mặc định: 10, tối đa: 100)
         *
         * Response (200) - Success:
         * {
         *   "status": true,
         *   "message": "Lấy danh sách dịch vụ thành công.",
         *   "data": {
         *     "items": [...],
         *     "meta": {...},
         *     "links": {...}
         *   }
         * }
         */
        Route::get('/', [ServiceController::class, 'index'])
            ->name('index');

        /**
         * GET /api/v1/services/default
         * Get the default service plan
         *
         * Response (200) - Success:
         * {
         *   "status": true,
         *   "message": "Lấy thông tin dịch vụ thành công.",
         *   "data": {
         *     "id": 1,
         *     "name": "Default Plan",
         *     "duration_days": 90,
         *     "price": 100000,
         *     "formatted_price": "100,000 VND"
         *   }
         * }
         *
         * Response (404) - Not found:
         * {
         *   "status": false,
         *   "message": "Không tìm thấy dịch vụ hoạt động.",
         *   "data": null
         * }
         */
        Route::get('default', [ServiceController::class, 'default'])
            ->name('default');

        /**
         * GET /api/v1/services/{id}
         * Get a specific service by ID
         *
         * Response (200) - Success:
         * {
         *   "status": true,
         *   "message": "Lấy thông tin dịch vụ thành công.",
         *   "data": {
         *     "id": 1,
         *     "name": "Default Plan",
         *     " /"
         * ơp0987654321`1`2345u690-=ư=-09876=-0987qs    
         * i ds  edsduration_days": 90,
         *     "price": 100000,
         *     "formatted_price": "100,000 VND",
         *     "is_active": true,
         *     "created_at": "2026-01-30T10:00:00Z",
         *     "updated_at": "2026-01-30T10:00:00Z"
         *   }
         * }
         *
         * Response (404) - Not found:
         * {
         *   "status": false,
         *   "message": "Không tìm thấy dịch vụ.",
         *   "data": null
         * }
         */
        Route::get('{id}', [ServiceController::class, 'show'])
            ->name('show');
    });

    // Order Routes
    Route::prefix('orders')->name('orders.')->group(function () {
        /**
         * POST /api/v1/orders
         * Create a new service order
         *
         * Request Body:
         * {
         *   "facebook_profile_link": "https://facebook.com/..."
         * }
         *
         * Response (201) - Success:
         * {
         *   "status": true,
         *   "message": "Tạo đơn hàng thành công.",
         *   "data": {
         *     "order_code": "ORD-XXXXXXXXXX",
         *     "amount": 100000,
         *     "expires_at": "2026-01-31T10:05:00Z",
         *     "qr_content": "bank:ORD-XXXXXXXXXX:100000",
         *     "status": "pending",
         *     "service": {
         *       "id": 1,
         *       "name": "Default Plan",
         *       "duration_days": 90
         *     }
         *   }
         * }
         *
         * Response (422) - Validation failed:
         * {
         *   "status": false,
         *   "message": "Dữ liệu đầu vào không hợp lệ.",
         *   "data": null,
         *   "errors": {
         *     "facebook_profile_link": ["The facebook profile link field is required."]
         *   }
         * }
         *
         * Response (404) - No active service:
         * {
         *   "status": false,
         *   "message": "Không tìm thấy dịch vụ hoạt động.",
         *   "data": null
         * }
         */
        Route::post('/', [OrderController::class, 'store'])
            ->name('store');

        /**
         * GET /api/v1/orders/{orderCode}
         * Get order details and status
         *
         * Response (200) - Success:
         * {
         *   "status": true,
         *   "message": "Lấy thông tin đơn hàng thành công.",
         *   "data": {
         *     "order_code": "ORD-XXXXXXXXXX",
         *     "amount": 100000,
         *     "status": "pending",
         *     "expires_at": "2026-01-31T10:05:00Z",
         *     "paid_at": null,
         *     "created_at": "2026-01-31T10:00:00Z",
         *     "service": {
         *       "id": 1,
         *       "name": "Default Plan",
         *       "duration_days": 90
         *     }
         *   }
         * }
         *
         * Response (404) - Order not found:
         * {
         *   "status": false,
         *   "message": "Không tìm thấy đơn hàng.",
         *   "data": null
         * }
         */
        Route::get('{orderCode}', [OrderController::class, 'show'])
            ->name('show');
    });

    // Facebook Profile Routes
    Route::prefix('facebook-profiles')->name('facebook-profiles.')->group(function () {
        /**
         * POST /api/v1/facebook-profiles/validate
         * Validate and parse Facebook profile URL
         *
         * Request Body:
         * {
         *   "facebook_profile_link": "https://facebook.com/username hoặc https://facebook.com/profile.php?id=123456789"
         * }
         *
         * Response (200) - Valid profile:
         * {
         *   "status": true,
         *   "message": "URL hợp lệ.",
         *   "data": {
         *     "original_url": "https://facebook.com/username",
         *     "normalized_url": "https://www.facebook.com/username",
         *     "profile_id": "username",
         *     "profile_type": "username",
         *     "is_mobile": false,
         *     "is_shortened": false,
         *     "profile_info": {
         *       "username": "username",
         *       "facebook_url": "https://www.facebook.com/username",
         *       "profile_url": "https://www.facebook.com/username"
         *     }
         *   }
         * }
         *
         * Response (422) - Invalid URL:
         * {
         *   "status": false,
         *   "message": "Dữ liệu đầu vào không hợp lệ.",
         *   "data": null,
         *   "errors": {
         *     "facebook_profile_link": ["URL phải là liên kết Facebook hợp lệ."]
         *   }
         * }
         */
        Route::post('validate', [FacebookProfileController::class, 'validate'])
            ->name('validate');

        /**
         * POST /api/v1/facebook-profiles/validate-batch
         * Validate multiple Facebook profile URLs at once
         *
         * Request Body:
         * {
         *   "urls": [
         *     "https://facebook.com/username1",
         *     "https://facebook.com/username2"
         *   ]
         * }
         *
         * Response (200):
         * {
         *   "status": true,
         *   "message": "Kiểm tra hàng loạt hoàn tất.",
         *   "data": {
         *     "total": 2,
         *     "valid": 2,
         *     "invalid": 0,
         *     "results": [...]
         *   }
         * }
         */
        Route::post('validate-batch', [FacebookProfileController::class, 'validateBatch'])
            ->name('validate-batch');

        /**
         * GET /api/v1/facebook-profiles/check
         * Quick check if a Facebook profile URL is valid
         *
         * Query Parameters:
         * - url: Facebook profile URL to check
         *
         * Response (200) - Valid:
         * {
         *   "status": true,
         *   "message": "URL hợp lệ.",
         *   "data": {
         *     "valid": true,
         *     "profile_id": "username",
         *     "profile_type": "username"
         *   }
         * }
         *
         * Response (200) - Invalid:
         * {
         *   "status": true,
         *   "message": "URL không hợp lệ.",
         *   "data": {
         *     "valid": false,
         *     "profile_id": null,
         *     "profile_type": null
         *   }
         * }
         */
        Route::get('check', [FacebookProfileController::class, 'check'])
            ->name('check');
    });

    // Debug route - Xem dữ liệu và mối quan hệ (Remove in production)
    Route::get('debug/data', function () {
        $services = \App\Models\Service::with('orders')->get();
        
        return response()->json([
            'services' => $services->map(function($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'price' => $service->price,
                    'price_formatted' => number_format($service->price) . ' VND',
                    'duration_days' => $service->duration_days,
                    'is_active' => $service->is_active,
                    'orders_count' => $service->orders->count(),
                    'orders' => $service->orders->map(function($order) {
                        return [
                            'order_code' => $order->order_code,
                            'amount' => $order->amount,
                            'status' => $order->status,
                            'expires_at' => $order->expires_at,
                            'paid_at' => $order->paid_at,
                        ];
                    }),
                ];
            }),
            'relationships' => [
                'service -> orders' => 'Service hasMany ServiceOrder',
                'service_order -> service' => 'ServiceOrder belongsTo Service',
            ]
        ]);
    })->name('debug.data');

    // API Documentation Routes
    Route::prefix('docs')->name('docs.')->group(function () {
        /**
         * GET /api/docs
         * Get API documentation in JSON format
         */
        Route::get('/', [ApiDocumentationController::class, 'index'])
            ->name('index');

        /**
         * GET /api/docs/html
         * Get API documentation as HTML
         */
        Route::get('html', [ApiDocumentationController::class, 'html'])
            ->name('html');

        /**
         * GET /api/docs/openapi
         * Get OpenAPI/Swagger JSON spec
         */
        Route::get('openapi', [ApiDocumentationController::class, 'openapi'])
            ->name('openapi');
    });

    /**
     * POST /api/v1/broadcasting/auth
     * Authorize access to private channels for Reverb (API-only, no CSRF)
     * 
     * Request Body:
     * {
     *   "socket_id": "socket123.456",
     *   "channel_name": "private-order.ORD-XXXXXXXXXX",
     *   "order_code": "ORD-XXXXXXXXXX" (optional)
     * }
     * 
     * Response (200) - Authorized:
     * {
     *   "authorized": true,
     *   "auth": "socket123.456:signature_hash"
     * }
     * 
     * Response (403) - Unauthorized:
     * {
     *   "authorized": false,
     *   "error": "Unauthorized message"
     * }
     */
    Route::post('broadcasting/auth', [\App\Http\Controllers\Api\BroadcastingAuthController::class, 'authorize'])
        ->name('broadcasting.auth');
});