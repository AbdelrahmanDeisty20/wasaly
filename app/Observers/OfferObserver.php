<?php

namespace App\Observers;

use App\Models\Offer;
use App\Models\User;
use App\Models\AppNotification;
use App\Models\UserFcmToken;
use App\Services\API\General\FirebaseNotificationService;

class OfferObserver
{
    protected $firebaseService;

    public function __construct(FirebaseNotificationService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Handle the Offer "created" event.
     */
    public function created(Offer $offer): void
    {
        // Load the product associated with this offer
        $product = $offer->product;
        if (!$product) {
            return;
        }

        // Fetch provider name
        $provider = $product->provider;
        $providerNameAr = $provider ? ($provider->title_ar ?? $provider->user?->name) : 'أحد مقدمي الخدمة';
        $providerNameEn = $provider ? ($provider->title_en ?? $provider->user?->name) : 'one of our providers';

        // Prepare the notification details
        $discount = $offer->discount_percentage;
        $titleAr = "فرصة ذهبية! عرض جديد 🔥";
        $titleEn = "Golden Deal! New Offer 🔥";
        $bodyAr = "لقد أضاف مقدم الخدمة «{$providerNameAr}» عرضاً رائعاً بخصم {$discount}% على منتجه «{$product->name_ar}»! تسوق الآن واستمتع بالخصم.";
        $bodyEn = "The provider «{$providerNameEn}» has added an amazing deal of {$discount}% off on their product «{$product->name_en}»! Shop now and enjoy the discount.";

        $data = [
            'type' => 'new_offer',
            'product_id' => (string) $product->id,
            'offer_id' => (string) $offer->id,
            'discount_percentage' => (string) $discount,
        ];

        // 1. Send push notifications to all guests (tokens without user_id)
        $guestTokens = UserFcmToken::whereNull('user_id')->pluck('token')->toArray();
        foreach ($guestTokens as $token) {
            try {
                $this->firebaseService->sendToToken(
                    $token,
                    app()->getLocale() == 'ar' ? $titleAr : $titleEn,
                    app()->getLocale() == 'ar' ? $bodyAr : $bodyEn,
                    $data
                );
            } catch (\Exception $e) {
                // Keep moving to other tokens even if one fails
            }
        }

        // 2. Send push notifications to all users (tokens with user_id) AND save it to app_notifications
        $users = User::all();
        foreach ($users as $user) {
            $userLocale = $user->locale ?? 'ar';
            // Save in database
            try {
                AppNotification::create([
                    'user_id' => $user->id,
                    'title' => $userLocale == 'ar' ? $titleAr : $titleEn,
                    'message' => $userLocale == 'ar' ? $bodyAr : $bodyEn,
                    'type' => 'new_offer',
                    'data' => $data,
                    'is_read' => false,
                ]);
            } catch (\Exception $e) {
                // Ignore DB insertion errors and continue
            }

            // Send push to user's tokens if their notification is enabled
            if ($user->is_notify) {
                $userTokens = UserFcmToken::where('user_id', $user->id)->pluck('token')->toArray();
                foreach ($userTokens as $token) {
                    try {
                        $this->firebaseService->sendToToken(
                            $token,
                            $userLocale == 'ar' ? $titleAr : $titleEn,
                            $userLocale == 'ar' ? $bodyAr : $bodyEn,
                            $data
                        );
                    } catch (\Exception $e) {
                        // Continue sending
                    }
                }
            }
        }
    }
}
