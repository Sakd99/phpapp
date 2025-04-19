<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;

class OrderStatusNotification extends Notification
{
    use Queueable;

    protected $order;
    protected $message;

    public function __construct($order, $message)
    {
        $this->order = $order;
        $this->message = $message;

        // سجل إنشاء الإشعار
        Log::info('Creating notification for order #' . $order->order_number);
    }

    public function via($notifiable)
    {
        // سجل القنوات التي سيتم إرسال الإشعار من خلالها
        Log::info('Sending notification via OneSignal');

        return [OneSignalChannel::class];
    }

    public function toOneSignal($notifiable)
    {
        Log::info('Preparing OneSignal notification for device token: ' . $notifiable->routeNotificationForOneSignal());

        return OneSignalMessage::create()
            ->setSubject("تحديث حالة الطلب #" . $this->order->order_number)
            ->setBody($this->message)
            ->setUrl(''); // استخدم سلسلة نصية فارغة بدلاً من null
    }


    public function failed(\Exception $exception)
    {
        // سجل أي خطأ في إرسال الإشعار
        Log::error('Failed to send OneSignal notification: ' . $exception->getMessage());
    }
}
