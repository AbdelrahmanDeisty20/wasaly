<?php

namespace App\Services\API\General;

use App\Http\Resources\API\GENERAL\FavouriteResource;
use App\Http\Resources\API\ProductListResource;
use App\Models\Favorite;
use App\Models\Product;
use App\Traits\ApiResponse;

class favoriteService
{
    use ApiResponse;

    public function getFavorites()
    {
        $favorites = Favorite::with('product.offers')
            ->where('user_id', auth()->id())
            ->where('is_active', true)
            ->latest()
            ->paginate(10);

        if ($favorites->isEmpty()) {
            return [
                'status' => false,
                'message' => __('messages.favorites_not_found'),
                'data' => []
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

    public function toggleFavorite($data)
    {
        $product = Product::find($data['product_id']);
        if (!$product) {
            return [
                'status' => false,
                'message' => __('messages.product_not_found'),
                'data' => []
            ];
        }

        $favorite = Favorite::where('user_id', auth()->id())
            ->where('product_id', $data['product_id'])
            ->first();

        if ($favorite) {
            $favorite->update([
                'is_active' => !$favorite->is_active
            ]);

            $message = $favorite->is_active
                ? __('messages.added_to_favorites')
                : __('messages.removed_from_favorites');

            return [
                'status' => true,
                'message' => $message,
                'data' => new FavouriteResource($favorite->load('product'))
            ];
        }

        Favorite::create([
            'user_id' => auth()->id(),
            'product_id' => $data['product_id'],
            'is_active' => true,
        ]);

        return [
            'status' => true,
            'message' => __('messages.added_to_favorites'),
            'data' => [
                'is_favorite' => true
            ]
        ];
    }
}
