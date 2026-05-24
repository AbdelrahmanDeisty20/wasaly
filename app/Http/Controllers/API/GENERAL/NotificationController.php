<?php

namespace App\Http\Controllers\API\GENERAL;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\GENERAL\NotifyTurnOffRequest;
use App\Http\Requests\API\GENERAL\NotifyTurnOnRequest;
use App\Http\Requests\API\GENERAL\storeTokenRequest;
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
    public function NotificationStatus(){
        $result = $this->notificationService->NotificationStatus();
        if (!$result['status']) {
            return $this->error($result['message'], 400);
        }
        return $this->success($result['data'], $result['message'], 200);
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
    public function sendToken(storeTokenRequest $request)
    {
        $result = $this->notificationService->sendToken($request->validated());

        if (!$result['status']) {
            return $this->error($result['message'], 400);
        }

        return $this->success([], $result['message']);
    }
    public function sendTestNotification(Request $request)
    {
        $title = $request->title ?? __('messages.test_notification_guest_title');
        $body = $request->body ?? __('messages.test_notification_guest_body');
        $data = $request->data ?? ['type' => 'test'];

        $result = $this->notificationService->sendNotificationToGuests($title, $body, $data);

        return $this->success($result, $result['message']);
    }

    public function sendTestNotificationToUsers(Request $request)
    {
        $title = $request->title ?? __('messages.test_notification_user_title');
        $body = $request->body ?? __('messages.test_notification_user_body');
        $data = $request->data ?? ['type' => 'test_user'];

        $result = $this->notificationService->sendNotificationToUsers($title, $body, $data);

        return $this->success($result, $result['message']);
    }
    public function notifications()
    {
        $result = $this->notificationService->notifications();
        if (!$result['status']) {
            return $this->error($result['message'], 400);
        }
        return $this->success($result['data'], $result['message'], 200);
    }
    public function readNotification(Request $request)
    {
        $result = $this->notificationService->readNotification($request->all());
        if (!$result['status']) {
            return $this->error($result['message'], 400);
        }
        return $this->success($result['data'], $result['message'], 200);
    }
    public function readAllNotifications(Request $request)
    {
        $result = $this->notificationService->readAllNotifications($request->all());
        if (!$result['status']) {
            return $this->error($result['message'], 400);
        }
        return $this->success($result['data'], $result['message'], 200);
    }
    public function deleteNotification(Request $request)
    {
        $result = $this->notificationService->deleteNotification($request->all());
        if (!$result['status']) {
            return $this->error($result['message'], 400);
        }
        return $this->success($result['data'], $result['message'], 200);
    }
    public function deleteAllNotifications(Request $request)
    {
        $result = $this->notificationService->deleteAllNotifications($request->all());
        if (!$result['status']) {
            return $this->error($result['message'], 400);
        }
        return $this->success($result['data'], $result['message'], 200);
    }
}
