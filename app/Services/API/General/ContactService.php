<?php

namespace App\Services\API\General;

use App\Http\Resources\API\GENERAL\ContactResource;
use App\Models\Contact;
use App\Models\User;

class ContactService
{
    public function storeContactMessage(array $data)
    {
        $contacts = Contact::create([
            'user_id' => auth()->check() ? auth()->id() : null,
            'provider_id' => $data['provider_id'] ?? null,
            'service_id' => $data['service_id'] ?? null,
            'name' => $data['name'],
            'phone' => $data['phone'],
            'message' => $data['message'],
        ]);

       return [
        'status' => true,
        'message' => __('messages.message_sent_successfully'),
        'data' => ContactResource::make($contacts->load('provider.user', 'service', 'user'))
       ];
    }
}
