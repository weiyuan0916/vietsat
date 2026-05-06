<?php

namespace App\Notifications;

use App\Models\ServiceOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class PaymentReceivedTestNotification extends Notification
{
    use Queueable;

    public function __construct(public ServiceOrder $order) {}

    public function via(object $notifiable): array
    {
        return [WebPushChannel::class];
    }

    public function toWebPush(object $notifiable, Notification $notification): WebPushMessage
    {
        $icon = url('/app/img/cards/1.jpg');

        return (new WebPushMessage)
            ->title('Thanh toán thành công')
            ->body('Đơn ' . $this->order->order_code . ' đã được xác nhận.')
            ->icon($icon)
            ->data([
                'url' => url('/app/#!/service/'),
                'order_code' => $this->order->order_code,
            ]);
    }
}
