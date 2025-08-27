<?php

namespace Admin\Http\Requests\UserApplication;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAssessmentApplicationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'status'=>'nullable',
            'comment' => 'nullable'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
