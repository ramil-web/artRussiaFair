<?php

namespace Lk\Http\Requests\MyDocuments;

use Illuminate\Foundation\Http\FormRequest;

class MyDocumentsDeleteFileRequest extends FormRequest
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
            'id'   => 'required|exists:my_documents,id',
            'url'  => 'required|string|min:3|max:100',
            'type' => 'required|string|min:3|max:100',
            'name' => 'required|string|min:3|max:100',
        ];
    }
}
