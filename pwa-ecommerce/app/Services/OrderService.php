<?php

namespace App\Services;

use App\Events\PaymentExpired;
use App\Events\PaymentPending;
use App\Events\PaymentSuccess;
use App\Models\Service;
use App\Models\ServiceOrder;
use App\Repositories\Interfaces\ServiceOrderRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderService
{
    private ExternalServiceApi $externalServiceApi;
    private DeviceTrackingService $deviceTrackingService;
    private ServiceOrderRepositoryInterface $repository;

    public function __construct(
        ExternalServiceApi $externalServiceApi,
        DeviceTrackingService $deviceTrackingService,
        ServiceOrderRepositoryInterface $repository
    ) {
        $this->externalServiceApi = $externalServiceApi;
        $this->deviceTrackingService = $deviceTrackingService;
        $this->repository = $repository;
    }

    public function createOrder(
        string $facebookProfileLink,
        ?int $serviceId,
        ?int $userId,
        Request $request
    ): array {
        $serviceData = null;
        $localServiceId = null;

        if ($this->shouldUseExternalApi()) {
            if ($serviceId) {
                $serviceData = $this->externalServiceApi->getServiceById($serviceId);
            } else {
                $serviceData = $this->externalServiceApi->getDefaultService();
            }

            if (!$serviceData) {
                $localService = $this->resolveLocalService($serviceId);

                if ($localService) {
                    $localServiceId = $localService->id;
                    $serviceData = [
                        'id' => $localService->id,
                        'name' => $localService->name,
                        'duration_days' => $localService->duration_days,
                        'price' => $localService->price,
                    ];
                }
            }
        }

        if (!$serviceData) {
            $localService = $this->resolveLocalService($serviceId);

            if (!$localService || !$localService->is_active) {
                return [
                    'success' => false,
                    'error' => 'service_not_found',
                    'message' => 'Dịch vụ không hợp lệ hoặc đã ngừng hoạt động.',
                ];
            }

            $localServiceId = $localService->id;
            $serviceData = [
                'id' => $localService->id,
                'name' => $localService->name,
                'duration_days' => $localService->duration_days,
                'price' => $localService->price,
            ];
        }

        $amount = $serviceData['price'];

        return DB::transaction(function () use ($request, $serviceData, $localServiceId, $amount, $facebookProfileLink, $userId) {
            $this->cleanupExpiredPendingOrders();

            $deviceFingerprint = $this->deviceTrackingService->generateFingerprint($request);

            $orderData = [
                'order_code' => 'ORDFB' . Str::upper(Str::random(10)),
                'amount' => $amount,
                'status' => ServiceOrder::STATUS_PENDING,
                'expires_at' => now()->addMinutes(5),
                'facebook_profile_link' => $facebookProfileLink,
                'device_fingerprint' => $deviceFingerprint,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ];

            if ($userId) {
                $orderData['user_id'] = $userId;
            }

            if ($localServiceId === null) {
                $orderData['service_data'] = $serviceData;
                $orderData['service_id'] = null;
            } else {
                $orderData['service_id'] = $localServiceId;
            }

            $order = $this->repository->create($orderData);

            event(new PaymentPending($order));

            return [
                'success' => true,
                'data' => [
                    'order_code' => $order->order_code,
                    'amount' => $order->amount,
                    'expires_at' => $order->expires_at->toIso8601String(),
                    'qr_content' => 'bank:' . $order->order_code . ':' . $order->amount,
                    'status' => $order->status,
                    'service' => [
                        'id' => $serviceData['id'],
                        'name' => $serviceData['name'],
                        'duration_days' => $serviceData['duration_days'],
                    ],
                ],
            ];
        });
    }

    public function verifyPayment(string $orderCode, string $bankTxnId): array
    {
        try {
            return DB::transaction(function () use ($orderCode, $bankTxnId) {
                $order = $this->findOrderByCodeWithLock($orderCode);

                if (!$order) {
                    return [
                        'success' => false,
                        'error' => 'order_not_found',
                        'message' => 'Không tìm thấy đơn hàng.',
                    ];
                }

                if ($order->status === ServiceOrder::STATUS_PAID) {
                    return [
                        'success' => false,
                        'error' => 'already_paid',
                        'message' => 'Đơn hàng đã được thanh toán.',
                    ];
                }

                if ($order->isTimeExpired()) {
                    $this->expireOrderIfNeeded($order);
                    return [
                        'success' => false,
                        'error' => 'order_expired',
                        'message' => 'Đơn hàng đã hết hạn.',
                    ];
                }

                $existingOrderWithTxn = $this->repository->findByBankTxnIdForUpdate($bankTxnId);
                if ($existingOrderWithTxn) {
                    return [
                        'success' => false,
                        'error' => 'duplicate_transaction',
                        'message' => 'Giao dịch này đã được xử lý.',
                    ];
                }

                $order->update([
                    'status' => ServiceOrder::STATUS_PAID,
                    'paid_at' => now(),
                    'bank_txn_id' => $bankTxnId,
                ]);

                event(new PaymentSuccess($order));

                return [
                    'success' => true,
                    'data' => [
                        'order_code' => $order->order_code,
                        'status' => $order->status,
                        'paid_at' => $order->paid_at->toIso8601String(),
                    ],
                ];
            });
        } catch (\Exception $e) {
            Log::error('Order verifyPayment transaction failed', [
                'order_code' => $orderCode,
                'bank_txn_id' => $bankTxnId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'transaction_failed',
                'message' => 'Có lỗi xảy ra khi xác nhận thanh toán.',
            ];
        }
    }

    public function getOrder(string $orderCode): ?ServiceOrder
    {
        $order = $this->findOrderByCode($orderCode);

        if ($order) {
            $this->expireOrderIfNeeded($order);
            $order->refresh();
        }

        return $order;
    }

    public function getUserOrders(int $userId, bool $withExpired = false): array
    {
        $orders = $this->repository->findByUser($userId);

        $orders->each(function (ServiceOrder $order) {
            $this->expireOrderIfNeeded($order);
        });
        $orders->each->refresh();

        return $orders->map(function (ServiceOrder $order) {
            return [
                'order_code' => $order->order_code,
                'amount' => $order->amount,
                'status' => $order->status,
                'facebook_profile_link' => $order->facebook_profile_link,
                'expires_at' => $order->expires_at?->toIso8601String(),
                'paid_at' => $order->paid_at?->toIso8601String(),
                'created_at' => $order->created_at->toIso8601String(),
                'service' => $order->getServiceInfo(),
            ];
        })->toArray();
    }

    public function expireOrderIfNeeded(ServiceOrder $order): bool
    {
        if (!$order->isTimeExpired()) {
            return false;
        }

        if (!$order->markExpired()) {
            return false;
        }

        event(new PaymentExpired($order));

        return true;
    }

    private function findOrderByCode(string $orderCode): ?ServiceOrder
    {
        return $this->repository->findByCode($orderCode);
    }

    private function findOrderByCodeWithLock(string $orderCode): ?ServiceOrder
    {
        return $this->repository->findByCodeForUpdate($orderCode);
    }

    private function cleanupExpiredPendingOrders(): void
    {
        $this->repository->deleteExpiredPending();
    }

    private function resolveLocalService(?int $serviceId): ?Service
    {
        if (!$serviceId) {
            return Service::where('is_active', true)->first();
        }

        return Service::where('id', $serviceId)
            ->where('is_active', true)
            ->first();
    }

    private function shouldUseExternalApi(): bool
    {
        if (!config('services.service.use_external_api', false)) {
            return false;
        }

        $baseUrl = rtrim((string) config('services.external_api.base_url', ''), '/');
        $currentApiBaseUrl = rtrim(url('/api/v1'), '/');

        if ($baseUrl === '' || $baseUrl === $currentApiBaseUrl) {
            return false;
        }

        return true;
    }
}
