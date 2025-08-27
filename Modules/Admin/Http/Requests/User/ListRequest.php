<?php

namespace Admin\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class ListRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'filter.trashed' => ['string'],
            'filter.ids' => ['array'],
            'filter.roles' => ['string'],
            'include' => ['string'],
            'per_page' => 'nullable|integer',
            'page' => 'nullable|integer',
        ];
    }
}
