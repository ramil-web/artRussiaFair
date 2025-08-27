<?php

namespace Admin\Classic\Http\Requests\ClassicUserApplication;

use Illuminate\Foundation\Http\FormRequest;

class ListClassicUserApplicationRequest extends FormRequest
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
            'filter.id' => ['array'],
            'filter.type' => ['string'],
            'filter.status' => ['string'],
            'include' => ['string'],
            'sort' => 'nullable|string',
            'per_page' => 'nullable|integer',
            'page' => 'nullable|integer',
        ];
    }
}
