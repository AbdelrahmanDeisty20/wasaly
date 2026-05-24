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

        $title = app()->getLocale() == 'ar' ? $titleAr : $titleEn;
        $body = app()->getLocale() == 'ar' ? $bodyAr : $bodyEn;

        $data = [
            'type' => 'new_offer',
            'product_id' => (string) $product->id,
            'offer_id' => (string) $offer->id,
            'discount_percentage' => (string) $discount,
        ];

        // 1. Broadcast push notifications to all tokens (guests and registered users)
        try {
            $notificationService->broadcastNotification($title, $body, $data);
        } catch (\Exception $e) {
            // Keep moving even if broadcasting throws an exception
        }

        // 2. Save a single broadcast notification to database
        try {
            AppNotification::create([
                'user_id' => null, // Broadcast for everyone
                'title' => $title,
                'message' => $body,
                'type' => 'new_offer',
                'data' => $data,
                'is_read' => false,
            ]);
        } catch (\Exception $e) {
            // Ignore DB insertion errors
        }
    }
}
