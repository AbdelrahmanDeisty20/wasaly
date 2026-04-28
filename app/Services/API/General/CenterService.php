<?php

namespace App\Services\API\General;

use App\Http\Resources\API\GENERAL\CenterResource;
use App\Models\Center;
use App\Traits\ApiResponse;

class CenterService
{
    use ApiResponse;

    public function getCentersByGovernorate($governorateId)
    {
        $centers = Center::with('governorate')->paginate(10);
        if($centers){
            return [
                'status' => true,
                'message' => __('messages.centers_fetched_successfully'),
                'data' => CenterResource::collection($centers),
            ];
        }
        return [
            'status' => false,
            'message' => __('messages.centers_fetched_failed'),
            'data' => [],
        ];
    }
}
