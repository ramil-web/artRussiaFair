<?php

namespace Admin\Http\Requests\Chat;

use Illuminate\Foundation\Http\FormRequest;

class NewMessageRequest extends FormRequest
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
     * @return array
     */
    public function rules(): array
    {
        return [
            'file' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id',
            'file_name' => 'nullable|string',
            'message' => 'nullable|string'
        ];
    }
}
