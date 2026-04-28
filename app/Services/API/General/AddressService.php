<?php

namespace App\Services\API\General;

use App\Http\Resources\API\GENERAL\AddressResource;
use App\Models\Address;
use App\Traits\ApiResponse;

class AddressService
{
    use ApiResponse;
    public function getUserAddresses()
    {
        $addresses = Address::where('user_id', auth()->id())->get();
        if ($addresses) {
            return [
                'status' => true,
                'messages' => __('messages.addresses_fetched_successfully'),
                'data' => AddressResource::collection($addresses),
            ];
        }
        return [
            'status' => false,
            'message' => __('messages.addresses_fetched_failed'),
            'data'=>[],
        ];
    }
    public function storeAddress(array $data)
    {
        $address = Address::create($data);
        if ($address) {
            return [
                'status' => true,
                'messages' => __('messages.address_created_successfully'),
                'data' => AddressResource::make($address),
            ];
        }
        return [
            'status' => false,
            'message' => __('messages.address_created_failed'),
            'data'=>[],
        ];
    }
}
