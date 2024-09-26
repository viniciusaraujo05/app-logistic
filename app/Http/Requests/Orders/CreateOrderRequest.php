<?php

namespace App\Http\Requests\Orders;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
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
            'order_code' => 'required|string|max:255',
            'customer_name' => 'required|string|max:255',
            'shipping_address' => 'required|array',
            'email' => 'required|email|max:255',
            'phone' => 'required|numeric',
            'notes' => 'nullable|string',
            'status' => 'required|string|max:255',
            'weight' => 'required|numeric',
            'responsible' => 'string|max:255',
            'price' => 'required|numeric',
            'products' => 'required|array',
        ];
    }
}
