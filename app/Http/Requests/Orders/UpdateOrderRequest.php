<?php

namespace App\Http\Requests\Orders;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'order_code' => 'string|max:255',
            'customer_name' => 'string|max:255',
            'shipping_address' => 'array',
            'email' => 'email|max:255',
            'phone' => 'numeric',
            'notes' => 'nullable|string',
            'status' => 'string|max:255',
            'responsible' => 'nullable|string|max:255',
            'weight' => 'numeric',
            'price' => 'numeric',
            'products' => 'array',
        ];
    }
}
