<?php

namespace App\Http\Requests\API\GENERAL;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CenterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'governorate_id' => 'required|exists:governorates,id',
        ];
    }

    public function messages(): array
    {
        return [
            'governorate_id.required' => __('messages.governorate_required'),
            'governorate_id.exists' => __('messages.governorate_exists'),
        ];
    }
}
