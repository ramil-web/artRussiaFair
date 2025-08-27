<?php

namespace Admin\Classic\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClassicAssessmentAppRequest extends FormRequest
{
    /***'
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
            'status'  => 'nullable',
            'comment' => 'nullable'
        ];
    }
}
