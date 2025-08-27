<?php

namespace Lk\Http\Requests\Visualization;

use Illuminate\Foundation\Http\FormRequest;
use Lk\Rules\UserApplicationAccessRule;

class VisualisationListRequest extends FormRequest
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
            'id'             => ['required', new UserApplicationAccessRule($this->id)],
            'filter.trashed' => ['string'],
            'sort_by'        => 'nullable|string',
            'order_by'       => 'nullable|string',
            'per_page'       => 'nullable|integer',
            'page'           => 'nullable|integer',
        ];
    }
}
