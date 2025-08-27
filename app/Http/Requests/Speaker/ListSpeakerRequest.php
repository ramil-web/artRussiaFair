<?php

namespace App\Http\Requests\Speaker;

use Illuminate\Foundation\Http\FormRequest;

class ListSpeakerRequest extends FormRequest
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
            'filter.year'       => ['nullable', 'digits:4', 'integer', 'min:2015', 'max:' . (date('Y') + 2)],
            'filter.event_type' => ['string'],
            'filter.event_id'   => ['integer'],
            'filter.date'       => ['string'],
            'filter.category'   => 'nullable|string',
            'sort_by'           => 'nullable|string',
            'order_by'          => 'nullable|string',
            'per_page'          => 'nullable|integer',
            'page'              => 'nullable|integer',
        ];
    }
}
