<?php

namespace Admin\Http\Requests\ServiceCatalog;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'sort_id' => 'nullable|integer',
            'image' => ['nullable', 'string'],
            'name' => 'nullable|array',
            'description' => 'nullable|array',
            'category' => 'nullable|array',
            'other' => 'nullable|array',
            'price' => 'nullable|integer',
            'locate' => 'nullable|string|in:ru,en'
        ];
    }
}
