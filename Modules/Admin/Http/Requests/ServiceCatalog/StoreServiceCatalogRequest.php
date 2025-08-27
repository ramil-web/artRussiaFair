<?php

namespace Admin\Http\Requests\ServiceCatalog;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceCatalogRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'sort_id' => 'required|integer',
            'image' => ['required', 'string'],
            'name' => 'required|array',
            'description' => 'required|array',
            'category' => 'required|array',
            'other' => 'nullable|array',
            'price' => 'required|integer',
            'locate' => 'nullable|string|in:ru,en'
        ];
    }
}
