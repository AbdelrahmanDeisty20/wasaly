<?php

namespace App\Http\Controllers\API\GENERAL;

use App\Http\Requests\API\GENERAL\AddToCartRequest;
use App\Http\Requests\API\GENERAL\UpdateCartQuantityRequest;
use App\Http\Requests\API\GENERAL\RemoveCartItemRequest;
use App\Http\Controllers\Controller;
use App\Services\API\General\CartService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    use ApiResponse;
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function getCart()
    {
        $result = $this->cartService->getCart();
        return $this->success($result['data'], $result['message']);
    }

    public function addToCart(AddToCartRequest $request)
    {
        $result = $this->cartService->addToCart($request->validated());
        if ($result['status']) {
            return $this->success($result['data'], $result['message']);
        }
        return $this->error($result['message'], 400);
    }

    public function updateQuantity(UpdateCartQuantityRequest $request)
    {
        $result = $this->cartService->updateQuantity($request->validated());
        if ($result['status']) {
            return $this->success($result['data'], $result['message']);
        }
        return $this->error($result['message'], 400);
    }

    public function removeItem(RemoveCartItemRequest $request)
    {
        $result = $this->cartService->removeItem($request->validated());
        if ($result['status']) {
            return $this->success($result['data'], $result['message']);
        }
        return $this->error($result['message'], 400);
    }

    public function clearCart()
    {
        $result = $this->cartService->clearCart();
        return $this->success($result['data'], $result['message']);
    }
}
