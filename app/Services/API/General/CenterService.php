<?php

namespace App\Services\API\General;

use App\Http\Resources\API\GENERAL\CenterResource;
use App\Models\Center;
use App\Traits\ApiResponse;

class CenterService
{
    use ApiResponse;

    public function getCentersByGovernorate(array $data)
    {
        $centers = Center::with('governorate')
            ->where('governorate_id', $data['governorate_id'])
            ->get();

        if ($centers->isNotEmpty()) {
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
