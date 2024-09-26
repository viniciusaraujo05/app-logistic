<?php

namespace App\Http\Requests\Tokens;

use Illuminate\Foundation\Http\FormRequest;

class TokenRequest extends FormRequest
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
            'name' => 'required|string',
            'platform' => 'required|string',
            'url' => 'nullable|string',
            'status' => 'boolean',
            'token' => 'required',
            'type' => 'required|int',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required',
            'platform.required' => 'Platform is required',
            'token.required' => 'Token is required',
            'type.required' => 'Type is required',
        ];
    }
}
