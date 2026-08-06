<?php

namespace App\Observers;

use App\Models\Review;
use App\Models\User;
use App\Models\UserFcmToken;
use App\Services\API\General\FirebaseNotificationService;

class ReviewObserver
{
    /**
     * Handle the Review "created" event.
     */
    public function created(Review $review): void
    {
        try {
            $review->load(['user', 'product', 'service', 'provider.user']);

            $customerName = $review->user?->name ?? 'عميل';
            $rating = $review->rating;
            $comment = $review->comment ? "«{$review->comment}»" : 'بدون تعليق';
            $commentEn = $review->comment ? "«{$review->comment}»" : 'No comment';

            $titleAr = 'تقييم جديد! ⭐';
            $titleEn = 'New Review! ⭐';

            $bodyAr = '';
            $bodyEn = '';

            if ($review->product_id && $review->product) {
                $productName = $review->product->name_ar;
                $productNameEn = $review->product->name_en ?? $productName;

                $titleAr = 'تقييم جديد لمنتج! ⭐';
                $titleEn = 'New Product Review! ⭐';
                $bodyAr = "قام العميل «{$customerName}» بتقييم المنتج «{$productName}» بـ {$rating} نجوم. التعليق: {$comment}";
                $bodyEn = "Customer «{$customerName}» rated product «{$productNameEn}» with {$rating} stars. Comment: {$commentEn}";
            } elseif ($review->service_id && $review->service) {
                $serviceName = $review->service->service_ar;
                $serviceNameEn = $review->service->service_en ?? $serviceName;

                $titleAr = 'تقييم جديد لخدمة! ⭐';
                $titleEn = 'New Service Review! ⭐';
                $bodyAr = "قام العميل «{$customerName}» بتقييم الخدمة «{$serviceName}» بـ {$rating} نجوم. التعليق: {$comment}";
                $bodyEn = "Customer «{$customerName}» rated service «{$serviceNameEn}» with {$rating} stars. Comment: {$commentEn}";
            } elseif ($review->provider_id && $review->provider) {
                $providerName = $review->provider->title_ar ?? $review->provider->user?->name ?? 'مقدم خدمة';
                $providerNameEn = $review->provider->title_en ?? $review->provider->user?->name ?? 'Service Provider';

                $titleAr = 'تقييم جديد لمقدم الخدمة! ⭐';
                $titleEn = 'New Provider Review! ⭐';
                $bodyAr = "قام العميل «{$customerName}» بتقييم مقدم الخدمة «{$providerName}» بـ {$rating} نجوم. التعليق: {$comment}";
                $bodyEn = "Customer «{$customerName}» rated provider «{$providerNameEn}» with {$rating} stars. Comment: {$commentEn}";
            } else {
                // Review on the project/app itself
                $titleAr = 'تقييم جديد للمنصة! ⭐';
                $titleEn = 'New Platform Review! ⭐';
                $bodyAr = "قام العميل «{$customerName}» بتقييم التطبيق بـ {$rating} نجوم. التعليق: {$comment}";
                $bodyEn = "Customer «{$customerName}» rated the application with {$rating} stars. Comment: {$commentEn}";
            }

            $actionUrl = \App\Filament\Resources\Reviews\ReviewResource::getUrl('view', ['record' => $review->id]);

            $this->notifyAdmins($titleAr, $titleEn, $bodyAr, $bodyEn, $actionUrl, 'new_review', [
                'review_id' => (string) $review->id,
            ]);
        } catch (\Exception $e) {
            // Fail silently
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
                    ->id((string) \Illuminate\Support\Str::uuid())
                    ->title($pushTitle)
                    ->body($pushBody)
                    ->icon('heroicon-o-star')
                    ->iconColor('warning')
                    ->actions([
                        \Filament\Notifications\Actions\Action::make('view')
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
