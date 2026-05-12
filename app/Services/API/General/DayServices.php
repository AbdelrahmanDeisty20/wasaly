<?php

namespace App\Services\API\General;

use App\Http\Resources\API\GENERAL\AvailableDayResource;
use App\Http\Resources\API\GENERAL\AvailableTimeResource;
use App\Models\AvailableDay;
use App\Models\AvailableTime;
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
            'data' => AvailableDayResource::collection($days)
        ];
    }
    public function getTimes()
    {
        $times = AvailableTime::with('availableDay')->paginate(10);
        if($times->isEmpty()){
            return [
                'status' => false,
                'message' => __('messages.times_not_found'),
                'data' => []
            ];
        }
        return [
            'status' => true,
            'message' => __('messages.times_fetched_successfully'),
            'data' => $times
        ];
    }
}
