<?php

namespace Admin\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class ListEventRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'filter.category' => 'required|string',
            'filter.name' => 'nullable|string',
            'filter.trashed' => 'nullable|string',
            'filter.type' =>'nullable|string',
            'filter.year' => 'nullable|string',
            'filter.status' => 'nullable',
            'sort_by' => 'nullable|string',
            'order_by' => 'nullable|string',
            'per_page' => 'nullable|integer',
            'page' => 'nullable|integer',
        ];
    }
}
