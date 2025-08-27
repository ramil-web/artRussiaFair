<?php

namespace Lk\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserProfileRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'locate'  => 'string',
            'avatar'  => 'nullable|string',
            'name'    => 'required|max:255',
            'surname' => 'required|max:255',
            'phone'   => 'required|max:255',
            'city'    => 'required|max:255',

        ];
    }
}
