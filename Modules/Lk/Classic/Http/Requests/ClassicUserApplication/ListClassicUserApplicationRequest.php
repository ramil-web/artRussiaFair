<?php

namespace Lk\Classic\Http\Requests\ClassicUserApplication;

use Illuminate\Foundation\Http\FormRequest;

class ListClassicUserApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
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
            'filter.id'     => 'nullable|exists:classic_user_applications',
            'filter.status' => 'nullable|string',
        ];
    }
}
