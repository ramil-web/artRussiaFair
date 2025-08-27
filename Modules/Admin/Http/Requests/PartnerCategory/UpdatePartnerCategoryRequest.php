<?php

namespace Admin\Http\Requests\PartnerCategory;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePartnerCategoryRequest extends FormRequest
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
            'name' => 'nullable|array',
            'locate' => 'nullable|string|in:ru,en',
            'sort_id' => 'nullable|integer'
        ];
    }
}
