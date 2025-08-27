<?php

namespace Admin\Http\Requests\Partner;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePartnerRequest extends FormRequest
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
            'sort_id' => 'nullable|integer',
            'event_id' => 'nullable|array',
            'important' => 'nullable|boolean',
            'name' => 'nullable|array',
            'link' => 'nullable|string|max:255',
            'partner_category_id' => 'required|integer',
            'image' => 'nullable|string',
            'locate' => 'nullable|string|in:ru,en'
        ];
    }
}
