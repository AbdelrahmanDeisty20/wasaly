<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\AppNotification;
use App\Services\API\General\NotificationService;

class OrderObserver
{
    /**
     * Determine if the observer events should be run after the database transaction is committed.
     *
     * @var bool
     */
    public $afterCommit = true;

    /**
     * Handle the Order "updating" event.
     */
    public function updating(\App\Models\Order $order): void
    {
        $order->tempOriginalStatus = $order->getOriginal('status');
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        $oldStatus = $order->tempOriginalStatus ?? $order->getOriginal('status');
        // Only run if the status was changed
        if ($oldStatus !== $order->status) {
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

                        // Generate bilingual notifications
                        if ($status === 'accepted') {
                            $titleAr = 'تم قبول طلبك! 🎉';
                            $bodyAr = "يسعدنا إبلاغك بأن مقدم الخدمة «{$providerNameAr}» قد قبل طلبك رقم #{$order->order_number} وهو قيد التجهيز الآن.";
                            
                            $titleEn = 'Order Accepted! 🎉';
                            $bodyEn = "We are happy to inform you that the provider «{$providerNameEn}» has accepted your order #{$order->order_number} and it is now in progress.";
                        } else {
                            $titleAr = 'تم إلغاء الطلب 😔';
                            $bodyAr = "نعتذر منك، لقد تم إلغاء طلبك رقم #{$order->order_number} من قبل مقدم الخدمة «{$providerNameAr}».";
                            
                            $titleEn = 'Order Cancelled 😔';
                            $bodyEn = "We are sorry, your order #{$order->order_number} has been cancelled by the provider «{$providerNameEn}».";
                        }

                        $pushTitle = $customerLocale === 'ar' ? $titleAr : $titleEn;
                        $pushBody = $customerLocale === 'ar' ? $bodyAr : $bodyEn;

                        // 1. Save in database (with bilingual translation support)
                        AppNotification::create([
                            'user_id' => $order->user_id,
                            'title_ar' => $titleAr,
                            'title_en' => $titleEn,
                            'message_ar' => $bodyAr,
                            'message_en' => $bodyEn,
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
                            $notificationService->sendToUser($order->user_id, $pushTitle, $pushBody, [
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
                    $titleAr = 'طلب جديد وارد! 📥';
                    $titleEn = 'New Order Received! 📥';
                    
                    $bodyAr = "لقد تلقيت طلباً جديداً رقم #{$order->order_number} بقيمة {$order->total_price} ج.م. تفقد تفاصيل الطلب الآن.";
                    $bodyEn = "You have received a new order #{$order->order_number} with a total of {$order->total_price} EGP. Check the order details now.";

                    $pushTitle = $providerLocale === 'ar' ? $titleAr : $titleEn;
                    $pushBody = $providerLocale === 'ar' ? $bodyAr : $bodyEn;

                    // 1. Save database notification for the provider user (with bilingual translation support)
                    AppNotification::create([
                        'user_id' => $providerUser->id,
                        'title_ar' => $titleAr,
                        'title_en' => $titleEn,
                        'message_ar' => $bodyAr,
                        'message_en' => $bodyEn,
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
                        $notificationService->sendToUser($providerUser->id, $pushTitle, $pushBody, [
                            'type' => 'new_order_received',
                            'order_id' => (string) $order->id,
                            'order_number' => (string) $order->order_number,
                        ]);
                    }
                }
            }

            // 3. Notify Admins
            $adminTitleAr = 'طلب جديد في النظام! 📦';
            $adminTitleEn = 'New Order in the System! 📦';
            
            $adminBodyAr = "تمت إضافة طلب جديد رقم #{$order->order_number} بقيمة {$order->total_price} ج.م.";
            $adminBodyEn = "A new order #{$order->order_number} has been placed with a total of {$order->total_price} EGP.";

            try {
                $actionUrl = \App\Filament\Resources\Orders\OrderResource::getUrl('view', ['record' => $order->id]);
            } catch (\Throwable $e) {
                $actionUrl = url("/admin/orders/{$order->id}");
            }

            $this->notifyAdmins($adminTitleAr, $adminTitleEn, $adminBodyAr, $adminBodyEn, $actionUrl, 'system_new_order', [
                'order_id' => (string) $order->id,
                'order_number' => (string) $order->order_number,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('OrderObserver created event error: ' . $e->getMessage());
        }
    }

    private function notifyAdmins(string $titleAr, string $titleEn, string $bodyAr, string $bodyEn, string $actionUrl, string $type, array $extraData = []): void
    {
        try {
            $admins = \App\Models\User::role(['admin', 'sub_admin', 'super_admin'])->get();

            foreach ($admins as $admin) {
                $userLocale = $admin->locale ?? 'ar';
                $pushTitle = $userLocale === 'ar' ? $titleAr : $titleEn;
                $pushBody = $userLocale === 'ar' ? $bodyAr : $bodyEn;

                // 1. Save standard Laravel database notification via Filament with a View Details action button
                \Filament\Notifications\Notification::make()
                    ->id((string) \Illuminate\Support\Str::uuid())
                    ->title($pushTitle)
                    ->body($pushBody)
                    ->icon('heroicon-o-bell')
                    ->iconColor('success')
                    ->actions([
                        \Filament\Actions\Action::make('view')
                            ->label($userLocale === 'ar' ? 'عرض التفاصيل' : 'View Details')
                            ->url($actionUrl)
                            ->markAsRead(),
                    ])
                    ->sendToDatabase($admin);

                // 2. Dispatch FCM push notification
                if ($admin->is_notify) {
                    $tokens = \App\Models\UserFcmToken::where('user_id', $admin->id)->pluck('token')->toArray();
                    $firebaseService = app(\App\Services\API\General\FirebaseNotificationService::class);
                    foreach ($tokens as $token) {
                        try {
                            $firebaseService->sendToToken($token, $pushTitle, $pushBody, array_merge([
                                'type' => $type,
                            ], $extraData));
                        } catch (\Exception $e) {
                            // Fail silently for FCM
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('OrderObserver notifyAdmins error: ' . $e->getMessage());
        }
    }
}
