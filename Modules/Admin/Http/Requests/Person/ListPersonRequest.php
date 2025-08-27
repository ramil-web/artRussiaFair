<?php

namespace Admin\Http\Requests\Person;

use Illuminate\Foundation\Http\FormRequest;

class ListPersonRequest extends FormRequest
{

    /** Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
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
            'filter.id' => 'nullable|integer',
            'filter.user_application_id' => 'nullable|integer',
        ];
    }
}
