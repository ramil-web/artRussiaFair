<?php

namespace Admin\Http\Requests\CategoryProduct;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|array|unique:category_products,name->ru',
            'sort_id' => 'required|integer',
            'locate'=>'string'
        ];
    }
}
