<?php

namespace Admin\Http\Requests\Participant;

use Illuminate\Foundation\Http\FormRequest;

class UpdateParticipantRequest extends FormRequest
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
            'sort_id' => 'nullable|integer',
            'type' => 'nullable|string',
            'slug' =>  "nullable|string|unique:participants,slug,$this->id",
            'event_id' => 'nullable|array',
            'stand_id' => 'nullable|string',
            'name' => 'nullable|array',
            'description' => 'nullable|array',
            'image' => 'nullable|string',
            'images' => 'nullable|array',
            'locate' => 'nullable|string|in:ru,en'
        ];
    }
}
