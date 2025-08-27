<?php

namespace Admin\Http\Requests\Program;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProgramRequest extends FormRequest
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
            'event_id'              => 'int|exists:events,id',
            'speaker_id'            => 'nullable|array',
            'partners_id'           => 'nullable|array',
            'name'                  => 'nullable|array',
            'moderator_name'        => 'nullable|array',
            'moderator_description' => 'nullable|array',
            'start_time'            => 'nullable|string',
            'end_time'              => 'nullable|string',
            'date'                  => 'nullable|date',
            'locate'                => 'nullable|string|in:ru,en',
            'program_format'        => 'nullable|string|in:lecture,discussion,workshop,interview,performance',
            'description'           => 'nullable|array',
        ];
    }
}
