<?php

namespace App\Http\Requests\Orders\OrderStatus;

use App\Enums\Order\OrderStatusTypeEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateOrderStatusRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'order_status_name' => 'required|string',
            'order_status_type' => 'required|in:'.OrderStatusTypeEnum::validationString(),
        ];
    }

    public function messages(): array
    {
        return [
            'order_status_type.required' => 'Status is required',
            'order_status_type.in' => 'Invalid status type',
            'order_status_name.required' => 'Status name is required',
            'order_status_name.string' => 'Status name must be string',
        ];
    }
}
