<?php

namespace App\Services\API\General;

use App\Http\Resources\API\BrandResource;
use App\Models\Brand;
use App\Traits\ApiResponse;

class BrandService
{
    use ApiResponse;
    /**
     * Create a new class instance.
     */
    public function getAllBrands()
    {
        $brands = Brand::paginate(10);
        if($brands->isEmpty()){
           return[
            'status'=>false,
            "message"=>__('messages.brands_not_found'),
            "data"=>[]
           ];
        }
        return[
            'status'=>true,
            "message"=>__('messages.brands_fetched_successfully'),
            "data"=>BrandResource::collection($brands)
        ];
    }
    public function getBrand(array $data){
        $brand = Brand::with('products')->find($data['brand_id']);
        if(!$brand){
            return[
                'status'=>false,
                "message"=>__('messages.brand_not_found'),
                "data"=>[]
            ];
        }
        return[
            'status'=>true,
            "message"=>__('messages.brand_fetched_successfully'),
            "data"=>new BrandResource($brand)
        ];
    }
    
}
