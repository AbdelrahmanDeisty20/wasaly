<?php

namespace App\Services\API\General;

use App\Http\Resources\API\GENERAL\BannerResource;
use App\Models\Banner;
use App\Traits\ApiResponse;

class BannerService
{
    use ApiResponse;
    public function getBanners()
    {
        $banners = Banner::where('is_active', 1)
            ->get();

        if($banners->isEmpty()){
            return [
            'status' => false,
            'message' => __('messages.banners_not_found'),
            'data' => [],
        ];
        }
        return [
            'status' => true,
            'message' => __('messages.banners_fetched_successfully'),
            'data' => BannerResource::collection($banners),
        ];
    }
}
