<?php

namespace Admin\Classic\Http\Requests\ClassicUserApplication;

use Illuminate\Foundation\Http\FormRequest;

class ShowClassicUserApplicationRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'id' => 'required|exists:classic_user_applications,id'
        ];
    }
}
