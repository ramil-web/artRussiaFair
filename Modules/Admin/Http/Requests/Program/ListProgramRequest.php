<?php

namespace Admin\Http\Requests\Program;

use Illuminate\Foundation\Http\FormRequest;

class ListProgramRequest extends FormRequest
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
            'filter.id'             => ['array'],
            'filter.speaker_id'     => 'nullable|integer',
            'filter.name'           => ['string'],
            'filter.trashed'        => ['string'],
            'filter.category'       => 'nullable|string',
            'filter.event_type'     => ['string'],
            'filter.program_format' => ['string'],
            'filter.moderator_name' => ['string'],
            'sort_by'               => 'nullable|string',
            'order_by'              => 'nullable|string',
            'per_page'              => 'nullable|integer',
            'page'                  => 'nullable|integer',
        ];
    }
}
