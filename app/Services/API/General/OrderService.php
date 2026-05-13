<?php

namespace App\Services\API\General;

use App\Http\Resources\API\GENERAL\OrderListResource;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

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

    public function updateOrder(int $orderId, array $data)
    {
        DB::beginTransaction();
        try {
            $order = Order::where('user_id', auth()->id())->find($orderId);

            if (!$order) {
                return ['status' => false, 'message' => __('messages.order_not_found'), 'data' => []];
            }

            if ($order->status !== 'pending') {
                return ['status' => false, 'message' => __('messages.cannot_edit_booking'), 'data' => []];
            }

            // Update items quantity
            if (isset($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $itemData) {
                    $orderItem = OrderItem::where('order_id', $order->id)->find($itemData['order_item_id']);
                    if ($orderItem) {
                        $product = $orderItem->product;
                        
                        // Restore old stock
                        $product->increment('stock', $orderItem->quantity);
                        
                        // Check new stock
                        if ($product->stock < $itemData['quantity']) {
                            DB::rollBack();
                            return [
                                'status' => false,
                                'message' => __('messages.insufficient_stock_for') . ' ' . $product->name,
                                'data' => []
                            ];
                        }
                        
                        // Reduce new stock
                        $product->decrement('stock', $itemData['quantity']);
                        
                        // Update order item
                        $orderItem->quantity = $itemData['quantity'];
                        $orderItem->total_price = $orderItem->unit_price * $itemData['quantity'];
                        $orderItem->save();
                    }
                }
            }

            // Now recalculate order totals
            $totalPrice = $order->items()->sum('total_price');
            $totalQuantity = $order->items()->sum('quantity');

            // Handle address/shipping cost update
            if (isset($data['address_id'])) {
                $address = \App\Models\Address::with(['governorate', 'center'])->find($data['address_id']);
                if ($address) {
                    $order->address_id = $address->id;
                    $order->governorate_id = $address->governorate_id;
                    $order->center_id = $address->center_id;
                    $order->customer_address = $address->address;
                    $order->shipping_cost = $address->governorate->shipping_cost ?? 0;
                }
            } elseif (isset($data['governorate_id'])) {
                $governorate = \App\Models\Governorate::find($data['governorate_id']);
                $order->governorate_id = $data['governorate_id'];
                $order->shipping_cost = $governorate ? $governorate->shipping_cost : 0;
                if (isset($data['center_id'])) $order->center_id = $data['center_id'];
            }

            // Handle Coupon
            $couponCode = isset($data['coupon_code']) ? $data['coupon_code'] : $order->coupon_code;
            $discountAmount = 0;
            
            if ($couponCode) {
                $couponService = app(\App\Services\API\General\CouponService::class);
                $couponResponse = $couponService->getCouponInfo($couponCode, $totalPrice);
                
                if ($couponResponse['data']['is_valid']) {
                    $coupon = \App\Models\Coupon::where('code', $couponCode)->first();
                    $discountAmount = $coupon->calculateDiscount($totalPrice);
                    $order->coupon_code = $couponCode;
                } else {
                    // If the user provided a NEW coupon that is invalid, return error
                    if (isset($data['coupon_code'])) {
                        DB::rollBack();
                        return [
                            'status' => false,
                            'message' => $couponResponse['message'],
                            'data' => []
                        ];
                    } else {
                        // Old coupon became invalid, just remove it
                        $order->coupon_code = null;
                    }
                }
            }

            // Update other fields
            $fields = ['customer_name', 'customer_phone', 'customer_address', 'payment_method', 'region'];
            foreach ($fields as $field) {
                if (isset($data[$field])) {
                    $order->$field = $data[$field];
                }
            }

            // Recalculate order total
            $order->unit_price = $totalPrice;
            $order->discount_amount = $discountAmount;
            $order->quantity = $totalQuantity;
            $order->total_price = ($totalPrice - $discountAmount) + $order->shipping_cost;

            $order->save();

            DB::commit();

            return [
                'status' => true,
                'message' => __('messages.order_updated_successfully'),
                'data' => \App\Http\Resources\API\CheckoutOrderResource::make($order->load(['items.product.offers', 'governorate', 'center']))
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'status' => false,
                'message' => $e->getMessage(),
                'data' => []
            ];
        }
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
