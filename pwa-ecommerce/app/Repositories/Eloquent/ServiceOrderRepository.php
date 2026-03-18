<?php

namespace App\Repositories\Eloquent;

use App\Models\ServiceOrder;
use App\Repositories\Interfaces\ServiceOrderRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ServiceOrderRepository implements ServiceOrderRepositoryInterface
{
    public function findByCode(string $orderCode): ?ServiceOrder
    {
        return ServiceOrder::with('service')
            ->where('order_code', $orderCode)
            ->first();
    }

    public function findByCodeForUpdate(string $orderCode): ?ServiceOrder
    {
        return ServiceOrder::with('service')
            ->where('order_code', $orderCode)
            ->lockForUpdate()
            ->first();
    }

    public function find(int $id): ?ServiceOrder
    {
        return ServiceOrder::with('service')
            ->find($id);
    }

    public function findByUser(int $userId): Collection
    {
        return ServiceOrder::with('service')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findActiveByUser(int $userId): Collection
    {
        return ServiceOrder::with('service')
            ->where('user_id', $userId)
            ->whereIn('status', [ServiceOrder::STATUS_PENDING])
            ->where('expires_at', '>', now())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function create(array $data): ServiceOrder
    {
        return ServiceOrder::create($data);
    }

    public function update(ServiceOrder $order, array $data): bool
    {
        return $order->update($data);
    }

    public function deleteExpiredPending(): int
    {
        return ServiceOrder::where('status', ServiceOrder::STATUS_PENDING)
            ->where('expires_at', '<', now())
            ->delete();
    }

    public function markAsExpired(ServiceOrder $order): bool
    {
        return $order->update([
            'status' => ServiceOrder::STATUS_EXPIRED,
        ]);
    }

    public function findByBankTxnId(string $bankTxnId): ?ServiceOrder
    {
        return ServiceOrder::where('bank_txn_id', $bankTxnId)->first();
    }

    public function findByBankTxnIdForUpdate(string $bankTxnId): ?ServiceOrder
    {
        return ServiceOrder::where('bank_txn_id', $bankTxnId)
            ->lockForUpdate()
            ->first();
    }
}
