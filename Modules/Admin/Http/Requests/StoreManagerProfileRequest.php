<?php

namespace Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreManagerProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
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
            'name' => 'required|string|min:3|max:255',
            'surname' => 'required|string|min:4|max:255',
            'phone' => 'required|string|min:3|max:255',
            'city' => 'required|string|min:4|max:255',
        ];
    }
}
