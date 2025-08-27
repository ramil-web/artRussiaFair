<?php

namespace Admin\Classic\Http\Requests\ClassicEvent;

use Illuminate\Foundation\Http\FormRequest;

class ListClassicEventRequest extends FormRequest
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
            'filter.trashed'    => ['string'],
            'filter.event_type' => ['string'],
            'filter.name'       => ['string'],
            'filter.year'       => ['integer'],
            'filter.status'     => ['string'],
            'sort_by'           => 'nullable|string',
            'order_by'          => 'nullable|string',
            'per_page'          => 'nullable|integer',
            'page'              => 'nullable|integer',
        ];
    }
}
