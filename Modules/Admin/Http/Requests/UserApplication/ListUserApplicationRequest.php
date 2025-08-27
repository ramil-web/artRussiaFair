<?php

namespace Admin\Http\Requests\UserApplication;

use Illuminate\Foundation\Http\FormRequest;

class ListUserApplicationRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'filter.category'      => ['required', 'string'],
            'filter.id'            => ['array'],
            'filter.type'          => ['string'],
            'filter.visualization' => 'nullable|in:with,without,only',
            'filter.status'        => ['string'],
            'include'              => ['string'],
            'sort'                 => 'nullable|string',
            'per_page'             => 'nullable|integer',
            'page'                 => 'nullable|integer',
        ];
    }
}
