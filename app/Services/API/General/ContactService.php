<?php

namespace App\Services\API\General;

use App\Models\Contact;
use App\Models\User;

class ContactService
{
    public function storeContactMessage(array $data)
    {
        try {
            // Verify the provider exists and is a service provider
            $provider = User::where('id', $data['provider_id'])->where('type', 'service_provider')->first();
            if (!$provider) {
                return [
                    'status' => false,
                    'message' => __('messages.provider_not_found'),
                    'data' => []
                ];
            }

            $contact = Contact::create([
                'user_id' => auth()->check() ? auth()->id() : null,
                'provider_id' => $data['provider_id'],
                'service_id' => $data['service_id'] ?? null,
                'name' => $data['name'],
                'phone' => $data['phone'],
                'message' => $data['message'],
            ]);

            return [
                'status' => true,
                'message' => __('messages.message_sent_successfully'),
                'data' => $contact
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => __('messages.something_went_wrong'),
                'data' => []
            ];
        }
    }
}
