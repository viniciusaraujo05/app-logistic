<?php

namespace App\Http\Requests\Carriers;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTrackingRequest extends FormRequest
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
            'tracking' => 'string|max:255',
            'order_code' => 'string|max:255',
            'integration_id' => 'numeric',
            'order_id' => 'numeric',
            'volumes_total' => 'numeric',
            'receiver_name' => 'nullable|string',
        ];
    }
}
