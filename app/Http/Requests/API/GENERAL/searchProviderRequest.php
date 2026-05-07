<?php

namespace App\Http\Requests\API\GENERAL;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class searchProviderRequest extends FormRequest
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
            'search' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'search.required' => __('messages.search_required'),
            'search.string' => __('messages.search_string'),
            'search.max' => __('messages.search_max'),
        ];
    }
}
