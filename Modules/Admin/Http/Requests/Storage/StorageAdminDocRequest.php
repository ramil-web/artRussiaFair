<?php

namespace Admin\Http\Requests\Storage;

use Illuminate\Foundation\Http\FormRequest;
use ProtoneMedia\LaravelMixins\Request\ConvertsBase64ToFiles;

class StorageAdminDocRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    use ConvertsBase64ToFiles;

    protected function base64FileKeys(): array
    {
        return [
            'file' => 'file',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'file' => 'required|mimes:pdf',
            'name' => 'required|string|unique:admin_documents,name',
            'event_id' => 'required|integer|exists:events,id'
        ];
    }
}
