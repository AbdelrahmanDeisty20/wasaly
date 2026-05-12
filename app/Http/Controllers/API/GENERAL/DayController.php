<?php

namespace App\Http\Controllers\API\GENERAL;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\GENERAL\AvailableTimeResource;
use App\Traits\ApiResponse;
use App\Services\API\General\DayServices;
use Illuminate\Http\Request;

class DayController extends Controller
{
    use ApiResponse;
    protected $dayService;
    public function __construct(DayServices $dayService)
    {
        $this->dayService = $dayService;
    }
    public function getDays()
    {
        $result = $this->dayService->getDays();
        if (!$result['status']) {
            return $this->error($result['message'], 404);
        }
        return $this->success($result['data'], $result['message'], 200);
    }
    public function getTimes()
    {
        $result = $this->dayService->getTimes();
        if (!$result['status']) {
            return $this->error($result['message'], 404);
        }
        return $this->paginated(AvailableTimeResource::class, $result['data'], $result['message']);
    }
}
