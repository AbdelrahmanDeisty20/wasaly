<?php

namespace App\Http\Requests\API\GENERAL;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class BrandRequest extends FormRequest
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
            'brand_id'=>'required|exists:brands,id'
        ];
    }
    public function messages(): array
    {
        return [
            'brand_id.required'=>__('messages.brand_id_required'),
            'brand_id.exists'=>__('messages.brand_not_found')
        ];
    }
}
