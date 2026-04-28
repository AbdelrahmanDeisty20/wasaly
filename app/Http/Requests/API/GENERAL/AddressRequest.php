<?php

namespace App\Http\Requests\API\GENERAL;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'address' => 'required|string',
            'governorate_id' => 'required|exists:governorates,id',
            'center_id' => 'required|exists:centers,id,governorate_id,' . $this->governorate_id,
            'is_default' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => __('messages.address_title_required'),
            'address.required' => __('messages.address_required'),
            'governorate_id.required' => __('messages.governorate_required'),
            'governorate_id.exists' => __('messages.governorate_exists'),
            'center_id.required' => __('messages.center_required'),
            'center_id.exists' => __('messages.center_exists'),
        ];
    }
}
