<?php

namespace Admin\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|array',
            'sort_id' => 'required|integer',
            'description' => 'required|array',
            'specifications' => 'required|array',
            'price' => 'required|int',
            'article' => 'required|string',
            'category_product_id' => 'int|exists:category_products,id',
            'locate' => 'string|in:ru,en',
            'image_path' => 'nullable|string'
        ];
    }
}
