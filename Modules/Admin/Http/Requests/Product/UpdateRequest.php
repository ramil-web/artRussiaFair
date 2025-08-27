<?php

namespace Admin\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'nullable|array',
            'description' => 'nullable|array',
            'specifications' => 'nullable|array',
            'price' => 'nullable|int',
            'article' => 'nullable|string',
            'sort_id' => 'nullable|integer',
            'category_product_id' => 'nullable|int|exists:category_products,id',
            'locate' => 'nullable|string|in:ru,en'
        ];
    }
}
