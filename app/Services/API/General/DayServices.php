<?php

namespace App\Services\API\General;

use App\Models\AvailableDay;
use App\Traits\ApiResponse;
use Illuminate\Support\Collection;

class DayServices
{
    use ApiResponse;
    public function getDays()
    {
        $days = AvailableDay::all();
        if($days->isEmpty()){
            return [
                'status' => false,
                'message' => __('messages.days_not_found'),
                'data' => []
            ];
        }
        return [
            'status' => true,
            'message' => __('messages.days_fetched_successfully'),
            'data' => $days
        ];
    }
}
