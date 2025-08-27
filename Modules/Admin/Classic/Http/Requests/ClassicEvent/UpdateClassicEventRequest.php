<?php

namespace Admin\Classic\Http\Requests\ClassicEvent;

use App\Rules\Classic\UpdateClassicMainEventRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateClassicEventRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'id'                           => 'required|exists:classic_events,id',
            'name'                         => 'nullable|array',
            'description'                  => 'nullable|array',
            'social_links'                 => 'array',
            'year'                         => 'nullable|numeric|min:2015',
            'start_date'                   => 'nullable|date',
            'end_date'                     => 'nullable|date',
            'status'                       => 'nullable|boolean',
            'slug'                         => "nullable|string|max:100|unique:classic_events,slug,$this->id",
            'sort_id'                      => 'nullable|integer',
            'start_accepting_applications' => 'nullable|date',
            'end_accepting_applications'   => 'nullable|date|after:start_accepting_applications',
            'place'                        => 'nullable|array',
            'event_type'                   => ['nullable', 'min:4', new UpdateClassicMainEventRule($this->id, $this->year)],
        ];
    }
}
