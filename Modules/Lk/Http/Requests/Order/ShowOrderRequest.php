<?php

namespace Lk\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class ShowOrderRequest extends FormRequest
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
            'filter.id'                  => 'nullable|integer',
            'filter.user_application_id' => 'nullable|integer',
            'filter.status'              => 'nullable|in:pending,processing,completed,cancelled',
            'filter.stand_area'          => 'nullable|in:small,big',
        ];
    }
}
