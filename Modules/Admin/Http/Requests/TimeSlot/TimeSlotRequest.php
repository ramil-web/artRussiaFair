<?php


namespace Admin\Http\Requests\TimeSlot;


use Illuminate\Foundation\Http\FormRequest;

class TimeSlotRequest extends FormRequest
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
            'begin' => 'required|string',
            'end' => 'required|string',
            'interval' => 'required|integer',
            'action' => 'required|in:check_in,exit',
            'event_id' => 'required|integer'
        ];
    }
}
