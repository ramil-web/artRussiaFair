<?php

namespace Admin\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
                'filter.id' => ['array'],
                'filter.user_application_id' => ['integer'],
                'filter.time_slot_start_id' => ['integer'],
                'filter.status' => ['string'],
                'filter.stand_area' => 'nullable|in:small,big',
        ];
    }
}
