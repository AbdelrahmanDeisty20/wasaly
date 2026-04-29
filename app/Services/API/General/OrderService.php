<?php

namespace App\Services\API\General;

use App\Http\Resources\API\GENERAL\OrderListResource;
use App\Models\Order;

class OrderService
{
    public function getMyOrders(){
       $orders = Order::with(['items', 'governorate', 'center'])->where('user_id', auth()->id())->paginate(10);
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
        $order = Order::with(['items.product.offers', 'governorate', 'center'])->find($orderId);
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

    public function searchOrders($searchTerm)
    {
        $orders = Order::with(['items.product.offers', 'governorate', 'center'])
            ->where('user_id', auth()->id())
            ->where('order_number', 'like', "%{$searchTerm}%")
            ->paginate(10);

        if ($orders->isEmpty()) {
            return [
                'status' => false,
                'message' => __('messages.order_not_found'),
                'data' => [],
            ];
        }

        return [
            'status' => true,
            'message' => __('messages.orders_retrieved_successfully'),
            'data' => $orders,
        ];
    }

    public function cancelOrder(int $orderId)
    {
        $order = Order::where('user_id', auth()->id())->find($orderId);

        if (!$order) {
            return [
                'status' => false,
                'message' => __('messages.order_not_found'),
                'data' => [],
            ];
        }

        if ($order->status === 'pending') {
            $order->update(['status' => 'cancelled']);
            
            return [
                'status' => true,
                'message' => __('messages.order_cancelled_successfully'),
                'data' => OrderListResource::make($order->load(['items.product.offers', 'governorate', 'center'])),
            ];
        }

        return [
            'status' => false,
            'message' => __('messages.cannot_cancel_order'),
            'data' => [],
        ];
    }

    public function deleteOrder(int $orderId)
    {
        $order = Order::where('user_id', auth()->id())->find($orderId);

        if (!$order) {
            return [
                'status' => false,
                'message' => __('messages.order_not_found'),
                'data' => [],
            ];
        }

        if (in_array($order->status, ['delivered', 'cancelled'])) {
            $order->delete();
            return [
                'status' => true,
                'message' => __('messages.order_deleted_successfully'),
                'data' => [],
            ];
        }

        return [
            'status' => false,
            'message' => __('messages.cannot_delete_order_unless_delivered_or_cancelled'),
            'data' => [],
        ];
    }
}
