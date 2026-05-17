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
        $offers = Offer::where('is_active',true)->with('product.offers','product.reviews')->paginate(10);
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
            'data' => $offers
        ];
    }

    public function getProviderOffers()
    {
        $user = auth()->user();
        $provider = \App\Models\Provider::where('user_id', $user->id)->first();
        
        if (!$provider) {
            return ['status' => false, 'message' => __('messages.provider_not_found'), 'data' => []];
        }

        $offers = Offer::whereHas('product', function($q) use ($provider) {
            $q->where('provider_id', $provider->id);
        })->with('product.offers', 'product.reviews')->paginate(10);

        if ($offers->isEmpty()) {
            return ['status' => true, 'message' => __('messages.offers_not_found'), 'data' => $offers];
        }

        return ['status' => true, 'message' => __('messages.offers_retrieved_successfully'), 'data' => $offers];
    }

    public function createOffer(array $data)
    {
        $user = auth()->user();
        $provider = \App\Models\Provider::where('user_id', $user->id)->first();
        
        if (!$provider) {
            return ['status' => false, 'message' => __('messages.provider_not_found'), 'data' => []];
        }

        $product = \App\Models\Product::where('id', $data['product_id'])->where('provider_id', $provider->id)->first();
        if (!$product) {
            return ['status' => false, 'message' => __('messages.product_not_found_or_not_owned'), 'data' => []];
        }

        if (isset($data['start_date'])) {
            $data['start_date'] = \Carbon\Carbon::parse($data['start_date'])->format('Y-m-d H:i:s');
        }
        if (isset($data['end_date'])) {
            $data['end_date'] = \Carbon\Carbon::parse($data['end_date'])->format('Y-m-d H:i:s');
        }

        $data['is_active'] = $data['is_active'] ?? true;
        $offer = Offer::create($data);

        return ['status' => true, 'message' => __('messages.offer_created_successfully'), 'data' => \App\Http\Resources\API\GENERAL\ProviderOfferResource::make($offer->load('product.offers', 'product.reviews'))];
    }

    public function updateOffer($offerId, array $data)
    {
        $user = auth()->user();
        $provider = \App\Models\Provider::where('user_id', $user->id)->first();
        
        if (!$provider) {
            return ['status' => false, 'message' => __('messages.provider_not_found'), 'data' => []];
        }

        $offer = Offer::whereHas('product', function($q) use ($provider) {
            $q->where('provider_id', $provider->id);
        })->find($offerId);

        if (!$offer) {
            return ['status' => false, 'message' => __('messages.offer_not_found_or_not_owned'), 'data' => []];
        }

        if (isset($data['start_date'])) {
            $data['start_date'] = \Carbon\Carbon::parse($data['start_date'])->format('Y-m-d H:i:s');
        }
        if (isset($data['end_date'])) {
            $data['end_date'] = \Carbon\Carbon::parse($data['end_date'])->format('Y-m-d H:i:s');
        }

        $offer->update($data);

        return ['status' => true, 'message' => __('messages.offer_updated_successfully'), 'data' => \App\Http\Resources\API\GENERAL\ProviderOfferResource::make($offer->load('product.offers', 'product.reviews'))];
    }

    public function deleteOffer($offerId)
    {
        $user = auth()->user();
        $provider = \App\Models\Provider::where('user_id', $user->id)->first();
        
        if (!$provider) {
            return ['status' => false, 'message' => __('messages.provider_not_found'), 'data' => []];
        }

        $offer = Offer::whereHas('product', function($q) use ($provider) {
            $q->where('provider_id', $provider->id);
        })->find($offerId);

        if (!$offer) {
            return ['status' => false, 'message' => __('messages.offer_not_found_or_not_owned'), 'data' => []];
        }

        $offer->delete();

        return ['status' => true, 'message' => __('messages.offer_deleted_successfully'), 'data' => []];
    }
}
