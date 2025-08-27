<?php

namespace Admin\Http\Requests\SchemaOfStand;

use Illuminate\Foundation\Http\FormRequest;

class SchemaOfStandStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'url'  => 'required|string',
            'name' => 'required|string|'
        ];
    }
}
