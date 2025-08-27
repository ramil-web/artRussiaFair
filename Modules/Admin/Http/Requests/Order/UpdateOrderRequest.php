<?php

namespace Admin\Http\Requests\Order;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
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
            'time_slot_start_id' => 'nullable|exists:time_slot_start,id',
            'time_slot_end_id' => 'nullable|exists:time_slot_start,id',
            'stand_area' => 'nullable|in:small,big',
            'status' => 'nullable|string'
        ];
    }
}
