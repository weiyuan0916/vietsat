<?php

namespace App\Repositories\Eloquent;

use App\Models\Order;
use App\Repositories\Interfaces\OrderRepositoryInterface;

/**
 * Order Repository
 */
class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    /**
     * OrderRepository constructor.
     */
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }

    /**
     * Get user orders.
     */
    public function getUserOrders(int $userId, int $perPage = 15)
    {
        return $this->model->where('user_id', $userId)
            ->with(['items.product', 'vendor'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get vendor orders.
     */
    public function getVendorOrders(int $vendorId, int $perPage = 15)
    {
        return $this->model->where('vendor_id', $vendorId)
            ->with(['items.product', 'user'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get orders by status.
     */
    public function getOrdersByStatus(string $status, int $perPage = 15)
    {
        return $this->model->where('status', $status)
            ->with(['items.product', 'user', 'vendor'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get pending orders.
     */
    public function getPendingOrders(int $perPage = 15)
    {
        return $this->model->pending()
            ->with(['items.product', 'user', 'vendor'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Update order status.
     */
    public function updateStatus(int $orderId, string $status): bool
    {
        $order = $this->find($orderId);
        
        if (!$order) {
            return false;
        }

        $updateData = ['status' => $status];

        // Update timestamp based on status
        switch ($status) {
            case 'confirmed':
                $updateData['confirmed_at'] = now();
                break;
            case 'shipped':
                $updateData['shipped_at'] = now();
                break;
            case 'delivered':
                $updateData['delivered_at'] = now();
                break;
            case 'cancelled':
                $updateData['cancelled_at'] = now();
                break;
        }

        return $order->update($updateData);
    }

    /**
     * Generate order number.
     */
    public function generateOrderNumber(): string
    {
        return Order::generateOrderNumber();
    }
}

