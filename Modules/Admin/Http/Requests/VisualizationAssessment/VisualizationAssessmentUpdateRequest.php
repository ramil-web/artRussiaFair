<?php

namespace Admin\Http\Requests\VisualizationAssessment;

use Illuminate\Foundation\Http\FormRequest;

class VisualizationAssessmentUpdateRequest extends FormRequest
{


    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'id'                  => 'required|exists:visualization_assessments,id',
            'user_application_id' => 'required|exists:user_applications,id',
            'status'              => 'nullable',
            'comment'             => 'nullable'
        ];
    }
}
