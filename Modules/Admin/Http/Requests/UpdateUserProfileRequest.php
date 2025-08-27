<?php

namespace Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'locate'=>'string',
            'avatar'=>'string',
            'name' => 'string|min:3|max:255',
            'surname' => 'string|min:4|max:255',
            'phone' => 'string|min:3|max:255',
            'city' => 'string|min:4|max:255',
        ];
    }
}
