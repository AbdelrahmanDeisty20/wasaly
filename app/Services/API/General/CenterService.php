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
        $centers = Center::where('governorate_id', $governorateId)->get();
        
        return [
            'status' => true,
            'message' => __('messages.centers_fetched_successfully'),
            'data' => CenterResource::collection($centers),
        ];
    }
}
