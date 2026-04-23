<?php

namespace App\Services\API\General;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Address;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    use ApiResponse;

    public function checkout($data)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $cart = Cart::with('items.product.offers')->where('user_id', $user->id)->first();

            if (!$cart || $cart->items->isEmpty()) {
                return [
                    'status' => false,
                    'message' => __('messages.cart_empty'),
                    'data' => []
                ];
            }

            // 1. حساب الإجمالي بناءً على العروض الحالية
            $totalPrice = 0;
            $totalQuantity = 0;
            
            foreach ($cart->items as $item) {
                // التأكد من المخزون
                if ($item->product->stock < $item->quantity) {
                    return [
                        'status' => false,
                        'message' => __('messages.insufficient_stock_for') . ' ' . $item->product->name,
                        'data' => []
                    ];
                }

                // حساب السعر بعد الخصم (لو فيه عرض نشط)
                $unitPrice = $item->product->discounted_price;
                $item->calculated_total = $unitPrice * $item->quantity;
                
                $totalPrice += $item->calculated_total;
                $totalQuantity += $item->quantity;
            }

            $address = Address::find($data['address_id'] ?? null) ?? $user->addresses()->where('is_default', 1)->first();
            
            

            // 2. إنشاء الطلب برقم مميز
            $order = Order::create([
                'order_number'     => 'ORD-' . strtoupper(bin2hex(random_bytes(3))),
                'user_id'          => $user->id,
                'address_id'       => $address ? $address->id : null,
                'unit_price'       => $totalPrice, // السعر الإجمالي هنا
                'quantity'         => $totalQuantity,
                'total_price'      => $totalPrice,
                'customer_name'    => $data['customer_name'] ?? ($user->full_name ?? $user->name),
                'customer_phone'   => $data['customer_phone'] ?? $user->phone,
                'customer_address' => $address ? $address->address : ($data['customer_address'] ?? null),
                'payment_method'   => $data['payment_method'] ?? 'cash',
                'status'           => 'pending',
                'governorate_id'   => $data['governorate_id'] ?? null,
                'region'           => $data['region'] ?? null,
            ]);

            // 3. نقل العناصر وخصم المخزون
            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id'    => $order->id,
                    'product_id'  => $item->product_id,
                    'quantity'    => $item->quantity,
                    'unit_price'  => $item->product->discounted_price,
                    'total_price' => $item->calculated_total,
                ]);

                // خصم المخزون
                $item->product->decrement('stock', $item->quantity);
            }

            // 4. تنظيف السلة
            $cart->items()->delete();
            $cart->delete();

            DB::commit();

            return [
                'status' => true,
                'message' => __('messages.checkout_success'),
                'data' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'total_price' => $order->total_price
                ]
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
}
