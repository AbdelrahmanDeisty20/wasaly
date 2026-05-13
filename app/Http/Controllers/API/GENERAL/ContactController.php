<?php

namespace App\Http\Controllers\API\GENERAL;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\GENERAL\StoreContactRequest;
use App\Http\Resources\API\GENERAL\ContactResource;
use App\Services\API\General\ContactService;
use App\Traits\ApiResponse;

class ContactController extends Controller
{
    use ApiResponse;

    protected $contactService;

    public function __construct(ContactService $contactService)
    {
        $this->contactService = $contactService;
    }

    public function myContacts()
    {
        $response = $this->contactService->myContacts();
        if (!$response['status']) {
            return $this->error($response['message']);
        }
        return $this->paginated(ContactResource::class, $response['data'], $response['message']);
    }

    public function store(StoreContactRequest $request)
    {
        $response = $this->contactService->storeContactMessage($request->validated());
        if (!$response['status']) {
            return $this->error($response['message']);
        }
        return $this->success(new ContactResource($response['data']), $response['message']);
    }
}
