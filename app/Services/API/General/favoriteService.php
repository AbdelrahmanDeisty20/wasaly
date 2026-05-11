<?php

namespace App\Services\API\General;

use App\Http\Resources\API\GENERAL\FavouriteResource;
use App\Http\Resources\API\ProductListResource;
use App\Models\Favorite;
use App\Models\Product;
use App\Models\Service;
use App\Traits\ApiResponse;

class favoriteService
{
    use ApiResponse;

    public function getFavorites()
    {
        $favorites = Favorite::with('product.offers', 'product.reviews')
            ->where('user_id', auth()->id())
            ->whereNotNull('product_id')
            ->where('is_active', true)
            ->latest()
            ->paginate(10);

        if ($favorites->isEmpty()) {
            return [
                'status' => true,
                'message' => __('messages.favorites_not_found'),
                'data' => $favorites
            ];
        }

        $products = $favorites->through(function ($favorite) {
            return $favorite->product;
        });

        return [
            'status' => true,
            'message' => __('messages.favorites_retrieved_successfully'),
            'data' => $products
        ];
    }

    public function getServiceFavorites()
    {
        $favorites = Favorite::with('service.subCategory')
            ->where('user_id', auth()->id())
            ->whereNotNull('service_id')
            ->where('is_active', true)
            ->latest()
            ->paginate(10);

        if ($favorites->isEmpty()) {
            return [
                'status' => true,
                'message' => __('messages.favorites_not_found'),
                'data' => $favorites
            ];
        }

        $services = $favorites->through(function ($favorite) {
            return $favorite->service;
        });

        return [
            'status' => true,
            'message' => __('messages.favorites_retrieved_successfully'),
            'data' => $services
        ];
    }

    public function addFavorite($data)
    {
        $product = Product::find($data['product_id']);
        if (!$product) {
            return [
                'status' => false,
                'message' => __('messages.product_not_found'),
                'data' => []
            ];
        }

        $favorite = Favorite::updateOrCreate(
            ['user_id' => auth()->id(), 'product_id' => $data['product_id']],
            ['is_active' => true]
        );

        return [
            'status' => true,
            'message' => __('messages.added_to_favorites'),
            'data' => new FavouriteResource($favorite->load('product'))
        ];
    }

    public function removeFavorite($data)
    {
        $favorite = Favorite::where('user_id', auth()->id())
            ->where('product_id', $data['product_id'])
            ->first();

        if ($favorite) {
            $favorite->update(['is_active' => false]);
        }

        return [
            'status' => true,
            'message' => __('messages.removed_from_favorites'),
            'data' => $favorite ? new FavouriteResource($favorite->load('product')) : []
        ];
    }

    public function addServiceFavorite($data)
    {
        $userId = auth()->id();
        $favorite = Favorite::updateOrCreate(
            ['user_id' => $userId, 'service_id' => $data['service_id']],
            ['is_active' => true]
        );

        return [
            'status' => true,
            'message' => __('messages.added_to_favorites'),
            'data' => new FavouriteResource($favorite->load('service.subCategory'))
        ];
    }

    public function removeServiceFavorite($data)
    {
        $userId = auth()->id();
        $favorite = Favorite::where('user_id', $userId)
            ->where('service_id', $data['service_id'])
            ->first();

        if ($favorite) {
            $favorite->update(['is_active' => false]);
        }

        return [
            'status' => true,
            'message' => __('messages.removed_from_favorites'),
            'data' => $favorite ? new FavouriteResource($favorite->load('service.subCategory')) : []
        ];
    }
}
