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
            $cart = Cart::with('items.product')->where('user_id', $user->id)->first();

            if (!$cart || $cart->items->isEmpty()) {
                return [
                    'status' => false,
                    'message' => __('messages.cart_empty'),
                    'data' => []
                ];
            }

            // 1. Stock Validation Snapshot
            foreach ($cart->items as $item) {
                if ($item->product->stock < $item->quantity) {
                    return [
                        'status' => false,
                        'message' => __('messages.insufficient_stock_for') . ' ' . $item->product->name,
                        'data' => []
                    ];
                }
            }

            $address = Address::find($data['address_id'] ?? null) ?? $user->addresses()->where('is_default', 1)->first();
            
            if (!$address) {
                return [
                    'status' => false,
                    'message' => __('messages.address_required'),
                    'data' => []
                ];
            }

            $subTotal = $cart->items->sum('total_price');
            $totalQuantity = $cart->items->sum('quantity');

            // 2. Create Order
            $order = Order::create([
                'user_id'          => $user->id,
                'address_id'       => $address->id,
                'unit_price'       => $subTotal, // Following migration's ambiguous schema for now
                'quantity'         => $totalQuantity,
                'total_price'      => $subTotal,
                'customer_name'    => $user->full_name ?? $user->name,
                'customer_phone'   => $user->phone,
                'customer_address' => $address->address,
                'payment_method'   => $data['payment_method'] ?? 'cash',
                'status'           => 'pending',
            ]);

            // 3. Move Items & Deduct Stock
            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id'    => $order->id,
                    'product_id'  => $item->product_id,
                    'quantity'    => $item->quantity,
                    'unit_price'  => $item->unit_price,
                    'total_price' => $item->total_price,
                ]);

                // Deduct stock
                $item->product->decrement('stock', $item->quantity);
            }

            // 4. Cleanup Cart
            $cart->items()->delete();
            $cart->delete();

            DB::commit();

            return [
                'status' => true,
                'message' => __('messages.checkout_success'),
                'data' => [
                    'order_id' => $order->id,
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
