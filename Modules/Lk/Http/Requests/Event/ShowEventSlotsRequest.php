<?php

namespace Lk\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class ShowEventSlotsRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'id' => 'required|exists:events,id',
        ];
    }
}
