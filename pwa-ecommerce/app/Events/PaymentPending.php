<?php

namespace App\Events;

use App\Models\ServiceOrder;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentPending implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public ServiceOrder $order) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('order.' . $this->order->order_code),
        ];
    }

    public function broadcastAs(): string
    {
        return 'payment.pending';
    }

    public function broadcastWith(): array
    {
        return [
            'order_code' => $this->order->order_code,
            'status' => $this->order->status,
            'amount' => $this->order->amount,
            'expires_at' => $this->order->expires_at?->toIso8601String(),
            'message' => 'Đơn hàng đang chờ thanh toán',
        ];
    }
}
