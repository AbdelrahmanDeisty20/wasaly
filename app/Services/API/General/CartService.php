<?php

namespace App\Services\API\General;

use App\Http\Resources\API\CartResource;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;

class CartService
{
    use ApiResponse;

    public function getCart()
    {
        $cart = Cart::with('items.offers')->firstOrCreate([
            'user_id' => auth()->id(),
        ]);

        return [
            'status' => true,
            'message' => __('messages.cart_retrieved_successfully'),
            'data' => new CartResource($cart)
        ];
    }

    public function addToCart($data)
    {
        DB::beginTransaction();
        try {
            $product = Product::find($data['product_id']);
            if (!$product) {
                return [
                    'status' => false,
                    'message' => __('messages.product_not_found'),
                    'data' => []
                ];
            }

            if ($product->stock < $data['quantity']) {
                return [
                    'status' => false,
                    'message' => __('messages.insufficient_stock'),
                    'data' => []
                ];
            }

            $cart = Cart::firstOrCreate(['user_id' => auth()->id()]);

            $cartItem = $cart->items()->where('product_id', $product->id)->first();

            if ($cartItem) {
                $newQuantity = $cartItem->quantity + $data['quantity'];
                if ($product->stock < $newQuantity) {
                    return [
                        'status' => false,
                        'message' => __('messages.insufficient_stock'),
                        'data' => []
                    ];
                }
                $cartItem->update([
                    'quantity' => $newQuantity,
                    'total_price' => $newQuantity * $product->price,
                ]);
            } else {
                $cart->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $data['quantity'],
                    'unit_price' => $product->price,
                    'total_price' => $data['quantity'] * $product->price,
                ]);
            }

            DB::commit();
            return [
                'status' => true,
                'message' => __('messages.cart_item_added'),
                'data' => []
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

    public function updateQuantity($data)
    {
        DB::beginTransaction();
        try {
            $cartItem = CartItem::whereHas('cart', function ($q) {
                $q->where('user_id', auth()->id());
            })->find($data['cart_item_id']);

            if (!$cartItem) {
                return [
                    'status' => false,
                    'message' => __('messages.cart_item_not_found'),
                    'data' => []
                ];
            }

            $product = $cartItem->product;
            if ($product->stock < $data['quantity']) {
                return [
                    'status' => false,
                    'message' => __('messages.insufficient_stock'),
                    'data' => []
                ];
            }

            if ($data['quantity'] <= 0) {
                $cartItem->delete();
            } else {
                $cartItem->update([
                    'quantity' => $data['quantity'],
                    'total_price' => $data['quantity'] * $cartItem->unit_price,
                ]);
            }

            DB::commit();
            return [
                'status' => true,
                'message' => __('messages.cart_item_updated'),
                'data' => []
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

    public function removeItem($data)
    {
        DB::beginTransaction();
        try {
            $cartItem = CartItem::whereHas('cart', function ($q) {
                $q->where('user_id', auth()->id());
            })->find($data['cart_item_id']);

            if (!$cartItem) {
                return [
                    'status' => false,
                    'message' => __('messages.cart_item_not_found'),
                    'data' => []
                ];
            }

            $cartItem->delete();

            DB::commit();
            return [
                'status' => true,
                'message' => __('messages.cart_item_removed'),
                'data' => []
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

    public function clearCart()
    {
        DB::beginTransaction();
        try {
            $cart = Cart::where('user_id', auth()->id())->first();
            if ($cart) {
                $cart->items()->delete();
            }

            DB::commit();
            return [
                'status' => true,
                'message' => __('messages.cart_cleared_successfully'),
                'data' => []
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
