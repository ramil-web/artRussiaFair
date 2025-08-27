<?php

namespace Admin\Http\Requests\Storage;

use Illuminate\Foundation\Http\FormRequest;
use ProtoneMedia\LaravelMixins\Request\ConvertsBase64ToFiles;

class UpdateAdminDocsRequest extends FormRequest
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
            'id' => 'required|exists:admin_documents,id',
            'file' => 'nullable|mimes:pdf',
            'name' => 'nullable|string',
            'event_id' => 'nullable|integer|exists:events,id'
        ];
    }
}
