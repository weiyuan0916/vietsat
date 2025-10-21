<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;

/**
 * Order Repository Interface
 */
interface OrderRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get user orders.
     */
    public function getUserOrders(int $userId, int $perPage = 15);

    /**
     * Get vendor orders.
     */
    public function getVendorOrders(int $vendorId, int $perPage = 15);

    /**
     * Get orders by status.
     */
    public function getOrdersByStatus(string $status, int $perPage = 15);

    /**
     * Get pending orders.
     */
    public function getPendingOrders(int $perPage = 15);

    /**
     * Update order status.
     */
    public function updateStatus(int $orderId, string $status): bool;

    /**
     * Generate order number.
     */
    public function generateOrderNumber(): string;
}

