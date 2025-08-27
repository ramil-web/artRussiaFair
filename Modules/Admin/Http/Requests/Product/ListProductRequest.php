<?php

namespace Admin\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class ListProductRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'filter.id' => ['array'],
            'filter.name' => ['string'],
            'filter.trashed' => ['string'],
            'filter.article' => ['string'],
            'filter.category_product_id' => ['integer'],
            'sort_by' => 'nullable|string',
            'order_by' => 'nullable|string',
            'per_page' => 'nullable|integer',
            'page' => 'nullable|integer',
        ];
    }
}
