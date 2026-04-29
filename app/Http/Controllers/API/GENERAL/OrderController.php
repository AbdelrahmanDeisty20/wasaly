<?php

namespace App\Http\Controllers\API\GENERAL;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\GENERAL\SearchOrderRequest;
use App\Http\Resources\API\GENERAL\OrderListResource;
use App\Http\Resources\API\OrderResource;
use App\Services\API\General\OrderService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use ApiResponse;

    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function getMyOrders()
    {
        $orders = $this->orderService->getMyOrders();
        if (!$orders['status']) {
            return $this->error($orders['message'], 404);
        }
        return $this->paginated(OrderResource::class, $orders['data'], $orders['message']);
    }

    public function getOrderDetails($orderId)
    {
        $order = $this->orderService->getOrderDetails($orderId);
        if (!$order['status']) {
            return $this->error($order['message']);
        }
        return $this->success($order['data'], $order['message']);
    }

    public function searchOrders(SearchOrderRequest $request)
    {
        $orders = $this->orderService->searchOrders($request->search_term);
        if (!$orders['status']) {
            return $this->error($orders['message'], 404);
        }
        return $this->paginated(OrderListResource::class, $orders['data'], $orders['message']);
    }

    public function cancelOrder($orderId)
    {
        $response = $this->orderService->cancelOrder($orderId);
        if (!$response['status']) {
            return $this->error($response['message'], 422);
        }
        return $this->success($response['data'], $response['message']);
    }

    public function deleteOrder($orderId)
    {
        $response = $this->orderService->deleteOrder($orderId);
        if (!$response['status']) {
            return $this->error($response['message'], 422);
        }
        return $this->success($response['data'], $response['message']);
    }
}
