<?php

namespace Admin\Http\Requests\Partner;

use Illuminate\Foundation\Http\FormRequest;

class StorePartnerRequest extends FormRequest
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
            'sort_id' => 'required|integer',
            'event_id' => 'required|array',
            'partner_category_id' => 'required|int|exists:partner_categories,id',
            'important' => 'nullable|boolean',
            'name' => 'required|array',
            'link' => 'nullable|string',
            'image' => 'required|string',
            'locate' => 'nullable|string|in:ru,en'
        ];
    }
}
