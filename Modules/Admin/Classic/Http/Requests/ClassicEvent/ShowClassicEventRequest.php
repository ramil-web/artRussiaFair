<?php

namespace Admin\Classic\Http\Requests\ClassicEvent;

use Illuminate\Foundation\Http\FormRequest;

class ShowClassicEventRequest extends FormRequest
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
            'id' => 'required|exists:classic_events,id',
        ];
    }
}
