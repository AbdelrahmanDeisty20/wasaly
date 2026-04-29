<?php

namespace App\Services\API\General;

use App\Http\Resources\API\ProductListResource;
use App\Http\Resources\API\ProductResource;
use App\Models\Product;
use App\Traits\ApiResponse;

class ProductService
{
    use ApiResponse;
    public function getProducts()
    {
        $products = Product::with(['offers','reviews'])->paginate(10);
        if($products->isEmpty()){
            return [
                'status' => false,
                'message' => __('messages.products_not_found'),
                'data' => []
            ];
        }
        return [
            'status' => true,
            'message' => __('messages.products_retrieved_successfully'),
            'data' => ProductResource::collection($products)
        ];
    }
    public function getProduct($data)
    {
        $product = Product::with(['specifications','images','subCategory','brand','offers','reviews.user'])->find($data['product_id']);
        if(!$product){
            return [
                'status' => false,
                'message' => __('messages.product_not_found'),
                'data' => []
            ];
        }
        return [
            'status' => true,
            'message' => __('messages.product_retrieved_successfully'),
            'data' => new ProductListResource($product)
        ];
    }

    public function filter(array $filters = [])
    {
        $query = Product::with(['offers', 'images', 'reviews']);

        // 1. الفلترة بالتصنيف الفرعي (SubCategory Filter)
        if (!empty($filters['category_id'])) {
            $query->where('sub_category_id', $filters['category_id']);
        }

        // 2. الفلترة بالسعر (Price Range)
        if (isset($filters['min_price']) || isset($filters['max_price'])) {
            $min = $filters['min_price'] ?? 0;
            $max = $filters['max_price'] ?? 999999;
            $query->whereBetween('price', [$min, $max]);
        }

        // 3. عروض خاصة (Special Offers)
        if (!empty($filters['special_offers'])) {
            $query->whereHas('offers', function ($q) {
                $q->where('is_active', true)
                  ->where(function($sq){
                      $sq->whereNull('end_date')->orWhere('end_date', '>=', now());
                  });
            });
        }

        // 4. التقييمات (Ratings)
        if (!empty($filters['ratings'])) {
            $query->withAvg('reviews', 'rating')
                ->having('reviews_avg_rating', '>=', $filters['ratings']);
        }

        // 5. الترتيب (Sorting)
        $sort = $filters['sort'] ?? 'latest';

        switch ($sort) {
            case 'min_price':
                $query->orderBy('price', 'asc');
                break;
            case 'max_price':
                $query->orderBy('price', 'desc');
                break;

            case 'offers':
                // ترتيب المنتجات التي لديها عروض نشطة لتظهر في البداية
                $query->leftJoin('offers', function($join) {
                        $join->on('products.id', '=', 'offers.product_id')
                            ->where('offers.is_active', true)
                            ->where(function($q){
                                $q->whereNull('offers.end_date')->orWhere('offers.end_date', '>=', now());
                            });
                    })
                    ->select('products.*')
                    ->orderByRaw('CASE WHEN offers.id IS NOT NULL THEN 0 ELSE 1 END');
                break;

            case 'latest':
            default:
                $query->orderByDesc('id');
                break;
        }

        $products = $query->paginate(10);

        if ($products->isEmpty()) {
            return [
                'status' => false,
                'message' => __('messages.products_not_found'),
                'data' => [],
            ];
        }

        return [
            'status' => true,
            'message' => __('messages.products_retrieved_successfully'),
            'data' => $products, // Will be wrapped in Controller if needed, or use Resource here
        ];
    }
}
