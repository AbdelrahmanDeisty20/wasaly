<?php

namespace App\Services\API\General;

use App\Http\Resources\API\GovernorateResource;
use App\Models\Governorate;

class GovernorateService
{
    public function getAllGovernorates()
    {
        $governorates = Governorate::all();
        if ($governorates) {
            return [
                'status' => true,
                'message' => __('messages.governorates_fetched_successfully'),
                'data' => GovernorateResource::collection($governorates),
            ];
        }
        return [
            'status' => false,
            'message' => __('messages.governorates_fetched_failed'),
            'data'=>[],
        ];
        
    }
}
