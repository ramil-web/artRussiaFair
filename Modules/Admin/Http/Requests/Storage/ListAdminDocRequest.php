<?php

namespace Admin\Http\Requests\Storage;

use Illuminate\Foundation\Http\FormRequest;

class ListAdminDocRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'filter.event_id' => ['exists:events,id'],
            'per_page' => 'nullable|integer',
            'page' => 'nullable|integer',
            'sort_by' => 'nullable|string',
            'order_by' => 'nullable|string',
        ];
    }
}
