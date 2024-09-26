<?php

namespace App\Http\Requests\Configuration\Role;

use Illuminate\Foundation\Http\FormRequest;

class DeleteRoleRequest extends FormRequest
{
    public function authorize(): true
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => [
                'required',
                'exists:roles,id',
            ],
        ];
    }
}
