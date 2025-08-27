<?php

namespace Admin\Http\Requests\Storage;

use Illuminate\Foundation\Http\FormRequest;

class DeleteAdminDocRequest extends FormRequest
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
            'id' => 'required|exists:admin_documents,id',
        ];
    }
}
