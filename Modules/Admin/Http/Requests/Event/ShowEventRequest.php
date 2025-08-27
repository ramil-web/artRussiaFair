<?php

namespace Admin\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class ShowEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|exists:events,id',
        ];
    }
}
