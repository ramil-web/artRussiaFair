<?php

namespace Admin\Classic\Http\Requests\ClassicEvent;

use App\Rules\Classic\MainClassicEventRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreClassicEventRequest extends FormRequest
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
            'name'                         => 'required|array',
            'description'                  => 'required|array',
            'social_links'                 => 'array',
            'year'                         => 'required|numeric|min:2015',
            'start_date'                   => 'required|date',
            'end_date'                     => 'required|date',
            'status'                       => 'required|boolean',
            'slug'                         => 'required|string|max:100|unique:classic_events,slug',
            'sort_id'                      => 'required|integer',
            'start_accepting_applications' => 'required|date',
            'end_accepting_applications'   => 'required|date|after:start_accepting_applications',
            'place'                        => 'required|array',
            'event_type'                   => ['required', 'min:4', new MainClassicEventRule($this->year)],
        ];
    }

}
