<?php

namespace App\Services\API\General;

use App\Http\Resources\API\GENERAL\ContactResource;
use App\Models\Contact;
use App\Models\User;

class ContactService
{
    public function myContacts()
    {
        $contacts = Contact::with('provider.user', 'service')->where('user_id', auth()->id())
        ->paginate(10);
        if($contacts->isEmpty()){
            return [
                'status' => false,
                'message' => __('messages.no_contacts_found'),
                'data' => []
            ];
        }
        return [
            'status' => true,
            'message' => __('messages.contacts_retrieved_successfully'),
            'data' => $contacts
        ];
    }
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
