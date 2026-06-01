<?php

namespace App\Observers;

use App\Models\Offer;
use App\Models\AppNotification;
use App\Services\API\General\NotificationService;

class OfferObserver
{
    /**
     * Handle the Offer "created" event.
     */
    public function created(Offer $offer): void
    {
        $notificationService = app(NotificationService::class);
        
        // Load the product associated with this offer
        $product = $offer->product;
        if (!$product) {
            return;
        }

        // Fetch provider
        $provider = $product->provider;

        // Prepare the notification details
        $discount = $offer->discount_percentage;
        $titleAr = "فرصة ذهبية! عرض جديد 🔥";
        $titleEn = "Golden Deal! New Offer 🔥";

        if ($provider) {
            $providerNameAr = $provider->title_ar ?? $provider->user?->name ?? 'مقدم الخدمة';
            $providerNameEn = $provider->title_en ?? $provider->user?->name ?? 'the provider';

            $bodyAr = "لقد أضاف مقدم الخدمة «{$providerNameAr}» عرضاً رائعاً بخصم {$discount}% على منتجه «{$product->name_ar}»! تسوق الآن واستمتع بالخصم.";
            $bodyEn = "The provider «{$providerNameEn}» has added an amazing deal of {$discount}% off on their product «{$product->name_en}»! Shop now and enjoy the discount.";
        } else {
            $bodyAr = "تمت إضافة عرض جديد رائع بخصم {$discount}% على منتج «{$product->name_ar}»! تسوق الآن واستمتع بالخصم المميز.";
            $bodyEn = "A great new offer of {$discount}% off has been added on «{$product->name_en}»! Shop now and enjoy the discount.";
        }

        $title = app()->getLocale() == 'ar' ? $titleAr : $titleEn;
        $body = app()->getLocale() == 'ar' ? $bodyAr : $bodyEn;

        $data = [
            'type' => 'new_offer',
            'product_id' => (string) $product->id,
            'offer_id' => (string) $offer->id,
            'discount_percentage' => (string) $discount,
        ];

        // 1. Find users who have this product in their Cart
        $cartUserIds = \App\Models\Cart::whereHas('items', function ($query) use ($product) {
            $query->where('product_id', $product->id);
        })
        ->whereNotNull('user_id')
        ->pluck('user_id')
        ->toArray();

        // 2. Find users who have this product in their Favorites
        $favoriteUserIds = \App\Models\Favorite::where('product_id', $product->id)
            ->whereNotNull('user_id')
            ->pluck('user_id')
            ->toArray();

        // Exclude Cart users from Favorites users
        $favoriteOnlyUserIds = array_diff($favoriteUserIds, $cartUserIds);

        // Keep track of notified users to exclude them from the generic broadcast
        $notifiedUserIds = [];

        // --- A. Process Cart Notifications ---
        if (!empty($cartUserIds)) {
            $users = \App\Models\User::whereIn('id', $cartUserIds)->get();
            foreach ($users as $user) {
                $userLocale = $user->locale ?? 'ar';
                $cartTitleAr = 'خصم على منتج في سلتك! 🛒🔥';
                $cartTitleEn = 'Discount on a product in your cart! 🛒🔥';
                
                $cartBodyAr = "المنتج «{$product->name_ar}» الموجود في سلتك أصبح عليه خصم بقيمة {$discount}%! سارع بطلب السلة الآن قبل انتهاء العرض!";
                $cartBodyEn = "The product «{$product->name_en}» in your cart has a new {$discount}% discount! Complete your order now before the offer ends!";

                $cartTitle = $userLocale === 'ar' ? $cartTitleAr : $cartTitleEn;
                $cartBody = $userLocale === 'ar' ? $cartBodyAr : $cartBodyEn;

                try {
                    // Save to database specifically for this user (with bilingual translation support)
                    AppNotification::create([
                        'user_id' => $user->id,
                        'title_ar' => $cartTitleAr,
                        'title_en' => $cartTitleEn,
                        'message_ar' => $cartBodyAr,
                        'message_en' => $cartBodyEn,
                        'type' => 'cart_offer_discount',
                        'data' => $data,
                        'is_read' => false,
                    ]);

                    // Send push notification if enabled
                    if ($user->is_notify) {
                        $notificationService->sendToUser($user->id, $cartTitle, $cartBody, $data);
                    }
                    $notifiedUserIds[] = $user->id;
                } catch (\Exception $e) {
                    // Ignore individual notification failure
                }
            }
        }

        // --- B. Process Favorite Notifications ---
        if (!empty($favoriteOnlyUserIds)) {
            $users = \App\Models\User::whereIn('id', $favoriteOnlyUserIds)->get();
            foreach ($users as $user) {
                $userLocale = $user->locale ?? 'ar';
                $favTitleAr = 'بشرى سارة لمنتجك المفضل! ❤️🔥';
                $favTitleEn = 'Great news for your favorite product! ❤️🔥';
                
                $favBodyAr = "بشرى سارة! منتجك المفضل «{$product->name_ar}» أصبح عليه خصم بقيمة {$discount}%! أضفه إلى السلة الآن!";
                $favBodyEn = "Great news! Your favorite product «{$product->name_en}» has a new {$discount}% discount! Add it to your cart now!";

                $favTitle = $userLocale === 'ar' ? $favTitleAr : $favTitleEn;
                $favBody = $userLocale === 'ar' ? $favBodyAr : $favBodyEn;

                try {
                    // Save to database specifically for this user (with bilingual translation support)
                    AppNotification::create([
                        'user_id' => $user->id,
                        'title_ar' => $favTitleAr,
                        'title_en' => $favTitleEn,
                        'message_ar' => $favBodyAr,
                        'message_en' => $favBodyEn,
                        'type' => 'favorite_offer_discount',
                        'data' => $data,
                        'is_read' => false,
                    ]);

                    // Send push notification if enabled
                    if ($user->is_notify) {
                        $notificationService->sendToUser($user->id, $favTitle, $favBody, $data);
                    }
                    $notifiedUserIds[] = $user->id;
                } catch (\Exception $e) {
                    // Ignore individual notification failure
                }
            }
        }

        // 3. Broadcast generic push notifications to all other tokens (guests and registered users who didn't get specific ones)
        try {
            $notificationService->broadcastNotification($title, $body, $data, $notifiedUserIds);
        } catch (\Exception $e) {
            // Keep moving even if broadcasting throws an exception
        }

        // 4. Save a single broadcast notification to database for guests/others to see dynamically
        try {
            AppNotification::create([
                'user_id' => null, // Broadcast for everyone else
                'title_ar' => $titleAr,
                'title_en' => $titleEn,
                'message_ar' => $bodyAr,
                'message_en' => $bodyEn,
                'type' => 'new_offer',
                'data' => $data,
                'is_read' => false,
            ]);
        } catch (\Exception $e) {
            // Ignore DB insertion errors
        }
    }
}
