<?php

namespace Admin\Http\Requests\Event;

use App\Rules\MainEventRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

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
            'slug'                         => 'required|string|max:100|unique:events,slug',
            'sort_id'                      => 'required|integer',
            'start_accepting_applications' => 'required|date',
            'end_accepting_applications'   => 'required|date|after:start_accepting_applications',
            'place'                        => 'required|array',
            'event_type'                   => ['required', 'min:4', new MainEventRule($this->year, $this->category)],
            'category'                     => 'required|string'
        ];
    }
}
