<?php

namespace App\Http\Requests\Carriers;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
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
            'label_type' => 'required|string',
            'order_code' => 'required|string',
            'collection_date' => 'date_format:d-m-Y',
            'service_type_id' => 'required|string',
            'number_of_volumes' => 'required|integer|min:1',
            'total_weight_of_volumes' => 'required|numeric|min:0',
            'amount' => 'required|numeric|min:0',
            'name' => 'required|string|max:255',
            'contact_name' => 'required|string|max:255',
            'street_1' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telephone' => 'required|string|max:20',
            'info_adicional' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'label_type.required' => 'O campo label_type e obrigatório',
            'order_code.required' => 'O campo order_code e obrigatório',
            'service_type_id.required' => 'O campo service_type_id e obrigatório',
            'number_of_volumes.required' => 'O campo number_of_volumes e obrigatório',
            'total_weight_of_volumes.required' => 'O campo total_weight_of_volumes e obrigatório',
            'amount.required' => 'O campo amount e obrigatório',
            'name.required' => 'O campo name e obrigatório',
            'contact_name.required' => 'O campo contact_name e obrigatório',
            'street_1.required' => 'O campo street_1 e obrigatório',
            'postal_code.required' => 'O campo postal_code e obrigatório',
            'city.required' => 'O campo city e obrigatório',
            'country.required' => 'O campo country e obrigatório',
            'email.required' => 'O campo email e obrigatório',
            'telephone.required' => 'O campo telephone e obrigatório',
            'info_adicional.required' => 'O campo info_adicional e obrigatório',
        ];
    }
}
