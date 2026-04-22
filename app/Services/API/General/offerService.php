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
        $offers = Offer::active()->with('product')->get();
        if ($offers->isEmpty()) {
            return $this->error(__('messages.offers_not_found'), 404);
        }
        return $this->success(OfferResource::collection($offers), __('messages.offers_found'), 200);
    }
}
