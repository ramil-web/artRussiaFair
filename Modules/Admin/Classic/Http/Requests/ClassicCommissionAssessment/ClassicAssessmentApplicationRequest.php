<?php

namespace Admin\Classic\Http\Requests\ClassicCommissionAssessment;

use Illuminate\Foundation\Http\FormRequest;

class ClassicAssessmentApplicationRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status'  => 'required',
            'comment' => ['required']
        ];
    }
}
