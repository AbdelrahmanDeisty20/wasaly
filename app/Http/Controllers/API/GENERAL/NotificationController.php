<?php

namespace App\Http\Controllers\API\GENERAL;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\GENERAL\NotifyTurnOffRequest;
use App\Http\Requests\API\GENERAL\NotifyTurnOnRequest;
use App\Services\API\General\NotificationService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use ApiResponse;
    protected $notificationService;
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    public function TurnOnNotification(NotifyTurnOnRequest $request)
    {
        $result = $this->notificationService->TurnOnNotification($request->validated());
        if (!$result['status']) {
            return $this->error($result['message'], 400);
        }
        return $this->success($result['data'], $result['message'], 200);
    }
    public function TurnOffNotification(NotifyTurnOffRequest $request)
    {
        $result = $this->notificationService->TurnOffNotification($request->validated());
        if (!$result['status']) {
            return $this->error($result['message'], 400);
        }
        return $this->success($result['data'], $result['message'], 200);
    }
}
