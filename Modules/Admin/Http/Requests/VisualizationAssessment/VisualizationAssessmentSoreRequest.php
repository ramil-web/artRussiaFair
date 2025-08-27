<?php

namespace Admin\Http\Requests\VisualizationAssessment;

use Illuminate\Foundation\Http\FormRequest;

class VisualizationAssessmentSoreRequest extends FormRequest
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
            'visualization_id'    => 'required|exists:visualizations,id',
            'status'              => 'required|string',
            'comment'             => 'nullable|string',
        ];
    }
}
