<?php

namespace App\Http\Requests\Configuration\Email;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmailRequest extends FormRequest
{
    public function authorize(): true
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email_id' => 'required|integer|exists:emails,id',
            'name' => 'required|string|max:255',
            'html_content' => 'required|string',
            'design' => 'nullable|string',
            'status_id' => 'nullable|integer|exists:order_statuses,id',
        ];
    }
}
