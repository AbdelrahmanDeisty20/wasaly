<?php

namespace App\Http\Requests\API\GENERAL;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AddressUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'address_id' => 'required|exists:addresses,id',
            'title' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'governorate_id' => 'nullable|exists:governorates,id',
            'is_default' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'address_id.required' => __('messages.address_id_required'),
            'address_id.exists' => __('messages.address_not_found'),
            'title.required' => __('messages.address_title_required'),
            'address.required' => __('messages.address_required'),
            'governorate_id.required' => __('messages.governorate_required'),
            'governorate_id.exists' => __('messages.governorate_exists'),
        ];
    }
}
