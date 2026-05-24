<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\AppNotification;
use App\Services\API\General\NotificationService;

class OrderObserver
{
    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        // Only run if the status was changed
        if ($order->wasChanged('status')) {
            $status = $order->status;

            if (in_array($status, ['accepted', 'cancelled'])) {
                try {
                    $customer = $order->user;
                    if ($customer) {
                        $customerLocale = $customer->locale ?? 'ar';

                        // Get the provider details through the order items
                        $firstItem = $order->items()->first();
                        $product = $firstItem ? $firstItem->product : null;
                        $provider = $product ? $product->provider : null;

                        $providerNameAr = $provider ? ($provider->title_ar ?? $provider->user?->name) : 'مقدم الخدمة';
                        $providerNameEn = $provider ? ($provider->title_en ?? $provider->user?->name) : 'the provider';

                        if ($status === 'accepted') {
                            $title = $customerLocale === 'ar' ? 'تم قبول طلبك! 🎉' : 'Order Accepted! 🎉';
                            $body = $customerLocale === 'ar' 
                                ? "يسعدنا إبلاغك بأن مقدم الخدمة «{$providerNameAr}» قد قبل طلبك رقم #{$order->order_number} وهو قيد التجهيز الآن."
                                : "We are happy to inform you that the provider «{$providerNameEn}» has accepted your order #{$order->order_number} and it is now in progress.";
                        } else {
                            $title = $customerLocale === 'ar' ? 'تم إلغاء الطلب 😔' : 'Order Cancelled 😔';
                            $body = $customerLocale === 'ar'
                                ? "نعتذر منك، لقد تم إلغاء طلبك رقم #{$order->order_number} من قبل مقدم الخدمة «{$providerNameAr}»."
                                : "We are sorry, your order #{$order->order_number} has been cancelled by the provider «{$providerNameEn}».";
                        }

                        // 1. Save in database
                        AppNotification::create([
                            'user_id' => $order->user_id,
                            'title' => $title,
                            'message' => $body,
                            'type' => 'order_status_updated',
                            'data' => [
                                'order_id' => (string) $order->id,
                                'status' => $status,
                            ],
                            'is_read' => false,
                        ]);

                        // 2. Send push notification if enabled
                        if ($customer->is_notify) {
                            $notificationService = app(NotificationService::class);
                            $notificationService->sendToUser($order->user_id, $title, $body, [
                                'type' => 'order_status_updated',
                                'order_id' => (string) $order->id,
                                'status' => $status,
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    // Fail silently to prevent interrupting application flow
                }
            }
        }
    }
}
