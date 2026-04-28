<?php

namespace App\Http\Controllers\API\GENERAL;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\GENERAL\AddressRequest;
use App\Http\Requests\API\GENERAL\AddressUpdateRequest;
use App\Http\Requests\API\GENERAL\DeleteAddressRequest;
use App\Http\Resources\API\GENERAL\AddressResource;
use App\Models\Address;
use App\Services\API\General\AddressService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    use ApiResponse;

    protected $addressService;

    public function __construct(AddressService $addressService)
    {
        $this->addressService = $addressService;
    }

    public function store(AddressRequest $request)
    {
        $response = $this->addressService->storeAddress($request->validated());
        if ($response['status']) {
            return $this->success($response['data'], $response['message'], 200);
        }
        return $this->error( $response['message'], 400);
    }
    public function getUserAddresses()
    {
        $response = $this->addressService->getUserAddresses();
        if ($response['status']) {
            return $this->success($response['data'], $response['message'], 200);
        }
        return $this->error( $response['message'], 400);
    }
    public function updateAddress(AddressUpdateRequest $request)
    {
        $response = $this->addressService->updateAddress($request->validated());
        if ($response['status']) {
            return $this->success($response['data'], $response['message'], 200);
        }
        return $this->error( $response['message'], 400);
    }
    public function deleteAddress(DeleteAddressRequest $request)
    {
        $response = $this->addressService->deleteAddress($request->all());
        if ($response['status']) {
            return $this->success($response['data'], $response['message'], 200);
        }
        return $this->error( $response['message'], 400);
    }
    public function makeDefaultAddress(Request $request)
    {
        $response = $this->addressService->makeDefaultAddress($request->all());
        if ($response['status']) {
            return $this->success($response['data'], $response['message'], 200);
        }
        return $this->error( $response['message'], 400);
    }
}
