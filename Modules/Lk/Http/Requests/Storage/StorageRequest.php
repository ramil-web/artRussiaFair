<?php

namespace Lk\Http\Requests\Storage;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use ProtoneMedia\LaravelMixins\Request\ConvertsBase64ToFiles;

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
        "visualization",
        "information_for_placement",
        "user_data",
        "my_documents",
        "classic_stands",
        "classic_product_image",
        "classic_avatar",
        "classic_speakers",
        'classic_project_team',
        'classic_curator',
        'classic_artist',
        'classic_sculptor',
        'classic_photographer',
        'classic_gallery',
        "classic_visualization",
        "classic_information_for_placement",
        "classic_user_data",
        "classic_my_documents",
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

    use ConvertsBase64ToFiles;

    protected function base64FileKeys(): array
    {
        return [
            'file' => 'file',
        ];
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
