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

    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        try {
            // Load the items, products, providers and their user accounts
            $order->load('items.product.provider.user');

            // Collect unique providers associated with the order items
            $providers = [];
            foreach ($order->items as $item) {
                $product = $item->product;
                if ($product && $product->provider) {
                    $providers[$product->provider->id] = $product->provider;
                }
            }

            // Send notification to each unique provider
            foreach ($providers as $provider) {
                $providerUser = $provider->user;
                if ($providerUser) {
                    $providerLocale = $providerUser->locale ?? 'ar';

                    // Personalized localized notification details
                    $title = $providerLocale === 'ar' ? 'طلب جديد وارد! 📥' : 'New Order Received! 📥';
                    $body = $providerLocale === 'ar'
                        ? "لقد تلقيت طلباً جديداً رقم #{$order->order_number} بقيمة {$order->total_price} ج.م. تفقد تفاصيل الطلب الآن."
                        : "You have received a new order #{$order->order_number} with a total of {$order->total_price} EGP. Check the order details now.";

                    // 1. Save database notification for the provider user
                    AppNotification::create([
                        'user_id' => $providerUser->id,
                        'title' => $title,
                        'message' => $body,
                        'type' => 'new_order_received',
                        'data' => [
                            'order_id' => (string) $order->id,
                            'order_number' => (string) $order->order_number,
                        ],
                        'is_read' => false,
                    ]);

                    // 2. Send push notification if provider user has enabled notifications
                    if ($providerUser->is_notify) {
                        $notificationService = app(NotificationService::class);
                        $notificationService->sendToUser($providerUser->id, $title, $body, [
                            'type' => 'new_order_received',
                            'order_id' => (string) $order->id,
                            'order_number' => (string) $order->order_number,
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            // Fail silently to prevent interrupting application flow
        }
    }
}
