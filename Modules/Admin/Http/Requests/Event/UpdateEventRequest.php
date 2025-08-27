<?php

namespace Admin\Http\Requests\Event;

use App\Rules\UpdateMainEventRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'nullable|array',
            'description' => 'nullable|array',
            'social_links' => 'array',
            'year' => 'nullable|numeric|min:2015',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'nullable|boolean',
            'slug' => "nullable|string|max:100|unique:events,slug,$this->id",
            'sort_id' => 'nullable|integer',
            'start_accepting_applications' =>'nullable|date',
            'end_accepting_applications' =>  'nullable|date|after:start_accepting_applications',
            'place' => 'nullable|array',
            'category' => "required|exists:events,category",
            'event_type' =>  ['nullable','min:4',new UpdateMainEventRule($this->id, $this->year, $this->category)],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
