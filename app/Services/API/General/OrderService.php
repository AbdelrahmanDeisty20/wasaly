<?php

namespace App\Services\API\General;

use App\Http\Resources\API\GENERAL\OrderListResource;
use App\Models\Order;

class OrderService
{
    public function getMyOrders(){
       $orders = Order::where('user_id', auth()->id())->paginate(10);
       if($orders->isEmpty()){
        return [
            'status' => false,
            'message' => __('messages.no_orders_found'),
            'data' => []
        ];
       }
       return [
        'status' => true,
        'message' => __('messages.orders_retrieved_successfully'),
        'data' => $orders
       ];
    }

    public function getOrderDetails($orderId){
        $order = Order::with('packages')->find($orderId);
        if(!$order){
            return [
                'status' => false,
                'message' => __('messages.order_not_found'),
                'data' => []
            ];
        }
        return [
            'status' => true,
            'message' => __('messages.order_retrieved_successfully'),
            'data' => OrderListResource::make($order)
        ];
    }
}
