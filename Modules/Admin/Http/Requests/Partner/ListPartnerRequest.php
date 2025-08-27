<?php

namespace Admin\Http\Requests\Partner;

use Illuminate\Foundation\Http\FormRequest;

class ListPartnerRequest extends FormRequest
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
            'filter.id'                  => ['array'],
            'filter.trashed'             => ['string'],
            'filter.event_id'            => ['integer'],
            'filter.name'                => ['string'],
            'filter.important'           => 'string',
            'filter.category'            => 'nullable|string',
            'filter.partner_category_id' => ['integer'],
            'sort_by'                    => 'nullable|string',
            'order_by'                   => 'nullable|string',
            'per_page'                   => 'nullable|integer',
            'page'                       => 'nullable|integer',
        ];
    }
}
