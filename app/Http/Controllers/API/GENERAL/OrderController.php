<?php

namespace App\Http\Controllers\API\GENERAL;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\GENERAL\OrderListResource;
use App\Http\Resources\API\OrderResource;
use App\Traits\ApiResponse;
use App\Services\API\General\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use ApiResponse;
    protected $orderService;
    public function __construct(OrderService $orderService){
        $this->orderService = $orderService;
    }

    public function getMyOrders(){
        $orders = $this->orderService->getMyOrders();
        if(!$orders['status']){
            return $this->error($orders['message'],404);
        }
        return $this->paginated(OrderResource::class,$orders['data'], $orders['message']);
    }

    public function getOrderDetails($orderId){
        $order = $this->orderService->getOrderDetails($orderId);
        if(!$order['status']){
            return $this->error($order['message']);
        }
        return $this->success($order['data'], $order['message']);
    }
}
