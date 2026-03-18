<?php

namespace App\Repositories\Interfaces;

use App\Models\ServiceOrder;
use Illuminate\Database\Eloquent\Collection;

/**
 * Service Order Repository Interface
 *
 * Defines methods for ServiceOrder data access
 */
interface ServiceOrderRepositoryInterface
{
    /**
     * Find order by order code.
     */
    public function findByCode(string $orderCode): ?ServiceOrder;

    /**
     * Find order by order code with lock for update.
     */
    public function findByCodeForUpdate(string $orderCode): ?ServiceOrder;

    /**
     * Find order by ID.
     */
    public function find(int $id): ?ServiceOrder;

    /**
     * Get all orders for a user.
     */
    public function findByUser(int $userId): Collection;

    /**
     * Get active (non-expired, non-paid) orders for a user.
     */
    public function findActiveByUser(int $userId): Collection;

    /**
     * Create a new order.
     */
    public function create(array $data): ServiceOrder;

    /**
     * Update an order.
     */
    public function update(ServiceOrder $order, array $data): bool;

    /**
     * Delete expired pending orders.
     */
    public function deleteExpiredPending(): int;

    /**
     * Mark order as expired.
     */
    public function markAsExpired(ServiceOrder $order): bool;

    /**
     * Find order by bank transaction ID.
     */
    public function findByBankTxnId(string $bankTxnId): ?ServiceOrder;

    /**
     * Find order by bank transaction ID with lock for update.
     */
    public function findByBankTxnIdForUpdate(string $bankTxnId): ?ServiceOrder;
}
