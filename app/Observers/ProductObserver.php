<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\User;
use App\Models\AppNotification;
use App\Models\UserFcmToken;
use App\Services\API\General\FirebaseNotificationService;

class ProductObserver
{
    /**
     * Determine if the observer events should be run after the database transaction is committed.
     *
     * @var bool
     */
    public $afterCommit = true;

    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        try {
            $product->load('provider.user');
            $provider = $product->provider;
            $providerUser = $provider ? $provider->user : null;
            $providerNameAr = $provider->title_ar ?? $providerUser?->name ?? 'مقدم خدمة';
            $providerNameEn = $provider->title_en ?? $providerUser?->name ?? 'Service Provider';

            $productNameAr = $product->name_ar;
            $productNameEn = $product->name_en ?? $productNameAr;

            $titleAr = 'إضافة منتج جديد في النظام! 📦';
            $titleEn = 'New Product Added to the System! 📦';

            $bodyAr = "قام مقدم الخدمة «{$providerNameAr}» بإضافة منتج جديد باسم «{$productNameAr}».";
            $bodyEn = "The service provider «{$providerNameEn}» has added a new product named «{$productNameEn}».";

            try {
                $actionUrl = \App\Filament\Resources\Products\ProductResource::getUrl('view', ['record' => $product->id]);
            } catch (\Throwable $e) {
                $actionUrl = url("/admin/products/{$product->id}");
            }

            $this->notifyAdmins($titleAr, $titleEn, $bodyAr, $bodyEn, $actionUrl, 'product_created', [
                'product_id' => (string) $product->id
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('ProductObserver created event error: ' . $e->getMessage());
        }
    }

    private function notifyAdmins(string $titleAr, string $titleEn, string $bodyAr, string $bodyEn, string $actionUrl, string $type, array $extraData = []): void
    {
        try {
            $admins = User::role(['admin', 'sub_admin', 'super_admin'])->get();

            foreach ($admins as $admin) {
                $userLocale = $admin->locale ?? 'ar';
                $pushTitle = $userLocale === 'ar' ? $titleAr : $titleEn;
                $pushBody = $userLocale === 'ar' ? $bodyAr : $bodyEn;

                // 1. Save standard Laravel database notification via Filament with a View Details action button
                \Filament\Notifications\Notification::make()
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
                    $tokens = UserFcmToken::where('user_id', $admin->id)->pluck('token')->toArray();
                    $firebaseService = app(FirebaseNotificationService::class);
                    foreach ($tokens as $token) {
                        try {
                            $firebaseService->sendToToken($token, $pushTitle, $pushBody, array_merge([
                                'type' => $type,
                            ], $extraData));
                        } catch (\Exception $e) {
                            // Fail silently
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            // Fail silently
        }
    }
}
