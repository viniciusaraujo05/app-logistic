<?php

namespace App\Http\Requests\Tokens;

use Illuminate\Foundation\Http\FormRequest;

class IndexTokensRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'integration_type' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'integration_type.required' => 'Integration is required',
        ];
    }
}
