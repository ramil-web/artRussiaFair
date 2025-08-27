<?php

namespace Admin\Http\Requests\Storage;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorageRequest extends FormRequest
{
    const STORAGE_TYPE = [
        'stands',
        'product_images',
        'avatar',
        'speaker',
        'project_team',
        'curator',
        'artist',
        'sculptor',
        'photographer',
        'gallery',
        'user_data',
        'schema-of-stand'
    ];

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
            'file' => 'required|array',
            'type' => ['required', Rule::in(self::STORAGE_TYPE)]
        ];
    }
}
