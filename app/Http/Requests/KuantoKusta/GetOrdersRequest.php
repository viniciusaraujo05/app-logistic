<?php

namespace App\Http\Requests\KuantoKusta;

use App\Enums\KuantoKusta\OrderStateEnum;
use Illuminate\Foundation\Http\FormRequest;

class GetOrdersRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'createdAt' => 'nullable|date_format:Y-m-d H:i:s',
            'createdAt_gte' => 'nullable|date_format:Y-m-d H:i:s',
            'createdAt_gt' => 'nullable|date_format:Y-m-d H:i:s',
            'createdAt_lte' => 'nullable|date_format:Y-m-d H:i:s',
            'createdAt_lt' => 'nullable|date_format:Y-m-d H:i:s',
            'orderState' => ['nullable', 'string', 'in:'.implode(',', OrderStateEnum::getAll())],
            'page' => 'nullable|integer|min:1',
            'maxResultsPerPage' => 'nullable|integer|min:1|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'createdAt_gt.required' => 'The createdAt_gt is required',
            'page.required' => 'The page is required',
            'maxResultsPerPage.required' => 'The maxResultsPerPage is required',
            'maxResultsPerPage.max' => 'The maxResultsPerPage must be less than or equal to 100',
            'createdAt.date_format' => 'The :attribute must be in the format Y-m-d H:i:s',
            'createdAt_gte.date_format' => 'The :attribute must be in the format Y-m-d H:i:s',
            'createdAt_gt.date_format' => 'The :attribute must be in the format Y-m-d H:i:s',
            'createdAt_lte.date_format' => 'The :attribute must be in the format Y-m-d H:i:s',
            'createdAt_lt.date_format' => 'The :attribute must be in the format Y-m-d H:i:s',
            'orderState.in' => 'The orderState must be one of: '.implode(',', OrderStateEnum::getAll()),
        ];
    }
}
