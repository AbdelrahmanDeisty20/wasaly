<?php

namespace App\Http\Requests\API\GENERAL;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SearchOrderRequest extends FormRequest
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
            'search_term' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'search_term.required' => __('messages.search_required'),
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Http\Exceptions\HttpResponseException(response()->json([
            'status' => false,
            'message' => $validator->errors()->first(),
            'data' => [],
        ], 422));
    }
}
