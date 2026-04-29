<?php

namespace App\Http\Requests\API\GENERAL;

use Illuminate\Foundation\Http\FormRequest;

class SearchProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

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
            'search.string'   => __('messages.search_string'),
        ];
    }
}
