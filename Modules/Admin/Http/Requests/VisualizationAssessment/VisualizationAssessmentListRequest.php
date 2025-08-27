<?php

namespace Admin\Http\Requests\VisualizationAssessment;

use Illuminate\Foundation\Http\FormRequest;

class VisualizationAssessmentListRequest extends FormRequest
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
            'user_application_id' => 'required|exists:user_applications,id',
            'filter.id'           => ['array'],
            'filter.trashed'      => ['string'],
            'filter.name'         => ['string'],
            'sort_by'             => 'nullable|string',
            'order_by'            => 'nullable|string',
            'per_page'            => 'nullable|integer',
            'page'                => 'nullable|integer',
        ];
    }
}
