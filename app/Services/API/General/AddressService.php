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
                'message' => __('messages.addresses_fetched_successfully'),
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
        $data['user_id'] = auth()->id();

        if ($data['is_default'] ?? false) {
            Address::where('user_id', auth()->id())->update(['is_default' => false]);
        }

        $address = Address::create($data);
        if ($address) {
            return [
                'status' => true,
                'message' => __('messages.address_created_successfully'),
                'data' => AddressResource::make($address->load('governorate')),
            ];
        }
        return [
            'status' => false,
            'message' => __('messages.address_created_failed'),
            'data'=>[],
        ];
    }
    public function updateAddress(array $data)
    {
        $address = Address::where('id', $data['address_id'])->where('user_id', auth()->id())->first();
        if (!$address) {
            return [
                'status' => false,
                'message' => __('messages.address_not_found'),
                'data'=>[],
            ];
        }

        if ($data['is_default'] ?? false) {
            Address::where('user_id', auth()->id())->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        $address->update($data);
        return [
            'status' => true,
            'message' => __('messages.address_updated_successfully'),
            'data' => AddressResource::make($address->load('governorate')),
        ];
    }

    public function deleteAddress(array $data)
    {
        $address = Address::where('id', $data['address_id'])->where('user_id', auth()->id())->first();
        if (!$address) {
            return [
                'status' => false,
                'message' => __('messages.address_not_found'),
                'data'=>[],
            ];
        }
        $address->delete();
        return [
            'status' => true,
            'message' => __('messages.address_deleted_successfully'),
            'data' => [],
        ];
    }

    public function makeDefaultAddress(array $data)
    {
        $address = Address::where('id', $data['address_id'])->where('user_id', auth()->id())->first();
        if (!$address) {
            return [
                'status' => false,
                'message' => __('messages.address_not_found'),
                'data'=>[],
            ];
        }
        
        Address::where('user_id', auth()->id())->update(['is_default' => false]);
        $address->update(['is_default' => true]);
        
        return [
            'status' => true,
            'message' => __('messages.address_updated_successfully'),
            'data' => AddressResource::make($address->load('governorate')),
        ];
    }
}
