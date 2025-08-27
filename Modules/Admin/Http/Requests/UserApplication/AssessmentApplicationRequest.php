<?php

namespace Admin\Http\Requests\UserApplication;

use Illuminate\Foundation\Http\FormRequest;

class AssessmentApplicationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'status'=>'required',
            'comment' => ['required']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
