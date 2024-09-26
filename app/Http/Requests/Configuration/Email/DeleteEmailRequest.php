<?php

namespace App\Http\Requests\Configuration\Email;

use Illuminate\Foundation\Http\FormRequest;

class DeleteEmailRequest extends FormRequest
{
    public function authorize(): true
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email_id' => 'required|integer|exists:emails,id',
        ];
    }
}
