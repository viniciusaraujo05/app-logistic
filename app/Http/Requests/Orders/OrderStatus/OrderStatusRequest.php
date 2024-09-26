<?php

namespace App\Http\Requests\Orders\OrderStatus;

use Illuminate\Foundation\Http\FormRequest;

class OrderStatusRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'order_status_id' => ['required', 'integer', 'exists:order_statuses,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'order_status_id.required' => 'O ID do status é obrigatório.',
            'order_status_id.integer' => 'O ID do status deve ser um número inteiro.',
            'order_status_id.exists' => 'O ID do status fornecido não existe.',
        ];
    }
}
