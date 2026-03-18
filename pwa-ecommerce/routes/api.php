<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiDocumentationController;
use App\Http\Controllers\Api\FacebookProfileController;
use App\Http\Controllers\Api\LicenseController;
use App\Http\Controllers\Api\PcInfoController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\PageController;

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
         * POST /api/v1/orders/verify-payment
         * Verify payment from TPBank webhook
         *
         * This endpoint is called by api_tpbank_free when a transaction is detected.
         * It verifies the device fingerprint before confirming payment.
         *
         * Request Body:
         * {
         *   "order_code": "ORDFBXXXXXXXXXX",
         *   "bank_txn_id": "TPB123456789"
         * }
         */
        Route::post('verify-payment', [OrderController::class, 'verifyPayment'])
            ->name('verify-payment');

        /**
         * GET /api/v1/orders/my-orders
         * Get current user's orders
         *
         * Requires authentication (sanctum)
         */
        Route::get('my-orders', [OrderController::class, 'myOrders'])
            ->name('my-orders')
            ->middleware('auth:sanctum');

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

    // Cart Routes
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/items', [CartController::class, 'addItem'])->name('items.add');
        Route::put('/items/{itemId}', [CartController::class, 'updateItem'])->name('items.update');
        Route::delete('/items/{itemId}', [CartController::class, 'removeItem'])->name('items.remove');
        Route::delete('/', [CartController::class, 'clear'])->name('clear');
        Route::get('/count', [CartController::class, 'count'])->name('count');
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
         * POST /api/v1/facebook-profiles/extract-uid
         * Extract real Facebook UID from a profile URL
         *
         * This endpoint fetches the actual Facebook profile page and extracts
         * the numeric UID using various patterns found in the HTML.
         *
         * Request Body:
         * {
         *   "facebook_profile_link": "https://facebook.com/username"
         * }
         *
         * Response (200) - UID found:
         * {
         *   "success": true,
         *   "message": "UID được trích xuất thành công.",
         *   "data": {
         *     "original_url": "https://facebook.com/username",
         *     "normalized_url": "https://www.facebook.com/username",
         *     "profile_id_from_url": "username",
         *     "uid": "100014343376569",
         *     "profile_info": {
         *       "username": "username",
         *       "facebook_url": "https://www.facebook.com/username",
         *       "profile_url": "https://www.facebook.com/username"
         *     }
         *   }
         * }
         *
         * Response (422) - UID not found or profile private:
         * {
         *   "success": false,
         *   "message": "Không tìm thấy UID trong trang...",
         *   "data": null
         * }
         */
        Route::post('extract-uid', [FacebookProfileController::class, 'extractUid'])
            ->name('extract-uid');

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
        $useExternalApi = config('services.service.use_external_api', true);
        
        $services = \App\Models\Service::with('orders')->get();
        $orders = \App\Models\ServiceOrder::with('service')->get();
        
        return response()->json([
            'config' => [
                'use_external_api' => $useExternalApi,
                'external_api_url' => config('services.external_api.base_url'),
            ],
            'local_services' => $services->map(function($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'price' => $service->price,
                    'price_formatted' => number_format($service->price) . ' VND',
                    'duration_days' => $service->duration_days,
                    'is_active' => $service->is_active,
                    'orders_count' => $service->orders->count(),
                ];
            }),
            'orders' => $orders->map(function($order) {
                return [
                    'order_code' => $order->order_code,
                    'amount' => $order->amount,
                    'status' => $order->status,
                    'expires_at' => $order->expires_at,
                    'paid_at' => $order->paid_at,
                    'service_data' => $order->service_data,
                    'has_external_service' => $order->hasExternalServiceData(),
                ];
            }),
            'relationships' => [
                'service -> orders' => 'Service hasMany ServiceOrder',
                'service_order -> service' => 'ServiceOrder belongsTo Service',
                'service_order -> service_data' => 'External API service data (JSON)',
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

    /**
     * GET /api/v1/debug/pusher-test
     * Test Pusher connection and verify credentials
     */
    Route::get('debug/pusher-test', function () {
        // Check if Pusher is configured
        $pusherConfigured = !empty(env('PUSHER_APP_KEY')) && !empty(env('PUSHER_APP_SECRET'));

        if (!$pusherConfigured) {
            return response()->json([
                'status' => false,
                'message' => 'Pusher chưa được cấu hình',
                'config' => [
                    'app_id' => env('PUSHER_APP_ID') ? '✓ Configured' : '✗ Not set',
                    'app_key' => env('PUSHER_APP_KEY') ? '✓ Configured' : '✗ Not set',
                    'app_secret' => env('PUSHER_APP_SECRET') ? '✓ Configured' : '✗ Not set',
                    'cluster' => env('PUSHER_APP_CLUSTER') ?: '✗ Not set',
                ],
            ]);
        }

        // Try to trigger a test event
        try {
            $pusher = new \Pusher\Pusher(
                env('PUSHER_APP_KEY'),
                env('PUSHER_APP_SECRET'),
                env('PUSHER_APP_ID'),
                [
                    'cluster' => env('PUSHER_APP_CLUSTER', 'ap1'),
                    'useTLS' => true,
                ]
            );

            // Test channel and event
            $testChannel = 'order.TEST_DEBUG';
            $testData = [
                'message' => 'Debug test - ' . now()->toIso8601String(),
                'status' => 'testing',
                'timestamp' => now()->toIso8601String(),
            ];

            // Trigger test event
            $result = $pusher->trigger($testChannel, 'order.status', $testData);

            return response()->json([
                'status' => true,
                'message' => 'Pusher kết nối thành công!',
                'config' => [
                    'app_id' => env('PUSHER_APP_ID'),
                    'app_key' => substr(env('PUSHER_APP_KEY'), 0, 8) . '...',
                    'cluster' => env('PUSHER_APP_CLUSTER', 'ap1'),
                ],
                'test' => [
                    'channel' => $testChannel,
                    'event' => 'order.status',
                    'result' => $result,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi kết nối Pusher: ' . $e->getMessage(),
                'config' => [
                    'app_id' => env('PUSHER_APP_ID'),
                    'app_key' => substr(env('PUSHER_APP_KEY'), 0, 8) . '...',
                    'cluster' => env('PUSHER_APP_CLUSTER', 'ap1'),
                ],
            ]);
        }
    })->name('debug.pusher-test');

    /**
     * POST /api/v1/debug/trigger-payment
     * Test trigger payment status event
     *
     * Security: Requires HMAC signature or order secret
     *
     * Request Body:
     * {
     *   "order_code": "ORD-XXXXXXXXXX",
     *   "status": "paid", // pending, paid, processing, expired
     *   "message": "Thanh toán thành công",
     *   "signature": "optional_hmac_signature" // Required if PUSHER_TRIGGER_SECRET is set
     * }
     */
    Route::post('debug/trigger-payment', function (\Illuminate\Http\Request $request) {
        $orderCode = $request->input('order_code');
        $status = $request->input('status', 'paid');
        $message = $request->input('message', 'Test payment status');
        $signature = $request->input('signature');

        if (empty($orderCode)) {
            return response()->json([
                'status' => false,
                'message' => 'order_code là bắt buộc',
            ], 422);
        }

        // ========== SECURITY: Verify order exists ==========
        $order = \App\Models\ServiceOrder::where('order_code', $orderCode)->first();

        if (!$order) {
            \Log::warning('[Trigger] Attempt to trigger non-existent order', [
                'order_code' => $orderCode,
                'ip' => $request->ip(),
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Đơn hàng không tồn tại.',
            ], 404);
        }

        // ========== SECURITY: Validate status transitions ==========
        // Only allow pending -> paid transition
        $validTransitions = [
            'pending' => ['paid', 'expired'],
            'paid' => [], // Cannot change from paid
            'expired' => [], // Cannot change from expired
        ];

        $currentStatus = $order->status;
        if (!isset($validTransitions[$currentStatus])) {
            return response()->json([
                'status' => false,
                'message' => 'Trạng thái đơn hàng không hợp lệ.',
            ], 400);
        }

        if (!in_array($status, $validTransitions[$currentStatus])) {
            \Log::warning('[Trigger] Invalid status transition attempt', [
                'order_code' => $orderCode,
                'current_status' => $currentStatus,
                'requested_status' => $status,
            ]);
            return response()->json([
                'status' => false,
                'message' => "Không thể chuyển trạng thái từ '{$currentStatus}' sang '{$status}'.",
            ], 400);
        }

        // ========== SECURITY: Check order expiration ==========
        if ($order->isExpired() && $status === 'paid') {
            return response()->json([
                'status' => false,
                'message' => 'Đơn hàng đã hết hạn, không thể thanh toán.',
            ], 400);
        }

        // ========== SECURITY: HMAC signature verification ==========
        $triggerSecret = env('PUSHER_TRIGGER_SECRET');

        if ($triggerSecret) {
            if (empty($signature)) {
                \Log::warning('[Trigger] Missing signature', [
                    'order_code' => $orderCode,
                    'ip' => $request->ip(),
                ]);
                return response()->json([
                    'status' => false,
                    'message' => 'Yêu cầu chữ ký xác thực.',
                    'requires_signature' => true,
                ], 401);
            }

            // Verify HMAC signature
            $expectedSignature = hash_hmac(
                'sha256',
                $orderCode . ':' . $status . ':' . $message,
                $triggerSecret
            );

            if (!hash_equals($expectedSignature, $signature)) {
                \Log::warning('[Trigger] Invalid signature attempt', [
                    'order_code' => $orderCode,
                    'ip' => $request->ip(),
                ]);
                return response()->json([
                    'status' => false,
                    'message' => 'Chữ ký xác thực không hợp lệ.',
                ], 401);
            }
        }

        try {
            $channelName = 'order.' . $orderCode;
            $eventData = [
                'status' => $status,
                'message' => $message,
                'order_code' => $orderCode,
                'timestamp' => now()->toIso8601String(),
                'verified' => true, // Indicates this event was verified by backend
            ];

            // Use PusherService for consistency
            $pusherService = app(\App\Services\PusherService::class);
            $result = $pusherService->notifyOrderStatus($orderCode, $status, $message, ['verified' => true]);

            \Log::info('[Trigger] Payment event triggered successfully', [
                'order_code' => $orderCode,
                'channel' => $channelName,
                'event' => 'order.status',
                'data' => $eventData,
                'result' => $result,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Đã gửi event thành công!',
                'data' => [
                    'channel' => $channelName,
                    'event' => 'order.status',
                    'payload' => $eventData,
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('[Trigger] Failed to trigger event', [
                'order_code' => $orderCode,
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Lỗi: ' . $e->getMessage(),
            ], 500);
        }
    })->name('debug.trigger-payment');

    /**
     * POST /api/v1/debug/generate-signature
     * Generate HMAC signature for trigger-payment request
     *
     * Request Body:
     * {
     *   "order_code": "ORD-XXXXXXXXXX",
     *   "status": "paid",
     *   "message": "Thanh toán thành công"
     * }
     *
     * Response:
     * {
     *   "signature": "abc123...",
     *   "expires_at": "2026-02-07T19:00:00+00:00"
     * }
     */
    Route::post('debug/generate-signature', function (\Illuminate\Http\Request $request) {
        $triggerSecret = env('PUSHER_TRIGGER_SECRET');

        if (!$triggerSecret) {
            return response()->json([
                'status' => false,
                'message' => 'PUSHER_TRIGGER_SECRET not configured in .env',
            ], 500);
        }

        $orderCode = $request->input('order_code');
        $status = $request->input('status', 'paid');
        $message = $request->input('message', 'Test payment status');

        if (empty($orderCode)) {
            return response()->json([
                'status' => false,
                'message' => 'order_code là bắt buộc',
            ], 422);
        }

        // Generate signature (simple HMAC without timestamp for testing)
        $signature = hash_hmac(
            'sha256',
            $orderCode . ':' . $status . ':' . $message,
            $triggerSecret
        );

        return response()->json([
            'signature' => $signature,
            'note' => 'Use this signature in trigger-payment request',
            'full_request' => [
                'order_code' => $orderCode,
                'status' => $status,
                'message' => $message,
                'signature' => $signature,
            ],
        ]);
    })->name('debug.generate-signature');

    // Authentication Routes (Public)
    Route::prefix('auth')->name('auth.')->group(function () {
        /**
         * POST /api/v1/auth/register
         * Register a new user account
         *
         * Request Body:
         * {
         *   "name": "Nguyen Van A",
         *   "email": "user@example.com",
         *   "password": "password123",
         *   "password_confirmation": "password123"
         * }
         *
         * Response (201):
         * {
         *   "status": true,
         *   "message": "Đăng ký thành công.",
         *   "data": {
         *     "user": {...},
         *     "token": "..."
         *   }
         * }
         */
        Route::post('register', [AuthController::class, 'register'])
            ->name('register');

        /**
         * POST /api/v1/auth/login
         * Login with email and password
         *
         * Request Body:
         * {
         *   "email": "user@example.com",
         *   "password": "password123"
         * }
         *
         * Response (200):
         * {
         *   "status": true,
         *   "message": "Đăng nhập thành công.",
         *   "data": {
         *     "user": {...},
         *     "token": "..."
         *   }
         * }
         */
        Route::post('login', [AuthController::class, 'login'])
            ->name('login');
    });

    // Authenticated Routes
    Route::prefix('auth')->name('auth.')->middleware('auth:sanctum')->group(function () {
        /**
         * POST /api/v1/auth/logout
         * Logout current user
         */
        Route::post('logout', [AuthController::class, 'logout'])
            ->name('logout');

        /**
         * GET /api/v1/auth/profile
         * Get current user profile
         */
        Route::get('profile', [AuthController::class, 'profile'])
            ->name('profile');

        /**
         * PUT /api/v1/auth/profile
         * Update user profile
         */
        Route::put('profile', [AuthController::class, 'updateProfile'])
            ->name('updateProfile');

        /**
         * DELETE /api/v1/auth/account
         * Delete user account (requires password confirmation)
         *
         * Request Body:
         * {
         *   "password": "user_current_password"
         * }
         *
         * Response (200):
         * {
         *   "status": true,
         *   "message": "Xóa tài khoản thành công."
         * }
         *
         * Response (422) - Wrong password:
         * {
         *   "status": false,
         *   "message": "Mật khẩu không đúng."
         * }
         */
        Route::delete('account', [AuthController::class, 'deleteAccount'])
            ->name('deleteAccount');
    });

    // Pages Routes (Public)
    Route::prefix('pages')->name('pages.')->group(function () {
        /**
         * GET /api/v1/pages/privacy
         * Get privacy policy page
         */
        Route::get('privacy', [PageController::class, 'privacy'])
            ->name('privacy');

        /**
         * GET /api/v1/pages/terms
         * Get terms of service page
         */
        Route::get('terms', [PageController::class, 'terms'])
            ->name('terms');

        /**
         * GET /api/v1/pages/{slug}
         * Get page by slug
         */
        Route::get('{slug}', [PageController::class, 'show'])
            ->name('show');
    });
});