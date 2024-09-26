<?php

namespace App\Http\Requests\Configuration\Role;

use Illuminate\Foundation\Http\FormRequest;

class CreateRoleRequest extends FormRequest
{
    public function authorize(): true
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'permissions' => ['required'],
            'name' => ['required', 'string', 'max:255'],
        ];
    }
}
