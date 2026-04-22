<?php

namespace App\Services\API\General;

use App\Http\Resources\API\OfferResource;
use App\Models\Offer;
use App\Traits\ApiResponse;

class offerService
{
    use ApiResponse;
    public function getAllActiveOffer()
    {
        $offers = Offer::where('is_active',true)->with('product')->get();
        if ($offers->isEmpty()) {
           return [
            'status' => false,
            'message' => __('messages.offers_not_found'),
            'data' => []
           ];
        }
        return [
            'status' => true,
            'message' => __('messages.offers_retrieved_successfully'),
            'data' => OfferResource::collection($offers)
        ];
    }
}
