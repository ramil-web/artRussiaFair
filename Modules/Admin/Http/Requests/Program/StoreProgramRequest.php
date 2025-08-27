<?php

namespace Admin\Http\Requests\Program;

use Illuminate\Foundation\Http\FormRequest;

class StoreProgramRequest extends FormRequest
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
            'partners_id'           => 'nullable|array',
            'speaker_id'            => 'nullable|array',
            'event_id'              => 'int|exists:events,id',
            'start_time'            => 'required|string|max:255',
            'end_time'              => 'required|string|max:255',
            'date'                  => 'required|date',
            'name'                  => 'required|array',
            'moderator_name'        => 'required|array',
            'moderator_description' => 'required|array',
            'locate'                => 'nullable|string|in:ru,en',
            'program_format'        => 'nullable|string|in:lecture,discussion,workshop,interview,performance',
            'description'           => 'nullable|array',
        ];
    }

}
