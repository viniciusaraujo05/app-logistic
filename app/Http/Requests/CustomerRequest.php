<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
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
            'company_name' => 'required|max:255',
            'responsible_name' => 'required|max:255',
            'nif' => 'required|max:14',
            'email' => 'required|email|unique:customers,email',
            'phone' => 'required|max:15',
        ];
    }

    public function messages()
    {
        return [
            'company_name.required' => 'The company name is required.',
            'responsible_name.required' => 'The responsible name is required.',
            'nif.unique' => 'The NIF has already been taken.',
            'email.unique' => 'The email has already been taken.',
            'phone.required' => 'The phone number is required.',
        ];
    }
}
