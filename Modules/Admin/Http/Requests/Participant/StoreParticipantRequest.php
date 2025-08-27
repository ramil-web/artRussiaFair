<?php

namespace Admin\Http\Requests\Participant;

use Illuminate\Foundation\Http\FormRequest;

class StoreParticipantRequest extends FormRequest
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
            'sort_id'     => 'required|integer',
            'slug'        => 'required|string|max:100|unique:participants,slug',
            'event_id'    => 'required|array',
            'stand_id'    => 'nullable|string',
            'name'        => 'required|array',
            'description' => 'required|array',
            'image'       => 'required|string',
            'images'      => ['nullable', 'array'],
            'locate'      => 'nullable|string|in:ru,en'
        ];
    }
}
