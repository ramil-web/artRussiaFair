<?php

namespace Admin\Http\Requests\Speaker;

use Illuminate\Foundation\Http\FormRequest;

class StoreSpeakerRequest extends FormRequest
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
            'sort_id'          => 'required|integer',
            'event_id'         => 'required|array',
            'name'             => 'required|array',
            'description'      => 'required|array',
            'image'            => 'nullable|string',
            'images'           => ['nullable', 'array'],
            'locate'           => 'nullable|string|in:ru,en',
            'position'         => 'nullable|array',
            'full_description' => 'required|array',
        ];
    }
}
