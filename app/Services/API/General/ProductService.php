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
        $products = Product::with(['offers'])->paginate(10);
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
        $product = Product::with(['specifications','images','subCategory','brand','offers','reviews'])->find($data['product_id']);
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
}
